<?php
/**
 * DocuFlow - Point d'entrée
 * Portail collaboratif de gestion documentaire
 */

// Chargement de la configuration
require_once __DIR__ . '/../src/Config/config.php';

// Autoloader simple
spl_autoload_register(function ($class) {
    // Convertit le namespace en chemin de fichier
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../src/';
    
    // Vérifie si la classe utilise le namespace App
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Récupère le nom de classe relatif
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Initialisation du routeur
$router = new App\Router();

// ==========================================
// Définition des routes
// ==========================================

// Authentification
$router->get('/login', 'App\Controllers\AuthController', 'showLogin');
$router->post('/login', 'App\Controllers\AuthController', 'login');
$router->get('/logout', 'App\Controllers\AuthController', 'logout');

// Dashboard
$router->get('/', 'App\Controllers\DashboardController', 'index');
$router->get('/dashboard', 'App\Controllers\DashboardController', 'index');
$router->get('/search', 'App\Controllers\DashboardController', 'search');
$router->get('/activity', 'App\Controllers\DashboardController', 'activity');
$router->post('/admin/reset-all', 'App\Controllers\DashboardController', 'resetAll');

// API Notifications
$router->get('/api/notifications', 'App\Controllers\DashboardController', 'getNotifications');
$router->post('/api/notifications/read', 'App\Controllers\DashboardController', 'markNotificationRead');
$router->post('/api/notifications/read-all', 'App\Controllers\DashboardController', 'markAllNotificationsRead');
$router->get('/api/notifications/poll', 'App\Controllers\DashboardController', 'pollNotifications');

// Documents
$router->get('/documents', 'App\Controllers\DocumentController', 'index');
$router->get('/documents/create', 'App\Controllers\DocumentController', 'create');
$router->post('/documents', 'App\Controllers\DocumentController', 'store');
$router->post('/documents/upload-ajax', 'App\Controllers\DocumentController', 'uploadAjax'); // Upload AJAX multiple
$router->get('/documents/{id}', 'App\Controllers\DocumentController', 'show');
$router->get('/documents/{id}/edit', 'App\Controllers\DocumentController', 'edit');
$router->post('/documents/{id}', 'App\Controllers\DocumentController', 'update');
$router->post('/documents/{id}/delete', 'App\Controllers\DocumentController', 'delete');

// API Documents - Zones
$router->get('/api/zones/{id}', 'App\Controllers\DocumentController', 'getZone');
$router->post('/api/zones/{id}/update', 'App\Controllers\DocumentController', 'updateZone');
$router->post('/api/zones', 'App\Controllers\DocumentController', 'createZone');
$router->post('/api/zones/{id}/delete', 'App\Controllers\DocumentController', 'deleteZone');
$router->get('/api/documents/{id}/zones', 'App\Controllers\DocumentController', 'getZones');

// API Documents - Liaisons
$router->post('/api/links', 'App\Controllers\DocumentController', 'createLink');
$router->post('/api/links/{id}/delete', 'App\Controllers\DocumentController', 'deleteLink');
$router->get('/api/documents/{id}/links', 'App\Controllers\DocumentController', 'getLinks');

// API Documents - Annotations
$router->post('/api/annotations', 'App\Controllers\DocumentController', 'createAnnotation');
$router->post('/api/annotations/{id}/resolve', 'App\Controllers\DocumentController', 'resolveAnnotation');
$router->post('/api/annotations/{id}/delete', 'App\Controllers\DocumentController', 'deleteAnnotation');

// API Documents - OCR / Contenu
$router->post('/api/documents/content', 'App\Controllers\DocumentController', 'savePageContent');
$router->get('/api/search/content', 'App\Controllers\DocumentController', 'searchContent');

// API Notifications - Mention
$router->post('/api/notifications/mention', 'App\Controllers\DocumentController', 'sendMention');

// Utilisateurs
$router->get('/users', 'App\Controllers\UserController', 'index');
$router->get('/users/create', 'App\Controllers\UserController', 'create');
$router->post('/users', 'App\Controllers\UserController', 'store');
$router->get('/users/{id}', 'App\Controllers\UserController', 'show');
$router->get('/users/{id}/edit', 'App\Controllers\UserController', 'edit');
$router->post('/users/{id}', 'App\Controllers\UserController', 'update');
$router->post('/users/{id}/password', 'App\Controllers\UserController', 'changePassword');
$router->post('/users/{id}/deactivate', 'App\Controllers\UserController', 'deactivate');
$router->post('/users/{id}/delete', 'App\Controllers\UserController', 'delete');
$router->get('/profile', 'App\Controllers\UserController', 'profile');

// Équipes
$router->get('/teams', 'App\Controllers\UserController', 'teams');
$router->post('/teams', 'App\Controllers\UserController', 'createTeam');
$router->post('/teams/{id}', 'App\Controllers\UserController', 'updateTeam');
$router->post('/teams/{id}/delete', 'App\Controllers\UserController', 'deleteTeam');

// Rôles et permissions (admin uniquement)
$router->get('/roles', 'App\Controllers\RoleController', 'index');
$router->get('/roles/create', 'App\Controllers\RoleController', 'create');
$router->post('/roles', 'App\Controllers\RoleController', 'store');
$router->get('/roles/{id}', 'App\Controllers\RoleController', 'show');
$router->get('/roles/{id}/edit', 'App\Controllers\RoleController', 'edit');
$router->post('/roles/{id}', 'App\Controllers\RoleController', 'update');
$router->post('/roles/{id}/delete', 'App\Controllers\RoleController', 'delete');

// API Chat en temps réel
$router->get('/api/chat/messages', 'App\Controllers\ChatController', 'getMessages');
$router->post('/api/chat/send', 'App\Controllers\ChatController', 'sendMessage');
$router->post('/api/chat/delete', 'App\Controllers\ChatController', 'deleteMessage');
$router->get('/api/chat/online', 'App\Controllers\ChatController', 'getOnlineUsers');
$router->get('/api/chat/unread', 'App\Controllers\ChatController', 'getUnreadCount');
$router->get('/api/chat/channels', 'App\Controllers\ChatController', 'getChannels');

// ==========================================
// API Sync documents temps réel
// ==========================================
$router->get('/api/documents/sync', 'App\Controllers\DocumentSyncController', 'getUpdates');
$router->get('/api/documents/viewers', 'App\Controllers\DocumentSyncController', 'getViewers');
$router->post('/api/documents/leave', 'App\Controllers\DocumentSyncController', 'leaveDocument');

// Changement de langue
$router->get('/lang', 'App\Controllers\LanguageController', 'change');

// Résolution de la route
$router->resolve();
