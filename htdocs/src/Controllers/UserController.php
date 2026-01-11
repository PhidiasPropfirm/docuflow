<?php
/**
 * DocuFlow - Contrôleur Utilisateurs
 * Gestion des utilisateurs et équipes
 * 
 * CORRECTION: Bug des doublons d'équipes corrigé dans la méthode teams()
 */

namespace App\Controllers;

use App\Models\User;
use App\Models\Team;
use App\Models\ActivityLog;

class UserController {
    private User $userModel;
    private Team $teamModel;
    private ActivityLog $activityLog;
    
    public function __construct() {
        $this->userModel = new User();
        $this->teamModel = new Team();
        $this->activityLog = new ActivityLog();
    }
    
    /**
     * Liste des utilisateurs (admin uniquement)
     */
    public function index(): void {
        AuthController::requireAdmin();
        
        $filters = [
            'search' => $_GET['search'] ?? '',
            'role' => $_GET['role'] ?? '',
            'team_id' => $_GET['team_id'] ?? ''
        ];
        
        $users = $this->userModel->allWithTeam();
        $teams = $this->teamModel->allWithMemberCount();
        
        require __DIR__ . '/../Views/pages/users/index.php';
    }
    
    /**
     * Formulaire de création d'utilisateur
     */
    public function create(): void {
        AuthController::requireAdmin();
        
        $teams = $this->teamModel->all();
        
        require __DIR__ . '/../Views/pages/users/form.php';
    }
    
    /**
     * Crée un nouvel utilisateur
     */
    public function store(): void {
        AuthController::requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/users/create');
        }
        
