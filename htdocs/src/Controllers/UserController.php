<?php
/**
 * DocuFlow - Contrôleur Utilisateurs
 * Gestion des utilisateurs et équipes
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
            flash('error', 'Session expirée.');
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
            flash('error', 'Cet email est déjà utilisé.');
            redirect('/users/create');
        }
        
        if ($this->userModel->usernameExists($_POST['username'])) {
            flash('error', 'Ce nom d\'utilisateur est déjà utilisé.');
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
        
        $this->activityLog->log('create_user', 'user', $userId, 'Création de l\'utilisateur: ' . $_POST['username']);
        
        flash('success', 'Utilisateur créé avec succès.');
        redirect('/users');
    }
    
    /**
     * Affiche le profil d'un utilisateur
     */
    public function show(int $id): void {
        AuthController::requireAuth();
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            flash('error', 'Utilisateur introuvable.');
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
            flash('error', 'Accès refusé.');
            redirect('/dashboard');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            flash('error', 'Utilisateur introuvable.');
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
            flash('error', 'Accès refusé.');
            redirect('/dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/users/' . $id . '/edit');
        }
        
        if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
            flash('error', 'Session expirée.');
            redirect('/users/' . $id . '/edit');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            flash('error', 'Utilisateur introuvable.');
            redirect('/users');
        }
        
        // Vérification unicité email
        if ($this->userModel->emailExists($_POST['email'], $id)) {
            flash('error', 'Cet email est déjà utilisé.');
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
            $data['is_active'] = isset($_POST['is_active']) ? 1 : 0;
        }
        
        $this->userModel->update($id, $data);
        $this->activityLog->log('update_user', 'user', $id, 'Modification du profil');
        
        // Si l'utilisateur modifie son propre profil, met à jour la session
        if (currentUserId() === $id) {
            $_SESSION['full_name'] = $data['first_name'] . ' ' . $data['last_name'];
        }
        
        flash('success', 'Profil mis à jour.');
        redirect('/users/' . $id);
    }
    
    /**
     * Change le mot de passe
     */
    public function changePassword(int $id): void {
        AuthController::requireAuth();
        
        if (!isAdmin() && currentUserId() !== $id) {
            flash('error', 'Accès refusé.');
            redirect('/dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/users/' . $id . '/edit');
        }
        
        if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
            flash('error', 'Session expirée.');
            redirect('/users/' . $id . '/edit');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            flash('error', 'Utilisateur introuvable.');
            redirect('/users');
        }
        
        // Si ce n'est pas l'admin, vérifier l'ancien mot de passe
        if (!isAdmin() || currentUserId() === $id) {
            if (!password_verify($_POST['current_password'] ?? '', $user['password'])) {
                flash('error', 'Mot de passe actuel incorrect.');
                redirect('/users/' . $id . '/edit');
            }
        }
        
        // Validation nouveau mot de passe
        if (strlen($_POST['new_password'] ?? '') < PASSWORD_MIN_LENGTH) {
            flash('error', 'Le nouveau mot de passe doit contenir au moins ' . PASSWORD_MIN_LENGTH . ' caractères.');
            redirect('/users/' . $id . '/edit');
        }
        
        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            flash('error', 'Les mots de passe ne correspondent pas.');
            redirect('/users/' . $id . '/edit');
        }
        
        $this->userModel->changePassword($id, $_POST['new_password']);
        $this->activityLog->log('update_password', 'user', $id, 'Changement de mot de passe');
        
        flash('success', 'Mot de passe modifié.');
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
            flash('error', 'Session expirée.');
            redirect('/users');
        }
        
        // On ne peut pas se désactiver soi-même
        if (currentUserId() === $id) {
            flash('error', 'Vous ne pouvez pas désactiver votre propre compte.');
            redirect('/users');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            flash('error', 'Utilisateur introuvable.');
            redirect('/users');
        }
        
        $username = $user['username'];
        
        // Désactivation de l'utilisateur
        $this->userModel->update($id, ['is_active' => 0]);
        $this->activityLog->log('deactivate', 'user', $id, 'Désactivation de l\'utilisateur: ' . $username);
        
        flash('success', 'Utilisateur "' . $username . '" désactivé.');
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
            flash('error', 'Session expirée.');
            redirect('/users');
        }
        
        // On ne peut pas se supprimer soi-même
        if (currentUserId() === $id) {
            flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            redirect('/users');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            flash('error', 'Utilisateur introuvable.');
            redirect('/users');
        }
        
        $username = $user['username'];
        
        // Suppression réelle de l'utilisateur
        $this->userModel->delete($id);
        $this->activityLog->log('delete', 'user', $id, 'Suppression de l\'utilisateur: ' . $username);
        
        flash('success', 'Utilisateur supprimé.');
        redirect('/users');
    }
    
    /**
     * Page Mon Profil
     */
    public function profile(): void {
        AuthController::requireAuth();
        
        $user = currentUser();
        $team = $user['team_id'] ? $this->teamModel->find($user['team_id']) : null;
        $activity = $this->activityLog->getByUser(currentUserId(), 20);
        
        require __DIR__ . '/../Views/pages/profile.php';
    }
    
    /**
     * Valide les données utilisateur
     */
    private function validateUserData(array $data, bool $isUpdate = false): array {
        $errors = [];
        
        if (empty($data['username']) && !$isUpdate) {
            $errors[] = 'Le nom d\'utilisateur est requis.';
        } elseif (!$isUpdate && !preg_match('/^[a-zA-Z0-9_]{3,50}$/', $data['username'])) {
            $errors[] = 'Le nom d\'utilisateur doit contenir entre 3 et 50 caractères (lettres, chiffres, underscore).';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'L\'email est requis.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'L\'email n\'est pas valide.';
        }
        
        if (!$isUpdate) {
            if (empty($data['password'])) {
                $errors[] = 'Le mot de passe est requis.';
            } elseif (strlen($data['password']) < PASSWORD_MIN_LENGTH) {
                $errors[] = 'Le mot de passe doit contenir au moins ' . PASSWORD_MIN_LENGTH . ' caractères.';
            }
        }
        
        if (empty($data['first_name'])) {
            $errors[] = 'Le prénom est requis.';
        }
        
        if (empty($data['last_name'])) {
            $errors[] = 'Le nom est requis.';
        }
        
        return $errors;
    }
    
    // ==========================================
    // Gestion des équipes
    // ==========================================
    
    /**
     * Liste des équipes
     */
    public function teams(): void {
        AuthController::requireAdmin();
        
        $teams = $this->teamModel->allWithMemberCount();
        
        foreach ($teams as &$team) {
            $team['stats'] = $this->teamModel->getStats($team['id']);
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
            flash('error', 'Session expirée.');
            redirect('/teams');
        }
        
        if (empty($_POST['name'])) {
            flash('error', 'Le nom de l\'équipe est requis.');
            redirect('/teams');
        }
        
        if ($this->teamModel->nameExists($_POST['name'])) {
            flash('error', 'Ce nom d\'équipe existe déjà.');
            redirect('/teams');
        }
        
        $teamId = $this->teamModel->create([
            'name' => sanitize($_POST['name']),
            'description' => sanitize($_POST['description'] ?? ''),
            'color' => $_POST['color'] ?? '#3B82F6'
        ]);
        
        $this->activityLog->log('create', 'team', $teamId, 'Création de l\'équipe: ' . $_POST['name']);
        
        flash('success', 'Équipe créée.');
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
            flash('error', 'Session expirée.');
            redirect('/teams');
        }
        
        $team = $this->teamModel->find($id);
        
        if (!$team) {
            flash('error', 'Équipe introuvable.');
            redirect('/teams');
        }
        
        if ($this->teamModel->nameExists($_POST['name'], $id)) {
            flash('error', 'Ce nom d\'équipe existe déjà.');
            redirect('/teams');
        }
        
        $this->teamModel->update($id, [
            'name' => sanitize($_POST['name']),
            'description' => sanitize($_POST['description'] ?? ''),
            'color' => $_POST['color'] ?? '#3B82F6'
        ]);
        
        flash('success', 'Équipe mise à jour.');
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
            flash('error', 'Session expirée.');
            redirect('/teams');
        }
        
        $team = $this->teamModel->find($id);
        
        if (!$team) {
            flash('error', 'Équipe introuvable.');
            redirect('/teams');
        }
        
        $this->teamModel->delete($id);
        $this->activityLog->log('delete', 'team', $id, 'Suppression de l\'équipe: ' . $team['name']);
        
        flash('success', 'Équipe supprimée.');
        redirect('/teams');
    }
}
