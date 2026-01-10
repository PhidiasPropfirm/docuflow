<?php
/**
 * TeamController - Gestion des équipes avec support multilingue
 */

namespace App\Controllers;

class TeamController extends BaseController
{
    /**
     * Liste des équipes
     */
    public function index(): void
    {
        $this->requireAuth();
        
        $stmt = $this->db->query("
            SELECT t.*, 
                   COUNT(DISTINCT u.id) as member_count,
                   COUNT(DISTINCT d.id) as document_count
            FROM teams t
            LEFT JOIN users u ON u.team_id = t.id AND u.is_active = 1
            LEFT JOIN documents d ON d.team_id = t.id
            GROUP BY t.id
            ORDER BY t.name
        ");
        $teams = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Récupérer les membres pour chaque équipe
        foreach ($teams as &$team) {
            $membersStmt = $this->db->prepare("
                SELECT id, first_name, last_name, email 
                FROM users 
                WHERE team_id = ? AND is_active = 1
                LIMIT 10
            ");
            $membersStmt->execute([$team['id']]);
            $team['members'] = $membersStmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        $this->render('pages/teams', ['teams' => $teams]);
    }
    
    /**
     * Créer ou modifier une équipe
     */
    public function store(): void
    {
        $this->requireAuth();
        $this->requirePermission('teams.create');
        
        $teamId = $_POST['team_id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $nameEn = trim($_POST['name_en'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $descriptionEn = trim($_POST['description_en'] ?? '');
        $color = $_POST['color'] ?? '#3B82F6';
        
        if (empty($name)) {
            $this->flash('error', __('error'));
            $this->redirect('/teams');
            return;
        }
        
        if ($teamId) {
            // Mise à jour
            $this->requirePermission('teams.edit');
            
            $stmt = $this->db->prepare("
                UPDATE teams 
                SET name = ?, name_en = ?, description = ?, description_en = ?, color = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$name, $nameEn ?: null, $description ?: null, $descriptionEn ?: null, $color, $teamId]);
            
            $this->flash('success', __('team_updated'));
        } else {
            // Création
            $stmt = $this->db->prepare("
                INSERT INTO teams (name, name_en, description, description_en, color, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([$name, $nameEn ?: null, $description ?: null, $descriptionEn ?: null, $color]);
            
            $this->flash('success', __('team_created'));
        }
        
        $this->redirect('/teams');
    }
    
    /**
     * Supprimer une équipe
     */
    public function delete(int $id): void
    {
        $this->requireAuth();
        $this->requirePermission('teams.delete');
        
        // Retirer les utilisateurs de l'équipe
        $stmt = $this->db->prepare("UPDATE users SET team_id = NULL WHERE team_id = ?");
        $stmt->execute([$id]);
        
        // Supprimer l'équipe
        $stmt = $this->db->prepare("DELETE FROM teams WHERE id = ?");
        $stmt->execute([$id]);
        
        $this->flash('success', __('team_deleted'));
        $this->redirect('/teams');
    }
}
