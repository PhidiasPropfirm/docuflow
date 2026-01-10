<?php
namespace App\Models;

/**
 * Modèle pour la gestion du chat
 */
class ChatMessage extends BaseModel {
    protected string $table = 'chat_messages';
    
    /**
     * Récupère les messages d'un canal
     */
    public function getByChannel(string $channel, int $limit = 50, ?int $afterId = null): array {
        $sql = "SELECT m.*, 
                       u.first_name, u.last_name, u.username,
                       r.message as reply_message, r.user_id as reply_user_id,
                       ru.first_name as reply_first_name, ru.last_name as reply_last_name
                FROM {$this->table} m
                JOIN users u ON u.id = m.user_id
                LEFT JOIN {$this->table} r ON r.id = m.reply_to_id
                LEFT JOIN users ru ON ru.id = r.user_id
                WHERE m.channel = ?";
        
        $params = [$channel];
        
        if ($afterId) {
            $sql .= " AND m.id > ?";
            $params[] = $afterId;
        }
        
        $sql .= " ORDER BY m.created_at ASC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les nouveaux messages depuis un ID
     */
    public function getNewMessages(string $channel, int $lastId): array {
        $sql = "SELECT m.*, 
                       u.first_name, u.last_name, u.username
                FROM {$this->table} m
                JOIN users u ON u.id = m.user_id
                WHERE m.channel = ? AND m.id > ?
                ORDER BY m.created_at ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$channel, $lastId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Envoie un message
     */
    public function send(int $userId, string $channel, string $message, ?int $replyToId = null): int {
        $sql = "INSERT INTO {$this->table} (user_id, channel, message, reply_to_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $channel, $message, $replyToId]);
        return (int) $this->db->lastInsertId();
    }
    
    /**
     * Supprime un message (par son auteur ou un admin)
     */
    public function deleteMessage(int $messageId, int $userId, bool $isAdmin = false): bool {
        if ($isAdmin) {
            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$messageId]);
        } else {
            $sql = "DELETE FROM {$this->table} WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$messageId, $userId]);
        }
    }
    
    /**
     * Compte les messages non lus pour un utilisateur
     */
    public function getUnreadCount(int $userId, string $channel = 'general'): int {
        $sql = "SELECT COUNT(*) FROM {$this->table} m
                LEFT JOIN chat_read_status rs ON rs.user_id = ? AND rs.channel = m.channel
                WHERE m.channel = ? 
                AND m.user_id != ?
                AND (rs.last_read_id IS NULL OR m.id > rs.last_read_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $channel, $userId]);
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * Compte tous les messages non lus
     */
    public function getTotalUnreadCount(int $userId): int {
        $sql = "SELECT COUNT(*) FROM {$this->table} m
                LEFT JOIN chat_read_status rs ON rs.user_id = ? AND rs.channel = m.channel
                WHERE m.user_id != ?
                AND (rs.last_read_id IS NULL OR m.id > rs.last_read_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId]);
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * Marque les messages comme lus
     */
    public function markAsRead(int $userId, string $channel): void {
        // Récupérer le dernier message ID
        $sql = "SELECT MAX(id) FROM {$this->table} WHERE channel = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$channel]);
        $lastId = (int) $stmt->fetchColumn();
        
        // Upsert le statut de lecture
        $sql = "INSERT INTO chat_read_status (user_id, channel, last_read_at, last_read_id) 
                VALUES (?, ?, NOW(), ?)
                ON DUPLICATE KEY UPDATE last_read_at = NOW(), last_read_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $channel, $lastId, $lastId]);
    }
    
    /**
     * Met à jour le statut en ligne d'un utilisateur
     */
    public function updateOnlineStatus(int $userId, string $channel = 'general'): void {
        $sql = "INSERT INTO chat_online_users (user_id, current_channel) 
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE last_activity = NOW(), current_channel = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $channel, $channel]);
    }
    
    /**
     * Récupère les utilisateurs en ligne (actifs dans les 2 dernières minutes)
     */
    public function getOnlineUsers(string $channel = null): array {
        $sql = "SELECT o.*, u.first_name, u.last_name, u.username
                FROM chat_online_users o
                JOIN users u ON u.id = o.user_id
                WHERE o.last_activity > DATE_SUB(NOW(), INTERVAL 2 MINUTE)";
        
        $params = [];
        if ($channel) {
            $sql .= " AND o.current_channel = ?";
            $params[] = $channel;
        }
        
        $sql .= " ORDER BY u.first_name, u.last_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Nettoie les vieux messages (plus de 30 jours)
     */
    public function cleanOldMessages(int $days = 30): int {
        $sql = "DELETE FROM {$this->table} WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->rowCount();
    }
    
    /**
     * Récupère les canaux disponibles pour un utilisateur
     */
    public function getAvailableChannels(int $userId): array {
        // Canal général + canaux de documents où l'utilisateur a participé
        $sql = "SELECT DISTINCT channel, 
                       CASE 
                           WHEN channel = 'general' THEN 'Général'
                           WHEN channel LIKE 'document_%' THEN CONCAT('Document #', SUBSTRING(channel, 10))
                           WHEN channel LIKE 'team_%' THEN CONCAT('Équipe #', SUBSTRING(channel, 6))
                           ELSE channel
                       END as display_name
                FROM {$this->table}
                WHERE channel = 'general' 
                   OR user_id = ?
                   OR channel IN (SELECT CONCAT('team_', team_id) FROM users WHERE id = ? AND team_id IS NOT NULL)
                ORDER BY channel = 'general' DESC, channel";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll();
    }
}
