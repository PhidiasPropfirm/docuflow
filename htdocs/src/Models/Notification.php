<?php
/**
 * DocuFlow - Modèle Notification
 * Gestion des notifications
 */

namespace App\Models;

class Notification extends BaseModel {
    protected string $table = 'notifications';
    protected array $fillable = [
        'user_id', 'title', 'message', 'type', 'link', 'is_read'
    ];
    
    public const TYPES = [
        'info' => ['icon' => 'info', 'color' => '#3B82F6'],
        'success' => ['icon' => 'check-circle', 'color' => '#10B981'],
        'warning' => ['icon' => 'alert-triangle', 'color' => '#F59E0B'],
        'document' => ['icon' => 'file-text', 'color' => '#6366F1'],
        'link' => ['icon' => 'link', 'color' => '#8B5CF6'],
        'annotation' => ['icon' => 'message-circle', 'color' => '#EC4899']
    ];
    
    /**
     * Récupère les notifications d'un utilisateur
     */
    public function getByUser(int $userId, int $limit = 20, bool $unreadOnly = false): array {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ?";
        
        if ($unreadOnly) {
            $sql .= " AND is_read = 0";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Compte les notifications non lues
     */
    public function countUnread(int $userId): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE user_id = ? AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return (int) $stmt->fetch()['count'];
    }
    
    /**
     * Marque comme lue
     */
    public function markAsRead(int $id): bool {
        return $this->update($id, ['is_read' => 1]);
    }
    
    /**
     * Marque toutes comme lues
     */
    public function markAllAsRead(int $userId): bool {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE user_id = ? AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
    
    /**
     * Crée une notification pour plusieurs utilisateurs
     */
    public function notifyUsers(array $userIds, string $title, string $message, string $type = 'info', ?string $link = null): void {
        foreach ($userIds as $userId) {
            $this->create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'link' => $link
            ]);
        }
    }
    
    /**
     * Notifie tous les utilisateurs sauf un
     */
    public function notifyAllExcept(int $excludeUserId, string $title, string $message, string $type = 'info', ?string $link = null): void {
        $sql = "SELECT id FROM users WHERE id != ? AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$excludeUserId]);
        $users = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        $this->notifyUsers($users, $title, $message, $type, $link);
    }
    
    /**
     * Supprime les anciennes notifications (plus de 30 jours)
     */
    public function cleanup(int $daysOld = 30): int {
        $sql = "DELETE FROM {$this->table} 
                WHERE is_read = 1 AND created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$daysOld]);
        return $stmt->rowCount();
    }
    
    /**
     * Notifications récentes pour le polling AJAX
     */
    public function getNewSince(int $userId, string $since): array {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = ? AND created_at > ? AND is_read = 0
                ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $since]);
        return $stmt->fetchAll();
    }
}
