<?php
/**
 * DocuFlow - Modèle de base
 * Classe abstraite pour tous les modèles
 */

namespace App\Models;

use PDO;

abstract class BaseModel {
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    
    public function __construct() {
        $this->db = \Database::getInstance();
    }
    
    /**
     * Trouve un enregistrement par son ID
     */
    public function find(int $id): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Récupère tous les enregistrements
     */
    public function all(string $orderBy = 'created_at', string $direction = 'DESC'): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$direction}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Recherche avec conditions
     */
    public function where(array $conditions, string $orderBy = null): array {
        $where = [];
        $params = [];
        
        foreach ($conditions as $column => $value) {
            if (is_array($value)) {
                $where[] = "{$column} {$value[0]} ?";
                $params[] = $value[1];
            } else {
                $where[] = "{$column} = ?";
                $params[] = $value;
            }
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where);
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Trouve le premier enregistrement correspondant
     */
    public function findBy(array $conditions): ?array {
        $result = $this->where($conditions);
        return $result[0] ?? null;
    }
    
    /**
     * Crée un nouvel enregistrement
     */
    public function create(array $data): int {
        $data = $this->filterFillable($data);
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        
        return (int) $this->db->lastInsertId();
    }
    
    /**
     * Met à jour un enregistrement
     */
    public function update(int $id, array $data): bool {
        $data = $this->filterFillable($data);
        $set = [];
        
        foreach (array_keys($data) as $column) {
            $set[] = "{$column} = ?";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE {$this->primaryKey} = ?";
        $params = array_values($data);
        $params[] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Supprime un enregistrement
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Compte les enregistrements
     */
    public function count(array $conditions = []): int {
        if (empty($conditions)) {
            $sql = "SELECT COUNT(*) as count FROM {$this->table}";
            $stmt = $this->db->query($sql);
        } else {
            $where = [];
            $params = [];
            
            foreach ($conditions as $column => $value) {
                $where[] = "{$column} = ?";
                $params[] = $value;
            }
            
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE " . implode(' AND ', $where);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
        }
        
        return (int) $stmt->fetch()['count'];
    }
    
    /**
     * Filtre les données pour ne garder que les champs autorisés
     */
    protected function filterFillable(array $data): array {
        if (empty($this->fillable)) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    /**
     * Exécute une requête SQL brute
     */
    public function raw(string $sql, array $params = []): array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Pagination
     */
    public function paginate(int $page = 1, int $perPage = 20, array $conditions = [], string $orderBy = 'created_at DESC'): array {
        $offset = ($page - 1) * $perPage;
        
        $where = '';
        $params = [];
        
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $whereParts[] = "{$column} = ?";
                $params[] = $value;
            }
            $where = 'WHERE ' . implode(' AND ', $whereParts);
        }
        
        // Compte total
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} {$where}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch()['total'];
        
        // Données paginées
        $sql = "SELECT * FROM {$this->table} {$where} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
}
