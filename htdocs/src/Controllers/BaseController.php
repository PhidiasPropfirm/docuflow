<?php
/**
 * BaseController - Contrôleur de base pour tous les contrôleurs
 */

namespace App\Controllers;

abstract class BaseController
{
    protected \PDO $db;
    
    public function __construct()
    {
        $this->db = db();
    }
    
    /**
     * Vérifie que l'utilisateur est authentifié
     */
    protected function requireAuth(): void
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }
    }
    
    /**
     * Vérifie que l'utilisateur est admin
     */
    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (!isAdmin()) {
            flash('error', __('access_denied'));
            redirect('/');
        }
    }
    
    /**
     * Vérifie une permission spécifique
     */
    protected function requirePermission(string $permission): void
    {
        $this->requireAuth();
        if (!hasPermission($permission)) {
            flash('error', __('access_denied'));
            redirect('/');
        }
    }
    
    /**
     * Rendu d'une vue
     */
    protected function render(string $view, array $data = []): void
    {
        // Extraire les données pour les rendre disponibles dans la vue
        extract($data);
        
        // Chemin vers le fichier de vue
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("Vue non trouvée: $view");
        }
        
        // Inclure la vue
        require $viewPath;
    }
    
    /**
     * Réponse JSON
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Vérification CSRF
     */
    protected function verifyCsrf(): bool
    {
        $token = $_POST[CSRF_TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        return verify_csrf($token);
    }
    
    /**
     * Vérification CSRF avec arrêt si invalide
     */
    protected function requireCsrf(): void
    {
        if (!$this->verifyCsrf()) {
            if ($this->isAjax()) {
                $this->json(['error' => 'Invalid CSRF token'], 403);
            } else {
                flash('error', __('session_expired'));
                redirect($_SERVER['HTTP_REFERER'] ?? '/');
            }
        }
    }
    
    /**
     * Vérifie si la requête est AJAX
     */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Récupère l'utilisateur courant
     */
    protected function currentUser(): ?array
    {
        return currentUser();
    }
    
    /**
     * Récupère l'ID de l'utilisateur courant
     */
    protected function currentUserId(): ?int
    {
        return currentUserId();
    }
    
    /**
     * Log une activité
     */
    protected function logActivity(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null,
        ?array $metadata = null
    ): void {
        \App\Controllers\ActivityController::log(
            $this->db,
            $this->currentUserId(),
            $action,
            $entityType,
            $entityId,
            $description,
            $metadata
        );
    }
}
