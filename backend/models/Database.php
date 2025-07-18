<?php
class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function query($sql, $params = [])
    {
        try {
            // Valide les paramètres avant exécution pour éviter les erreurs
            $this->validateSqlParams($sql, $params);

            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erreur PDO dans query: " . $e->getMessage() . " - SQL: " . $sql);

            if (defined('DEBUG') && DEBUG === true) {
                echo "<div style='color:red;border:1px solid red;padding:10px;'>";
                echo "<h4>Erreur SQL</h4>";
                echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<p>SQL: <code>" . htmlspecialchars($sql) . "</code></p>";
                echo "<pre>" . print_r($params, true) . "</pre>";
                echo "</div>";
                exit;
            } else {
                throw $e; // Relance pour traitement global
            }
        }
    }

    public function findOne($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    public function findAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function insert($table, $data)
    {
        try {
            // Filtre les données pour éviter les conflits AUTO_INCREMENT
            $filteredData = [];
            foreach ($data as $key => $value) {
                // Ignore les ID vides pour laisser AUTO_INCREMENT fonctionner
                if ($key === 'id' && (is_null($value) || $value === 0 || $value === '' || $value === '0')) {
                    continue;
                }
                $filteredData[$key] = $value;
            }

            $columns = implode(", ", array_keys($filteredData));
            $values = ":" . implode(", :", array_keys($filteredData));

            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($filteredData);

            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur lors de l'insertion dans $table: " . $e->getMessage());
            if (defined('DEBUG') && DEBUG === true) {
                echo "Erreur SQL: " . $e->getMessage() . "<br>";
                echo "Table: $table<br>";
                echo "Données: " . print_r($filteredData, true);
            }
            return false;
        }
    }

    public function update($table, $data, $where, $whereParams = [])
    {
        if (empty($data)) {
            return 0; // Rien à mettre à jour
        }

        $sets = [];
        $params = [];

        // Prépare les paramètres SET avec préfixe pour éviter les conflits
        foreach ($data as $key => $value) {
            $paramKey = "set_" . $key; // Préfixe pour éviter conflits WHERE
            $sets[] = "$key = :$paramKey";
            $params[$paramKey] = $value;
        }

        // Fusionne avec les paramètres WHERE
        foreach ($whereParams as $key => $value) {
            $params[$key] = $value;
        }

        $sql = "UPDATE {$table} SET " . implode(", ", $sets) . " WHERE {$where}";

        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour: " . $e->getMessage());
            if (defined('DEBUG') && DEBUG === true) {
                echo "Erreur: " . $e->getMessage();
            }
            return 0;
        }
    }

    public function delete($table, $where, $params = [])
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

    // Exécute une requête avec gestion d'erreur améliorée
    public function safeQuery($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erreur SQL: " . $e->getMessage() . " | Requête: $sql");

            // Affiche les détails en mode debug
            if (defined('DEBUG') && DEBUG === true) {
                echo "Erreur SQL: " . $e->getMessage() . "<br>";
                echo "Requête: $sql<br>";
                echo "Paramètres: " . print_r($params, true);
                exit;
            }

            return false;
        }
    }

    // Version sécurisée de findOne
    public function safeFindOne($sql, $params = [])
    {
        $stmt = $this->safeQuery($sql, $params);
        return $stmt ? $stmt->fetch() : false;
    }

    // Version sécurisée de findAll
    public function safeFindAll($sql, $params = [])
    {
        try {
            $stmt = $this->safeQuery($sql, $params);
            return $stmt ? $stmt->fetchAll() : [];
        } catch (PDOException $e) {
            error_log("Erreur SQL dans safeFindAll: " . $e->getMessage() . " - SQL: " . $sql);

            if (defined('DEBUG') && DEBUG === true) {
                echo "<div style='color:red;border:1px solid red;padding:10px;'>";
                echo "<h4>Erreur SQL</h4>";
                echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<p>SQL: <code>" . htmlspecialchars($sql) . "</code></p>";
                echo "<pre>" . print_r($params, true) . "</pre>";
                echo "</div>";
            }

            return [];
        }
    }

    // Vérifie que tous les paramètres SQL nommés sont fournis
    public function validateSqlParams($sql, $params = [])
    {
        $namedParams = [];
        // Trouve tous les paramètres nommés (:param)
        preg_match_all('/:([a-zA-Z0-9_]+)/', $sql, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $param) {
                $namedParams[$param] = true;
            }
        }

        // Vérifie que tous les paramètres sont présents
        foreach (array_keys($namedParams) as $param) {
            if (!array_key_exists($param, $params)) {
                if (defined('DEBUG') && DEBUG === true) {
                    error_log("Paramètre manquant dans la requête SQL: " . $param);
                    echo "<div style='color:red;border:1px solid red;padding:10px;'>";
                    echo "<h4>Paramètre SQL manquant</h4>";
                    echo "<p>Le paramètre <code>:" . htmlspecialchars($param) . "</code> est utilisé dans la requête mais n'est pas fourni.</p>";
                    echo "<p>SQL: <code>" . htmlspecialchars($sql) . "</code></p>";
                    echo "<p>Paramètres fournis: <pre>" . print_r($params, true) . "</pre></p>";
                    echo "</div>";
                }
                return false;
            }
        }

        return true;
    }

    // Commence une transaction
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    // Valide une transaction
    public function commit()
    {
        return $this->connection->commit();
    }

    // Annule une transaction
    public function rollBack()
    {
        return $this->connection->rollBack();
    }

    // Vérifie si une transaction est en cours
    public function inTransaction()
    {
        return $this->connection->inTransaction();
    }
}
