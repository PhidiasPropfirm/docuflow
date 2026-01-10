<?php
namespace App\Models;

/**
 * Modèle pour la gestion des rôles
 */
class Role extends BaseModel {
    protected string $table = 'roles';
    
    /**
     * Récupère tous les rôles avec le nombre d'utilisateurs
     */
    public function allWithUserCount(): array {
        $sql = "SELECT r.*, COUNT(u.id) as user_count 
                FROM {$this->table} r 
                LEFT JOIN users u ON u.role_id = r.id AND u.is_active = 1
                GROUP BY r.id 
                ORDER BY r.is_system DESC, r.display_name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère un rôle avec ses permissions
     */
    public function findWithPermissions(int $id): ?array {
        $role = $this->find($id);
        if (!$role) return null;
        
        $sql = "SELECT p.* FROM permissions p 
                JOIN role_permissions rp ON rp.permission_id = p.id 
                WHERE rp.role_id = ? 
                ORDER BY p.category, p.display_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $role['permissions'] = $stmt->fetchAll();
        
        return $role;
    }
    
    /**
     * Récupère un rôle par son nom
     */
    public function findByName(string $name): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE name = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Crée un rôle
     */
    public function create(array $data): int {
        $sql = "INSERT INTO {$this->table} (name, display_name, description, color) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['display_name'],
            $data['description'] ?? null,
            $data['color'] ?? '#6B7280'
        ]);
        return (int) $this->db->lastInsertId();
    }
    
    /**
     * Met à jour un rôle
     */
    public function update(int $id, array $data): bool {
        $fields = [];
        $values = [];
        
        foreach (['display_name', 'description', 'color'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Supprime un rôle (si non système)
     */
    public function delete(int $id): bool {
        // Vérifier que ce n'est pas un rôle système
        $role = $this->find($id);
        if (!$role || $role['is_system']) {
            return false;
        }
        
        // Mettre les utilisateurs de ce rôle en "member"
        $memberRole = $this->findByName('member');
        if ($memberRole) {
            $sql = "UPDATE users SET role_id = ?, role = 'member' WHERE role_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$memberRole['id'], $id]);
        }
        
        // Supprimer le rôle
        $sql = "DELETE FROM {$this->table} WHERE id = ? AND is_system = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Définit les permissions d'un rôle
     */
    public function setPermissions(int $roleId, array $permissionIds): bool {
        // Supprimer les anciennes permissions
        $sql = "DELETE FROM role_permissions WHERE role_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roleId]);
        
        // Ajouter les nouvelles
        if (!empty($permissionIds)) {
            $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            foreach ($permissionIds as $permId) {
                $stmt->execute([$roleId, $permId]);
            }
        }
        
        return true;
    }
    
    /**
     * Récupère les IDs des permissions d'un rôle
     */
    public function getPermissionIds(int $roleId): array {
        $sql = "SELECT permission_id FROM role_permissions WHERE role_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roleId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    /**
     * Vérifie si un rôle a une permission
     */
    public function hasPermission(int $roleId, string $permissionName): bool {
        $sql = "SELECT 1 FROM role_permissions rp 
                JOIN permissions p ON p.id = rp.permission_id 
                WHERE rp.role_id = ? AND p.name = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roleId, $permissionName]);
        return $stmt->fetch() !== false;
    }
}
