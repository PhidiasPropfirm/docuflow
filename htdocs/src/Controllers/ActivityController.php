<?php
/**
 * ActivityController - Gestion du journal d'activité
 */

namespace App\Controllers;

class ActivityController extends BaseController
{
    /**
     * Afficher le journal d'activité
     */
    public function index(): void
    {
        $this->requireAuth();
        
        // Paramètres de filtrage
        $action = $_GET['action'] ?? null;
        $userId = $_GET['user'] ?? null;
        $dateFrom = $_GET['from'] ?? null;
        $dateTo = $_GET['to'] ?? null;
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        // Construction de la requête
        $where = [];
        $params = [];
        
        if ($action) {
            $where[] = "a.action = ?";
            $params[] = $action;
        }
        
        if ($userId) {
            $where[] = "a.user_id = ?";
            $params[] = $userId;
        }
        
        if ($dateFrom) {
            $where[] = "DATE(a.created_at) >= ?";
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $where[] = "DATE(a.created_at) <= ?";
            $params[] = $dateTo;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        // Compter le total
        $countSql = "SELECT COUNT(*) FROM activity_log a $whereClause";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $totalItems = $countStmt->fetchColumn();
        $totalPages = ceil($totalItems / $perPage);
        
        // Récupérer les activités avec pagination
        $sql = "
            SELECT 
                a.id,
                a.user_id,
                a.action,
                a.entity_type,
                a.entity_id,
                a.description,
                a.metadata,
                a.ip_address,
                a.created_at,
                u.first_name,
                u.last_name,
                u.email
            FROM activity_log a
            LEFT JOIN users u ON a.user_id = u.id
            $whereClause
            ORDER BY a.created_at DESC
            LIMIT $perPage OFFSET $offset
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $activities = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Récupérer la liste des utilisateurs pour le filtre
        $usersStmt = $this->db->query("
            SELECT DISTINCT u.id, u.first_name, u.last_name 
            FROM users u 
            INNER JOIN activity_log a ON a.user_id = u.id
            ORDER BY u.first_name, u.last_name
        ");
        $users = $usersStmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Pagination
        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalItems,
            'per_page' => $perPage
        ];
        
        $this->render('pages/activity', [
            'activities' => $activities,
            'users' => $users,
            'pagination' => $pagination
        ]);
    }
    
    /**
     * Widget activité récente pour le dashboard
     */
    public function recent(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                a.id,
                a.user_id,
                a.action,
                a.entity_type,
                a.entity_id,
                a.description,
                a.metadata,
                a.created_at,
                u.first_name,
                u.last_name
            FROM activity_log a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Enregistrer une activité
     */
    public static function log(
        \PDO $db,
        ?int $userId,
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null,
        ?array $metadata = null
    ): void {
        $stmt = $db->prepare("
            INSERT INTO activity_log (user_id, action, entity_type, entity_id, description, metadata, ip_address, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $userId,
            $action,
            $entityType,
            $entityId,
            $description,
            $metadata ? json_encode($metadata) : null,
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    }
}
