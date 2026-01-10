<?php
/**
 * DocuFlow - Modèle Team
 * Gestion des équipes
 * 
 * CORRECTION: Requête SQL corrigée pour éviter les doublons avec GROUP BY
 */

namespace App\Models;

class Team extends BaseModel {
    protected string $table = 'teams';
    protected array $fillable = ['name', 'name_en', 'description', 'description_en', 'color'];
    
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
     * CORRIGÉ: Utilise une sous-requête pour éviter les problèmes de GROUP BY
     */
    public function allWithMemberCount(): array {
        $sql = "SELECT t.*,
                       (SELECT COUNT(*) FROM users u WHERE u.team_id = t.id AND u.is_active = 1) as member_count,
                       (SELECT COUNT(*) FROM documents d WHERE d.team_id = t.id) as document_count
                FROM {$this->table} t
                ORDER BY t.name";
        $stmt = $this->db->query($sql);
        $teams = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Récupérer les membres pour chaque équipe
        foreach ($teams as $key => $team) {
            $membersStmt = $this->db->prepare("
                SELECT id, first_name, last_name, email 
                FROM users 
                WHERE team_id = ? AND is_active = 1
                ORDER BY last_name, first_name
                LIMIT 10
            ");
            $membersStmt->execute([$team['id']]);
            $teams[$key]['members'] = $membersStmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        return $teams;
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
    
    /**
     * Retire tous les utilisateurs d'une équipe (met team_id à NULL)
     */
    public function removeUsersFromTeam(int $teamId): bool {
        $sql = "UPDATE users SET team_id = NULL WHERE team_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$teamId]);
    }
    
    /**
     * Crée une équipe
     */
    public function create(array $data): int {
        $sql = "INSERT INTO {$this->table} (name, name_en, description, description_en, color, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['name'] ?? '',
            $data['name_en'] ?? null,
            $data['description'] ?? null,
            $data['description_en'] ?? null,
            $data['color'] ?? '#3B82F6'
        ]);
        return (int) $this->db->lastInsertId();
    }
    
    /**
     * Met à jour une équipe
     */
    public function update(int $id, array $data): bool {
        $fields = [];
        $values = [];
        
        $allowedFields = ['name', 'name_en', 'description', 'description_en', 'color'];
        
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $fields[] = "updated_at = NOW()";
        $values[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
}
