<?php
namespace App\Models;

/**
 * Modèle pour la gestion des permissions
 */
class Permission extends BaseModel {
    protected string $table = 'permissions';
    
    /**
     * Récupère toutes les permissions groupées par catégorie
     */
    public function allGroupedByCategory(): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY category, display_name";
        $stmt = $this->db->query($sql);
        $permissions = $stmt->fetchAll();
        
        $grouped = [];
        foreach ($permissions as $perm) {
            $category = $perm['category'];
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $perm;
        }
        
        return $grouped;
    }
    
    /**
     * Récupère les permissions d'un utilisateur via son rôle
     */
    public function getByUserId(int $userId): array {
        $sql = "SELECT DISTINCT p.* FROM {$this->table} p 
                JOIN role_permissions rp ON rp.permission_id = p.id 
                JOIN users u ON u.role_id = rp.role_id 
                WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Vérifie si un utilisateur a une permission
     */
    public function userHas(int $userId, string $permissionName): bool {
        $sql = "SELECT 1 FROM {$this->table} p 
                JOIN role_permissions rp ON rp.permission_id = p.id 
                JOIN users u ON u.role_id = rp.role_id 
                WHERE u.id = ? AND p.name = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $permissionName]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Récupère les noms des permissions d'un utilisateur
     */
    public function getUserPermissionNames(int $userId): array {
        $sql = "SELECT DISTINCT p.name FROM {$this->table} p 
                JOIN role_permissions rp ON rp.permission_id = p.id 
                JOIN users u ON u.role_id = rp.role_id 
                WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    /**
     * Labels des catégories
     */
    public static function getCategoryLabels(): array {
        return [
            'documents' => 'Documents',
            'zones' => 'Zones & Annotations',
            'links' => 'Liaisons',
            'users' => 'Utilisateurs',
            'teams' => 'Équipes',
            'roles' => 'Rôles & Permissions',
            'admin' => 'Administration',
            'general' => 'Général'
        ];
    }
}
