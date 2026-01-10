<?php
/**
 * DocuFlow - Modèle ActivityLog
 * Journal d'activité des utilisateurs
 */

namespace App\Models;

class ActivityLog extends BaseModel {
    protected string $table = 'activity_log';
    protected array $fillable = [
        'user_id', 'action', 'entity_type', 'entity_id',
        'description', 'metadata', 'ip_address', 'user_agent'
    ];
    
    public const ACTIONS = [
        'login' => 'Connexion',
        'logout' => 'Déconnexion',
        'upload' => 'Upload de document',
        'delete' => 'Suppression',
        'create_link' => 'Création de liaison',
        'delete_link' => 'Suppression de liaison',
        'create_zone' => 'Création de zone',
        'create_annotation' => 'Ajout d\'annotation',
        'resolve_annotation' => 'Résolution d\'annotation',
        'update_profile' => 'Modification du profil',
        'create_user' => 'Création d\'utilisateur',
        'update_user' => 'Modification d\'utilisateur'
    ];
    
    public const ENTITY_TYPES = [
        'document' => 'Document',
        'user' => 'Utilisateur',
        'team' => 'Équipe',
        'zone' => 'Zone',
        'link' => 'Liaison',
        'annotation' => 'Annotation'
    ];
    
    /**
     * Enregistre une activité
     */
    public function log(string $action, string $entityType, ?int $entityId = null, ?string $description = null, ?array $metadata = null): int {
        return $this->create([
            'user_id' => currentUserId(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'metadata' => $metadata ? json_encode($metadata) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500)
        ]);
    }
    
    /**
     * Récupère l'historique récent
     */
    public function getRecent(int $limit = 50): array {
        $sql = "SELECT al.*, 
                       u.first_name, u.last_name, u.avatar
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                ORDER BY al.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère l'historique d'un utilisateur
     */
    public function getByUser(int $userId, int $limit = 50): array {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère l'historique d'une entité
     */
    public function getByEntity(string $entityType, int $entityId): array {
        $sql = "SELECT al.*, u.first_name, u.last_name
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.entity_type = ? AND al.entity_id = ?
                ORDER BY al.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$entityType, $entityId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère l'historique filtré
     */
    public function getFiltered(array $filters = [], int $page = 1, int $perPage = 50): array {
        $where = [];
        $params = [];
        
        if (!empty($filters['user_id'])) {
            $where[] = "al.user_id = ?";
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['action'])) {
            $where[] = "al.action = ?";
            $params[] = $filters['action'];
        }
        
        if (!empty($filters['entity_type'])) {
            $where[] = "al.entity_type = ?";
            $params[] = $filters['entity_type'];
        }
        
        if (!empty($filters['date_from'])) {
            $where[] = "al.created_at >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where[] = "al.created_at <= ?";
            $params[] = $filters['date_to'] . ' 23:59:59';
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset = ($page - 1) * $perPage;
        
        // Total
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} al {$whereClause}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch()['total'];
        
        // Données
        $sql = "SELECT al.*, u.first_name, u.last_name, u.avatar
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                {$whereClause}
                ORDER BY al.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage)
        ];
    }
    
    /**
     * Statistiques d'activité
     */
    public function getStats(int $days = 30): array {
        $sql = "SELECT DATE(created_at) as date, COUNT(*) as count
                FROM {$this->table}
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY DATE(created_at)
                ORDER BY date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
    
    /**
     * Actions les plus fréquentes
     */
    public function getTopActions(int $limit = 10): array {
        $sql = "SELECT action, COUNT(*) as count
                FROM {$this->table}
                GROUP BY action
                ORDER BY count DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Nettoyage des anciennes entrées
     */
    public function cleanup(int $daysOld = 90): int {
        $sql = "DELETE FROM {$this->table} 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$daysOld]);
        return $stmt->rowCount();
    }
}
