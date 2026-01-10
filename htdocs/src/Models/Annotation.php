<?php
/**
 * DocuFlow - Modèle Annotation
 * Gestion des annotations sur les documents
 */

namespace App\Models;

class Annotation extends BaseModel {
    protected string $table = 'annotations';
    protected array $fillable = [
        'document_id', 'zone_id', 'user_id', 'content',
        'annotation_type', 'color', 'is_resolved'
    ];
    
    public const TYPES = [
        'comment' => ['label' => 'Commentaire', 'icon' => 'message-circle', 'color' => '#3B82F6'],
        'note' => ['label' => 'Note', 'icon' => 'file-text', 'color' => '#10B981'],
        'warning' => ['label' => 'Attention', 'icon' => 'alert-triangle', 'color' => '#F59E0B'],
        'question' => ['label' => 'Question', 'icon' => 'help-circle', 'color' => '#8B5CF6']
    ];
    
    /**
     * Récupère une annotation avec ses détails
     */
    public function findWithDetails(int $id): ?array {
        $sql = "SELECT a.*, 
                       u.first_name, u.last_name, u.avatar,
                       d.title as document_title,
                       dz.page_number, dz.x, dz.y, dz.width, dz.height
                FROM {$this->table} a
                JOIN users u ON a.user_id = u.id
                JOIN documents d ON a.document_id = d.id
                LEFT JOIN document_zones dz ON a.zone_id = dz.id
                WHERE a.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Récupère les annotations d'un document
     */
    public function getByDocument(int $documentId, bool $includeResolved = true): array {
        $sql = "SELECT a.*, 
                       u.first_name, u.last_name, u.avatar,
                       dz.page_number, dz.x, dz.y, dz.width, dz.height, dz.label
                FROM {$this->table} a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN document_zones dz ON a.zone_id = dz.id
                WHERE a.document_id = ?";
        
        if (!$includeResolved) {
            $sql .= " AND a.is_resolved = 0";
        }
        
        $sql .= " ORDER BY a.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$documentId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les annotations d'une page
     */
    public function getByPage(int $documentId, int $pageNumber): array {
        $sql = "SELECT a.*, 
                       u.first_name, u.last_name,
                       dz.x, dz.y, dz.width, dz.height
                FROM {$this->table} a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN document_zones dz ON a.zone_id = dz.id
                WHERE a.document_id = ? AND dz.page_number = ?
                ORDER BY dz.y, dz.x";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$documentId, $pageNumber]);
        return $stmt->fetchAll();
    }
    
    /**
     * Marque comme résolu
     */
    public function resolve(int $id): bool {
        return $this->update($id, ['is_resolved' => 1]);
    }
    
    /**
     * Compte les annotations non résolues
     */
    public function countUnresolved(?int $documentId = null): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE is_resolved = 0";
        $params = [];
        
        if ($documentId) {
            $sql .= " AND document_id = ?";
            $params[] = $documentId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['count'];
    }
    
    /**
     * Annotations récentes de l'utilisateur
     */
    public function getRecentByUser(int $userId, int $limit = 10): array {
        $sql = "SELECT a.*, d.title as document_title
                FROM {$this->table} a
                JOIN documents d ON a.document_id = d.id
                WHERE a.user_id = ?
                ORDER BY a.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
}
