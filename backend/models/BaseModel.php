<?php

/**
 * Modèle de base pour factoriser les méthodes communes
 */
abstract class BaseModel
{
    protected $db;
    protected $tableName;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /* Méthodes CRUD de base */

    /**
     * Récupère un enregistrement par ID
     */
    public function getById($id)
    {
        if (!$this->tableName) {
            throw new Exception('Table name must be defined in child class');
        }

        $sql = "SELECT * FROM {$this->tableName} WHERE id = :id";
        return $this->db->findOne($sql, ['id' => $id]);
    }

    /**
     * Récupère tous les enregistrements
     */
    public function getAll($orderBy = 'id ASC')
    {
        if (!$this->tableName) {
            throw new Exception('Table name must be defined in child class');
        }

        $sql = "SELECT * FROM {$this->tableName} ORDER BY {$orderBy}";
        return $this->db->findAll($sql);
    }

    /**
     * Compte le nombre total d'enregistrements
     */
    public function countTotal()
    {
        if (!$this->tableName) {
            throw new Exception('Table name must be defined in child class');
        }

        $sql = "SELECT COUNT(*) as count FROM {$this->tableName}";
        $result = $this->db->findOne($sql);
        return (int)$result['count'];
    }

    /**
     * Supprime un enregistrement par ID
     */
    public function deleteById($id)
    {
        if (!$this->tableName) {
            throw new Exception('Table name must be defined in child class');
        }

        return $this->db->delete($this->tableName, 'id = :id', ['id' => $id]);
    }

    /* Méthodes utilitaires communes */

    /**
     * Vérifie si une table existe
     */
    protected function tableExists($tableName)
    {
        try {
            $sql = "SHOW TABLES LIKE :table";
            $result = $this->db->findOne($sql, ['table' => $tableName]);
            return $result !== false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Vérifie si une colonne existe dans une table
     */
    protected function columnExists($tableName, $columnName)
    {
        try {
            $sql = "SHOW COLUMNS FROM {$tableName} LIKE :column";
            $result = $this->db->findOne($sql, ['column' => $columnName]);
            return $result !== false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Récupère des enregistrements avec pagination
     */
    public function getPaginated($offset, $limit, $orderBy = 'id DESC')
    {
        if (!$this->tableName) {
            throw new Exception('Table name must be defined in child class');
        }

        $sql = "SELECT * FROM {$this->tableName} ORDER BY {$orderBy} LIMIT :limit OFFSET :offset";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte les enregistrements par statut
     */
    protected function countByStatus($statusColumn = 'status')
    {
        if (!$this->tableName) {
            throw new Exception('Table name must be defined in child class');
        }

        $sql = "SELECT {$statusColumn}, COUNT(*) as count 
                FROM {$this->tableName} 
                GROUP BY {$statusColumn}";

        $results = $this->db->findAll($sql);

        $counts = [];
        foreach ($results as $result) {
            $counts[$result[$statusColumn]] = (int)$result['count'];
        }

        return $counts;
    }

    /**
     * Vérifie si un champ a une valeur unique (pour validation)
     */
    protected function isFieldUnique($field, $value, $excludeId = null)
    {
        if (!$this->tableName) {
            throw new Exception('Table name must be defined in child class');
        }

        $sql = "SELECT id FROM {$this->tableName} WHERE {$field} = :value";
        $params = ['value' => $value];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        return $this->db->findOne($sql, $params) === false;
    }

    /**
     * Met à jour un enregistrement avec validation des données
     */
    protected function updateWithValidation($id, $data, $requiredFields = [])
    {
        if (!$this->tableName) {
            throw new Exception('Table name must be defined in child class');
        }

        /* Validation des champs requis */
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }

        /* Supprime les champs vides ou null */
        $cleanData = array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });

        if (empty($cleanData)) {
            return false;
        }

        return $this->db->update($this->tableName, $cleanData, 'id = :id', ['id' => $id]);
    }

    /**
     * Crée un enregistrement avec validation
     */
    protected function createWithValidation($data, $requiredFields = [])
    {
        if (!$this->tableName) {
            throw new Exception('Table name must be defined in child class');
        }

        /* Validation des champs requis */
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }

        return $this->db->insert($this->tableName, $data);
    }

    /**
     * Recherche avec filtres
     */
    protected function searchWithFilters($filters = [], $orderBy = 'id DESC', $limit = null)
    {
        if (!$this->tableName) {
            throw new Exception('Table name must be defined in child class');
        }

        $sql = "SELECT * FROM {$this->tableName}";
        $conditions = [];
        $params = [];

        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                $conditions[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY {$orderBy}";

        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->db->findAll($sql, $params);
    }
}
