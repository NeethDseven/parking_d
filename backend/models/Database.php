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
            // Valider les paramètres avant d'exécuter la requête
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
                throw $e; // Relancer l'exception pour un traitement plus global
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
        $columns = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);

        return $this->connection->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = [])
    {
        if (empty($data)) {
            return 0; // Rien à mettre à jour
        }

        $sets = [];
        $params = [];

        // Préparer les paramètres de SET
        foreach ($data as $key => $value) {
            $paramKey = "set_" . $key; // Préfixe pour éviter les conflits avec les paramètres WHERE
            $sets[] = "$key = :$paramKey";
            $params[$paramKey] = $value;
        }

        // Fusionner avec les paramètres WHERE
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

    /**
     * Exécute une requête SQL avec gestion d'erreur améliorée
     */
    public function safeQuery($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            // Log de l'erreur
            error_log("Erreur SQL: " . $e->getMessage() . " | Requête: $sql");

            // En mode développement, on peut afficher plus de détails
            if (defined('DEBUG') && DEBUG === true) {
                echo "Erreur SQL: " . $e->getMessage() . "<br>";
                echo "Requête: $sql<br>";
                echo "Paramètres: " . print_r($params, true);
                exit;
            }

            return false;
        }
    }

    /**
     * Version sécurisée de findOne
     */
    public function safeFindOne($sql, $params = [])
    {
        $stmt = $this->safeQuery($sql, $params);
        return $stmt ? $stmt->fetch() : false;
    }

    /**
     * Version sécurisée de findAll
     */
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

    /**
     * Vérifie si tous les paramètres nommés dans une requête SQL sont présents dans le tableau de paramètres
     * @param string $sql La requête SQL
     * @param array $params Le tableau de paramètres
     * @return bool True si tous les paramètres sont présents, false sinon
     */
    public function validateSqlParams($sql, $params = [])
    {
        $namedParams = [];
        // Trouver tous les paramètres nommés dans la requête SQL (format :param)
        preg_match_all('/:([a-zA-Z0-9_]+)/', $sql, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $param) {
                $namedParams[$param] = true;
            }
        }

        // Vérifier si tous les paramètres sont présents dans le tableau
        foreach ($namedParams as $param => $value) {
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

    /**
     * Commence une transaction
     * @return bool True si la transaction a démarré avec succès
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Valide une transaction
     * @return bool True si la transaction a été validée avec succès
     */
    public function commit()
    {
        return $this->connection->commit();
    }

    /**
     * Annule une transaction
     * @return bool True si la transaction a été annulée avec succès
     */
    public function rollBack()
    {
        return $this->connection->rollBack();
    }

    /**
     * Vérifie si une transaction est en cours
     * @return bool True si une transaction est en cours
     */
    public function inTransaction()
    {
        return $this->connection->inTransaction();
    }
}
