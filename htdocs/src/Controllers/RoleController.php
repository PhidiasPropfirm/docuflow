<?php
/**
 * RoleController - Gestion des rôles avec support multilingue
 */

namespace App\Controllers;

class RoleController extends BaseController
{
    /**
     * Vérifie si la table role_permissions existe
     */
    private function hasPermissionsTable(): bool
    {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE 'role_permissions'");
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Récupère les permissions d'un rôle (si la table existe)
     */
    private function getRolePermissions(int $roleId): array
    {
        if (!$this->hasPermissionsTable()) {
            return [];
        }
        
        try {
            // Essayer avec la structure permission_id (liaison avec table permissions)
            $permStmt = $this->db->prepare("
                SELECT p.name 
                FROM role_permissions rp
                JOIN permissions p ON rp.permission_id = p.id
                WHERE rp.role_id = ?
            ");
            $permStmt->execute([$roleId]);
            return $permStmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            // Fallback: essayer avec une colonne 'permission' directe
            try {
                $permStmt = $this->db->prepare("SELECT permission FROM role_permissions WHERE role_id = ?");
                $permStmt->execute([$roleId]);
                return $permStmt->fetchAll(\PDO::FETCH_COLUMN);
            } catch (\Exception $e2) {
                return [];
            }
        }
    }
    
    /**
     * Liste des rôles
     */
    public function index(): void
    {
        $this->requireAuth();
        
        $stmt = $this->db->query("
            SELECT r.*, 
                   COUNT(DISTINCT u.id) as user_count
            FROM roles r
            LEFT JOIN users u ON u.role_id = r.id AND u.is_active = 1
            GROUP BY r.id
            ORDER BY r.id ASC
        ");
        $roles = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Récupérer les permissions pour chaque rôle (si la table existe)
        foreach ($roles as &$role) {
            $role['permissions'] = $this->getRolePermissions($role['id']);
        }
        
        $this->render('pages/roles', ['roles' => $roles]);
    }
    
    /**
     * API: Récupérer un rôle
     */
    public function get(int $id): void
    {
        $this->requireAuth();
        
        $stmt = $this->db->prepare("SELECT * FROM roles WHERE id = ?");
        $stmt->execute([$id]);
        $role = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$role) {
            http_response_code(404);
            echo json_encode(['error' => 'Role not found']);
            return;
        }
        
        // Permissions
        $role['permissions'] = $this->getRolePermissions($id);
        
        header('Content-Type: application/json');
        echo json_encode($role);
    }
    
    /**
     * Afficher un rôle (page détail)
     */
    public function show(int $id): void
    {
        $this->requireAuth();
        
        $stmt = $this->db->prepare("
            SELECT r.* 
            FROM roles r 
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        $role = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$role) {
            flash('error', __('error_404'));
            redirect('/roles');
            return;
        }
        
        // Permissions du rôle
        $role['permissions'] = $this->getRolePermissions($id);
        
        // Utilisateurs avec ce rôle
        $usersStmt = $this->db->prepare("
            SELECT id, first_name, last_name, email, is_active, created_at 
            FROM users 
            WHERE role_id = ? 
            ORDER BY first_name, last_name
        ");
        $usersStmt->execute([$id]);
        $users = $usersStmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $this->render('pages/role-show', [
            'role' => $role,
            'users' => $users
        ]);
    }
    
    /**
     * Créer ou modifier un rôle
     */
    public function store(): void
    {
        $this->requireAuth();
        
        // Vérifier permission si la fonction existe
        if (function_exists('hasPermission') && !hasPermission('roles.create') && !isAdmin()) {
            flash('error', __('access_denied'));
            redirect('/roles');
            return;
        }
        
        $roleId = $_POST['role_id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $displayName = trim($_POST['display_name'] ?? '');
        $displayNameEn = trim($_POST['display_name_en'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $descriptionEn = trim($_POST['description_en'] ?? '');
        $color = $_POST['color'] ?? '#3B82F6';
        $permissions = $_POST['permissions'] ?? [];
        
        if (empty($name) || empty($displayName)) {
            flash('error', __('error'));
            redirect('/roles');
            return;
        }
        
        // Vérifier le format du nom
        if (!preg_match('/^[a-z0-9_]+$/', $name)) {
            flash('error', __('error'));
            redirect('/roles');
            return;
        }
        
        $this->db->beginTransaction();
        
        try {
            if ($roleId) {
                // Vérifier si c'est un rôle système
                $checkStmt = $this->db->prepare("SELECT is_system FROM roles WHERE id = ?");
                $checkStmt->execute([$roleId]);
                $isSystem = $checkStmt->fetchColumn();
                
                if ($isSystem) {
                    // Ne pas modifier le nom d'un rôle système
                    $stmt = $this->db->prepare("
                        UPDATE roles 
                        SET display_name = ?, display_name_en = ?, description = ?, description_en = ?, color = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$displayName, $displayNameEn ?: null, $description ?: null, $descriptionEn ?: null, $color, $roleId]);
                } else {
                    $stmt = $this->db->prepare("
                        UPDATE roles 
                        SET name = ?, display_name = ?, display_name_en = ?, description = ?, description_en = ?, color = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$name, $displayName, $displayNameEn ?: null, $description ?: null, $descriptionEn ?: null, $color, $roleId]);
                }
            } else {
                // Création
                $stmt = $this->db->prepare("
                    INSERT INTO roles (name, display_name, display_name_en, description, description_en, color, is_system, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, 0, NOW(), NOW())
                ");
                $stmt->execute([$name, $displayName, $displayNameEn ?: null, $description ?: null, $descriptionEn ?: null, $color]);
                $roleId = $this->db->lastInsertId();
            }
            
            // Mettre à jour les permissions (si la table existe)
            if ($this->hasPermissionsTable() && !empty($permissions)) {
                $delStmt = $this->db->prepare("DELETE FROM role_permissions WHERE role_id = ?");
                $delStmt->execute([$roleId]);
                
                // Insérer avec permission_id
                $insertStmt = $this->db->prepare("
                    INSERT INTO role_permissions (role_id, permission_id) 
                    SELECT ?, id FROM permissions WHERE name = ?
                ");
                foreach ($permissions as $perm) {
                    $insertStmt->execute([$roleId, $perm]);
                }
            }
            
            $this->db->commit();
            flash('success', __('changes_saved'));
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            flash('error', __('operation_failed'));
        }
        
        redirect('/roles');
    }
    
    /**
     * Supprimer un rôle
     */
    public function delete(int $id): void
    {
        $this->requireAuth();
        
        // Vérifier permission si la fonction existe
        if (function_exists('hasPermission') && !hasPermission('roles.delete') && !isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }
        
        // Vérifier si c'est un rôle système
        $stmt = $this->db->prepare("SELECT is_system FROM roles WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->fetchColumn()) {
            http_response_code(403);
            echo json_encode(['error' => 'Cannot delete system role']);
            return;
        }
        
        // Récupérer l'ID du rôle "member" par défaut
        $memberStmt = $this->db->query("SELECT id FROM roles WHERE name = 'member' LIMIT 1");
        $memberId = $memberStmt->fetchColumn() ?: 2;
        
        // Transférer les utilisateurs vers le rôle member
        $stmt = $this->db->prepare("UPDATE users SET role_id = ? WHERE role_id = ?");
        $stmt->execute([$memberId, $id]);
        
        // Supprimer les permissions (si la table existe)
        if ($this->hasPermissionsTable()) {
            $stmt = $this->db->prepare("DELETE FROM role_permissions WHERE role_id = ?");
            $stmt->execute([$id]);
        }
        
        // Supprimer le rôle
        $stmt = $this->db->prepare("DELETE FROM roles WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true]);
    }
}
