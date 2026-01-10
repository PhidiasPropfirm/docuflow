<?php
/**
 * DocuFlow - Contrôleur d'authentification
 */

namespace App\Controllers;

use App\Models\User;
use App\Models\ActivityLog;

class AuthController {
    private User $userModel;
    private ActivityLog $activityLog;
    
    public function __construct() {
        $this->userModel = new User();
        $this->activityLog = new ActivityLog();
    }
    
    /**
     * Affiche la page de connexion
     */
    public function showLogin(): void {
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
        
        require __DIR__ . '/../Views/pages/login.php';
    }
    
    /**
     * Traite la connexion
     */
    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }
        
        // Vérification CSRF
        if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
            flash('error', 'Session expirée, veuillez réessayer.');
            redirect('/login');
        }
        
        $login = sanitize($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Validation
        if (empty($login) || empty($password)) {
            flash('error', 'Veuillez remplir tous les champs.');
            redirect('/login');
        }
        
        // Tentative d'authentification
        $user = $this->userModel->authenticate($login, $password);
        
        if (!$user) {
            flash('error', 'Identifiants incorrects.');
            redirect('/login');
        }
        
        // Création de la session
        $this->createSession($user);
        
        // Log de l'activité
        $this->activityLog->log('login', 'user', $user['id'], 'Connexion réussie');
        
        flash('success', 'Bienvenue, ' . $user['first_name'] . ' !');
        redirect('/dashboard');
    }
    
    /**
     * Déconnexion
     */
    public function logout(): void {
        if (isLoggedIn()) {
            $this->activityLog->log('logout', 'user', currentUserId(), 'Déconnexion');
        }
        
        // Destruction de la session
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        redirect('/login');
    }
    
    /**
     * Crée la session utilisateur
     */
    private function createSession(array $user): void {
        // Régénère l'ID de session pour la sécurité
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['team_id'] = $user['team_id'];
        $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['login_time'] = time();
        
        // Stocker les infos utilisateur pour le template
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'role' => $user['role'],
            'team_id' => $user['team_id']
        ];
    }
    
    /**
     * Middleware d'authentification
     */
    public static function requireAuth(): void {
        if (!isLoggedIn()) {
            flash('error', 'Vous devez être connecté pour accéder à cette page.');
            redirect('/login');
        }
        
        // Vérification de l'expiration de la session
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > SESSION_LIFETIME) {
            $_SESSION = [];
            session_destroy();
            flash('error', 'Votre session a expiré.');
            redirect('/login');
        }
    }
    
    /**
     * Middleware admin
     */
    public static function requireAdmin(): void {
        self::requireAuth();
        
        if (!isAdmin()) {
            flash('error', 'Vous n\'avez pas les droits d\'accès à cette page.');
            redirect('/dashboard');
        }
    }
}
