<?php
/**
 * DocuFlow - Modèle User
 * Gestion des utilisateurs
 */

namespace App\Models;

class User extends BaseModel {
    protected string $table = 'users';
    protected array $fillable = [
        'team_id', 'username', 'email', 'password', 
        'first_name', 'last_name', 'role', 'avatar', 
        'last_login', 'is_active'
    ];
    
    /**
     * Trouve un utilisateur par email
     */
    public function findByEmail(string $email): ?array {
        return $this->findBy(['email' => $email]);
    }
    
    /**
     * Trouve un utilisateur par nom d'utilisateur
     */
    public function findByUsername(string $username): ?array {
        return $this->findBy(['username' => $username]);
    }
    
    /**
     * Authentifie un utilisateur
     */
    public function authenticate(string $login, string $password): ?array {
        // Cherche par email ou username
        $user = $this->findByEmail($login) ?? $this->findByUsername($login);
        
        if (!$user || !$user['is_active']) {
            return null;
        }
        
        if (!password_verify($password, $user['password'])) {
            return null;
        }
        
        // Met à jour la date de dernière connexion
        $this->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
        
        return $user;
    }
    
    /**
     * Crée un nouvel utilisateur avec hash du mot de passe
     */
    public function register(array $data): int {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        return $this->create($data);
    }
    
    /**
     * Change le mot de passe
     */
    public function changePassword(int $userId, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        return $this->update($userId, ['password' => $hash]);
    }
    
    /**
     * Vérifie si un email existe déjà
     */
    public function emailExists(string $email, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['count'] > 0;
    }
    
    /**
     * Vérifie si un username existe déjà
     */
    public function usernameExists(string $username, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = ?";
        $params = [$username];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['count'] > 0;
    }
    
    /**
     * Récupère tous les utilisateurs actifs
     */
    public function all(string $orderBy = 'last_name', string $direction = 'ASC'): array {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY {$orderBy} {$direction}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère tous les utilisateurs avec leur équipe
     */
    public function allWithTeam(): array {
        $sql = "SELECT u.*, t.name as team_name, t.color as team_color 
                FROM {$this->table} u 
                LEFT JOIN teams t ON u.team_id = t.id 
                WHERE u.is_active = 1
                ORDER BY u.last_name, u.first_name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les utilisateurs d'une équipe
     */
    public function getByTeam(int $teamId): array {
        return $this->where(['team_id' => $teamId], 'last_name, first_name');
    }
    
    /**
     * Récupère le nom complet
     */
    public function getFullName(array $user): string {
        return $user['first_name'] . ' ' . $user['last_name'];
    }
    
    /**
     * Récupère les initiales
     */
    public function getInitials(array $user): string {
        return strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
    }
    
    /**
     * Compte les utilisateurs actifs
     */
    public function countActive(): int {
        return $this->count(['is_active' => 1]);
    }
    
    /**
     * Recherche d'utilisateurs
     */
    public function search(string $query): array {
        $sql = "SELECT u.*, t.name as team_name 
                FROM {$this->table} u 
                LEFT JOIN teams t ON u.team_id = t.id 
                WHERE u.first_name LIKE ? 
                   OR u.last_name LIKE ? 
                   OR u.email LIKE ? 
                   OR u.username LIKE ?
                ORDER BY u.last_name, u.first_name";
        
        $like = "%{$query}%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$like, $like, $like, $like]);
        return $stmt->fetchAll();
    }
}
