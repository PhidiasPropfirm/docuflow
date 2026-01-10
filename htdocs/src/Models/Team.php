<?php
/**
 * DocuFlow - Modèle Team
 * Gestion des équipes
 */

namespace App\Models;

class Team extends BaseModel {
    protected string $table = 'teams';
    protected array $fillable = ['name', 'description', 'color'];
    
    /**
     * Récupère une équipe avec ses membres
     */
    public function findWithMembers(int $id): ?array {
        $team = $this->find($id);
        
        if ($team) {
            $sql = "SELECT id, username, email, first_name, last_name, role, avatar, last_login 
                    FROM users WHERE team_id = ? AND is_active = 1 
                    ORDER BY last_name, first_name";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $team['members'] = $stmt->fetchAll();
            $team['member_count'] = count($team['members']);
        }
        
        return $team;
    }
    
    /**
     * Récupère toutes les équipes avec le nombre de membres
     */
    public function allWithMemberCount(): array {
        $sql = "SELECT t.*, COUNT(u.id) as member_count 
                FROM {$this->table} t 
                LEFT JOIN users u ON t.id = u.team_id AND u.is_active = 1 
                GROUP BY t.id 
                ORDER BY t.name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les documents d'une équipe
     */
    public function getDocuments(int $teamId, int $limit = 10): array {
        $sql = "SELECT d.*, u.first_name, u.last_name 
                FROM documents d 
                JOIN users u ON d.user_id = u.id 
                WHERE d.team_id = ? 
                ORDER BY d.created_at DESC 
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teamId, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Statistiques d'une équipe
     */
    public function getStats(int $teamId): array {
        // Nombre de documents
        $sqlDocs = "SELECT COUNT(*) as count FROM documents WHERE team_id = ?";
        $stmtDocs = $this->db->prepare($sqlDocs);
        $stmtDocs->execute([$teamId]);
        $docCount = (int) $stmtDocs->fetch()['count'];
        
        // Nombre de liaisons créées par l'équipe
        $sqlLinks = "SELECT COUNT(*) as count FROM document_links dl 
                     JOIN users u ON dl.created_by = u.id 
                     WHERE u.team_id = ?";
        $stmtLinks = $this->db->prepare($sqlLinks);
        $stmtLinks->execute([$teamId]);
        $linkCount = (int) $stmtLinks->fetch()['count'];
        
        // Nombre d'annotations
        $sqlAnnot = "SELECT COUNT(*) as count FROM annotations a 
                     JOIN users u ON a.user_id = u.id 
                     WHERE u.team_id = ?";
        $stmtAnnot = $this->db->prepare($sqlAnnot);
        $stmtAnnot->execute([$teamId]);
        $annotCount = (int) $stmtAnnot->fetch()['count'];
        
        return [
            'documents' => $docCount,
            'links' => $linkCount,
            'annotations' => $annotCount
        ];
    }
    
    /**
     * Vérifie si le nom d'équipe existe
     */
    public function nameExists(string $name, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE name = ?";
        $params = [$name];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['count'] > 0;
    }
}