        if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
            flash('error', __('session_expired') ?? 'Session expirée.');
            redirect('/users/create');
        }
        
        // Validation
        $errors = $this->validateUserData($_POST);
        
        if (!empty($errors)) {
            flash('error', implode('<br>', $errors));
            redirect('/users/create');
        }
        
        // Vérification unicité
        if ($this->userModel->emailExists($_POST['email'])) {
            flash('error', __('email_already_used') ?? 'Cet email est déjà utilisé.');
            redirect('/users/create');
        }
        
        if ($this->userModel->usernameExists($_POST['username'])) {
            flash('error', __('username_already_used') ?? 'Ce nom d\'utilisateur est déjà utilisé.');
            redirect('/users/create');
        }
        
        // Création
        $userId = $this->userModel->register([
            'team_id' => !empty($_POST['team_id']) ? (int) $_POST['team_id'] : null,
            'username' => sanitize($_POST['username']),
            'email' => sanitize($_POST['email']),
            'password' => $_POST['password'],
            'first_name' => sanitize($_POST['first_name']),
            'last_name' => sanitize($_POST['last_name']),
            'role' => $_POST['role'] ?? 'member'
        ]);
        
        $this->activityLog->log('create_user', 'user', $userId, __('activity_create_user', ['name' => $_POST['username']]));
        
        flash('success', __('user_created') ?? 'Utilisateur créé avec succès.');
        redirect('/users');
    }
    
    /**
     * Affiche le profil d'un utilisateur
     */
    public function show(int $id): void {
        AuthController::requireAuth();
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            flash('error', __('user_not_found') ?? 'Utilisateur introuvable.');
            redirect('/users');
        }
        
        $team = $user['team_id'] ? $this->teamModel->find($user['team_id']) : null;
        $activity = $this->activityLog->getByUser($id, 20);
        
        require __DIR__ . '/../Views/pages/users/show.php';
    }
    
    /**
     * Formulaire de modification
     */
    public function edit(int $id): void {
        AuthController::requireAuth();
        
        // Un utilisateur peut modifier son propre profil, admin peut modifier tout le monde
        if (!isAdmin() && currentUserId() !== $id) {
            flash('error', __('access_denied') ?? 'Accès refusé.');
            redirect('/dashboard');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            flash('error', __('user_not_found') ?? 'Utilisateur introuvable.');
            redirect('/users');
        }
        
        $teams = $this->teamModel->all();
        
        require __DIR__ . '/../Views/pages/users/form.php';
    }
    
    /**
     * Met à jour un utilisateur
     */
    public function update(int $id): void {
        AuthController::requireAuth();
        
        if (!isAdmin() && currentUserId() !== $id) {
            flash('error', __('access_denied') ?? 'Accès refusé.');
            redirect('/dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/users/' . $id . '/edit');
        }
        
        if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
            flash('error', __('session_expired') ?? 'Session expirée.');
            redirect('/users/' . $id . '/edit');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            flash('error', __('user_not_found') ?? 'Utilisateur introuvable.');
            redirect('/users');
        }
        
        // Vérification unicité email
        if ($this->userModel->emailExists($_POST['email'], $id)) {
            flash('error', __('email_already_used') ?? 'Cet email est déjà utilisé.');
            redirect('/users/' . $id . '/edit');
        }
        
        // Données à mettre à jour
        $data = [
            'email' => sanitize($_POST['email']),
            'first_name' => sanitize($_POST['first_name']),
            'last_name' => sanitize($_POST['last_name'])
        ];
        
        // Seul l'admin peut modifier ces champs
        if (isAdmin()) {
            $data['team_id'] = !empty($_POST['team_id']) ? (int) $_POST['team_id'] : null;
            $data['role'] = $_POST['role'] ?? 'member';
            
            // Ne modifier is_active que si le champ checkbox est présent dans le formulaire
            // (indiqué par la présence du champ hidden preserve_is_active ou du checkbox is_active)
            if (isset($_POST['preserve_is_active']) || array_key_exists('is_active', $_POST)) {
                $data['is_active'] = isset($_POST['is_active']) ? 1 : 0;
            }
            // Si aucun des deux n'est présent, on ne touche pas à is_active (préserve la valeur actuelle)
        }
        
        $this->userModel->update($id, $data);
        $this->activityLog->log('update_user', 'user', $id, __('activity_update_user') ?? 'Modification du profil');
        
        // Si l'utilisateur modifie son propre profil, met à jour la session
        if (currentUserId() === $id) {
            $_SESSION['full_name'] = $data['first_name'] . ' ' . $data['last_name'];
        }
        
        flash('success', __('user_updated') ?? 'Profil mis à jour.');
        redirect('/users/' . $id);
    }
    
    /**
     * Change le mot de passe
     */
    public function changePassword(int $id): void {
        AuthController::requireAuth();
        
        if (!isAdmin() && currentUserId() !== $id) {
            flash('error', __('access_denied') ?? 'Accès refusé.');
            redirect('/dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/users/' . $id . '/edit');
        }
        
        if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
            flash('error', __('session_expired') ?? 'Session expirée.');
            redirect('/users/' . $id . '/edit');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            flash('error', __('user_not_found') ?? 'Utilisateur introuvable.');
            redirect('/users');
        }
        
        // Si ce n'est pas l'admin, vérifier l'ancien mot de passe
        if (!isAdmin() || currentUserId() === $id) {
            if (!password_verify($_POST['current_password'] ?? '', $user['password'])) {
                flash('error', __('current_password_incorrect') ?? 'Mot de passe actuel incorrect.');
                redirect('/users/' . $id . '/edit');
            }
        }
        
        // Validation nouveau mot de passe
        if (strlen($_POST['new_password'] ?? '') < PASSWORD_MIN_LENGTH) {
            flash('error', __('password_too_short') ?? 'Le nouveau mot de passe doit contenir au moins ' . PASSWORD_MIN_LENGTH . ' caractères.');
            redirect('/users/' . $id . '/edit');
        }
        
        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            flash('error', __('passwords_not_match') ?? 'Les mots de passe ne correspondent pas.');
            redirect('/users/' . $id . '/edit');
        }
        
        $this->userModel->changePassword($id, $_POST['new_password']);
        $this->activityLog->log('update_password', 'user', $id, __('activity_change_password') ?? 'Changement de mot de passe');
        
        flash('success', __('password_changed') ?? 'Mot de passe modifié.');
        redirect('/users/' . $id);
    }
    
    /**
     * Désactive un utilisateur (soft delete)
     */
    public function deactivate(int $id): void {
        AuthController::requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/users');
        }
        
        if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
            flash('error', __('session_expired') ?? 'Session expirée.');
            redirect('/users');
        }
        
        // On ne peut pas se désactiver soi-même
        if (currentUserId() === $id) {
            flash('error', __('cannot_deactivate_self') ?? 'Vous ne pouvez pas désactiver votre propre compte.');
            redirect('/users');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            flash('error', __('user_not_found') ?? 'Utilisateur introuvable.');
            redirect('/users');
        }
        
        $username = $user['username'];
        
        // Désactivation de l'utilisateur
        $this->userModel->update($id, ['is_active' => 0]);
        $this->activityLog->log('deactivate', 'user', $id, __('activity_deactivate_user', ['name' => $username]));
        
        flash('success', __('user_deactivated', ['name' => $username]) ?? 'Utilisateur "' . $username . '" désactivé.');
        redirect('/users');
    }
    
    /**
     * Supprime un utilisateur (hard delete)
     */
    public function delete(int $id): void {
        AuthController::requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/users');
        }
        
        if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
            flash('error', __('session_expired') ?? 'Session expirée.');
            redirect('/users');
        }
        
        // On ne peut pas se supprimer soi-même
        if (currentUserId() === $id) {
            flash('error', __('cannot_delete_self') ?? 'Vous ne pouvez pas supprimer votre propre compte.');
            redirect('/users');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            flash('error', __('user_not_found') ?? 'Utilisateur introuvable.');
            redirect('/users');
        }
        
        $username = $user['username'];
        
        // Suppression réelle de l'utilisateur
        $this->userModel->delete($id);
        $this->activityLog->log('delete', 'user', $id, __('activity_delete_user', ['name' => $username]));
        
        flash('success', __('user_deleted') ?? 'Utilisateur supprimé.');
        redirect('/users');
    }
    
    /**
     * Page Mon Profil
     */
    public function profile(): void {
        AuthController::requireAuth();
        
        $user = currentUser();
        $userId = currentUserId();
        $team = $user['team_id'] ? $this->teamModel->find($user['team_id']) : null;
        $activity = $this->activityLog->getByUser($userId, 20);
        
        // Statistiques de l'utilisateur
        $stats = $this->getUserStats($userId);
        
        require __DIR__ . '/../Views/pages/profile.php';
    }
    
    /**
     * Récupère les statistiques d'un utilisateur
     */
    private function getUserStats(int $userId): array {
        try {
            $db = \Database::getInstance();
            
            // Documents uploadés par l'utilisateur
            $stmtDocs = $db->prepare("SELECT COUNT(*) as count FROM documents WHERE user_id = ?");
            $stmtDocs->execute([$userId]);
            $documents = (int) $stmtDocs->fetch()['count'];
            
            // Annotations créées par l'utilisateur
            $stmtAnnot = $db->prepare("SELECT COUNT(*) as count FROM annotations WHERE user_id = ?");
            $stmtAnnot->execute([$userId]);
            $annotations = (int) $stmtAnnot->fetch()['count'];
            
            // Liaisons créées par l'utilisateur
            $stmtLinks = $db->prepare("SELECT COUNT(*) as count FROM document_links WHERE created_by = ?");
            $stmtLinks->execute([$userId]);
            $links = (int) $stmtLinks->fetch()['count'];
            
            return [
                'documents' => $documents,
                'annotations' => $annotations,
                'links' => $links
            ];
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des valeurs par défaut
            return [
                'documents' => 0,
                'annotations' => 0,
                'links' => 0
            ];
        }
    }
    
    /**
     * Valide les données utilisateur
     */
    private function validateUserData(array $data, bool $isUpdate = false): array {
        $errors = [];
        
        if (empty($data['username']) && !$isUpdate) {
            $errors[] = __('username_required') ?? 'Le nom d\'utilisateur est requis.';
        } elseif (!$isUpdate && !preg_match('/^[a-zA-Z0-9_]{3,50}$/', $data['username'])) {
            $errors[] = __('username_invalid') ?? 'Le nom d\'utilisateur doit contenir entre 3 et 50 caractères (lettres, chiffres, underscore).';
        }
        
        if (empty($data['email'])) {
            $errors[] = __('email_required') ?? 'L\'email est requis.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = __('email_invalid') ?? 'L\'email n\'est pas valide.';
        }
        
        if (!$isUpdate) {
            if (empty($data['password'])) {
                $errors[] = __('password_required') ?? 'Le mot de passe est requis.';
            } elseif (strlen($data['password']) < PASSWORD_MIN_LENGTH) {
                $errors[] = __('password_too_short') ?? 'Le mot de passe doit contenir au moins ' . PASSWORD_MIN_LENGTH . ' caractères.';
            }
        }
        
        if (empty($data['first_name'])) {
            $errors[] = __('first_name_required') ?? 'Le prénom est requis.';
        }
        
        if (empty($data['last_name'])) {
            $errors[] = __('last_name_required') ?? 'Le nom est requis.';
        }
        
        return $errors;
    }
    
    // ==========================================
    // Gestion des équipes
    // ==========================================
    
    /**
     * Liste des équipes
     * CORRIGÉ: Utilisation de for() au lieu de foreach avec référence
     */
    public function teams(): void {
        AuthController::requireAdmin();
        
        // Récupérer toutes les équipes avec compteurs et membres
        $teams = $this->teamModel->allWithMemberCount();
        
        // Ajouter les stats pour chaque équipe
        // CORRECTION: Utiliser for() au lieu de foreach(&$team) pour éviter les bugs de référence
        $teamsCount = count($teams);
        for ($i = 0; $i < $teamsCount; $i++) {
            $teams[$i]['stats'] = $this->teamModel->getStats($teams[$i]['id']);
        }
        
        require __DIR__ . '/../Views/pages/teams.php';
    }
    
    /**
     * Crée une équipe
     */
    public function createTeam(): void {
        AuthController::requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/teams');
        }
        
        if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
            flash('error', __('session_expired') ?? 'Session expirée.');
            redirect('/teams');
        }
        
        if (empty($_POST['name'])) {
            flash('error', __('team_name_required') ?? 'Le nom de l\'équipe est requis.');
            redirect('/teams');
        }
        
        if ($this->teamModel->nameExists($_POST['name'])) {
            flash('error', __('team_name_exists') ?? 'Ce nom d\'équipe existe déjà.');
            redirect('/teams');
        }
        
        $teamId = $this->teamModel->create([
            'name' => sanitize($_POST['name']),
            'name_en' => !empty($_POST['name_en']) ? sanitize($_POST['name_en']) : null,
            'description' => sanitize($_POST['description'] ?? ''),
            'description_en' => !empty($_POST['description_en']) ? sanitize($_POST['description_en']) : null,
            'color' => $_POST['color'] ?? '#3B82F6'
        ]);
        
        $this->activityLog->log('create', 'team', $teamId, __('activity_create_team', ['name' => $_POST['name']]));
        
        flash('success', __('team_created') ?? 'Équipe créée.');
        redirect('/teams');
    }
    
    /**
     * Met à jour une équipe
     */
    public function updateTeam(int $id): void {
        AuthController::requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/teams');
        }
        
        if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
            flash('error', __('session_expired') ?? 'Session expirée.');
            redirect('/teams');
        }
        
        $team = $this->teamModel->find($id);
        
        if (!$team) {
            flash('error', __('team_not_found') ?? 'Équipe introuvable.');
            redirect('/teams');
        }
        
        if ($this->teamModel->nameExists($_POST['name'], $id)) {
            flash('error', __('team_name_exists') ?? 'Ce nom d\'équipe existe déjà.');
            redirect('/teams');
        }
        
        $this->teamModel->update($id, [
            'name' => sanitize($_POST['name']),
            'name_en' => !empty($_POST['name_en']) ? sanitize($_POST['name_en']) : null,
            'description' => sanitize($_POST['description'] ?? ''),
            'description_en' => !empty($_POST['description_en']) ? sanitize($_POST['description_en']) : null,
            'color' => $_POST['color'] ?? '#3B82F6'
        ]);
        
        $this->activityLog->log('update', 'team', $id, __('activity_update_team') ?? 'Modification de l\'équipe');
        
        flash('success', __('team_updated') ?? 'Équipe mise à jour.');
        redirect('/teams');
    }
    
    /**
     * Supprime une équipe
     */
    public function deleteTeam(int $id): void {
        AuthController::requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/teams');
        }
        
        if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
            flash('error', __('session_expired') ?? 'Session expirée.');
            redirect('/teams');
        }
        
        $team = $this->teamModel->find($id);
        
        if (!$team) {
            flash('error', __('team_not_found') ?? 'Équipe introuvable.');
            redirect('/teams');
        }
        
        // Retirer les utilisateurs de cette équipe avant suppression
        $this->teamModel->removeUsersFromTeam($id);
        
        // Supprimer l'équipe
        $this->teamModel->delete($id);
        $this->activityLog->log('delete', 'team', $id, __('activity_delete_team', ['name' => $team['name']]));
        
        flash('success', __('team_deleted') ?? 'Équipe supprimée.');
        redirect('/teams');
    }
}
