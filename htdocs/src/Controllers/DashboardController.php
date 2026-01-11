<?php
/**
 * DocuFlow - Contrôleur Dashboard
 * Avec fonction de réinitialisation complète
 */

namespace App\Controllers;

use App\Models\Document;
use App\Models\DocumentLink;
use App\Models\Annotation;
use App\Models\Team;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification;

class DashboardController {
    private Document $documentModel;
    private DocumentLink $linkModel;
    private Annotation $annotationModel;
    private Team $teamModel;
    private User $userModel;
    private ActivityLog $activityLog;
    private Notification $notificationModel;
    
    public function __construct() {
        $this->documentModel = new Document();
        $this->linkModel = new DocumentLink();
        $this->annotationModel = new Annotation();
        $this->teamModel = new Team();
        $this->userModel = new User();
        $this->activityLog = new ActivityLog();
        $this->notificationModel = new Notification();
    }
    
    /**
     * Page d'accueil du dashboard
     */
    public function index(): void {
        AuthController::requireAuth();
        
        // Statistiques globales
        $stats = [
            'documents' => $this->documentModel->getStats(),
            'links' => $this->linkModel->getStats(),
            'unresolved_annotations' => $this->annotationModel->countUnresolved(),
            'users' => $this->userModel->countActive()
        ];
        
        // Documents récents
        $recentDocuments = $this->documentModel->getRecent(8);
        
        // Liaisons récentes
        $recentLinks = $this->linkModel->getRecent(5);
        
        // Activité récente
        $recentActivity = $this->activityLog->getRecent(10);
        
        // Équipes avec stats
        $teams = $this->teamModel->allWithMemberCount();
        
        // Notifications non lues
        $unreadNotifications = $this->notificationModel->countUnread(currentUserId());
        
        // Statistiques d'activité pour le graphique
        $activityStats = $this->activityLog->getStats(30);
        
        require __DIR__ . '/../Views/pages/dashboard.php';
    }
    
    /**
     * Page de recherche
     */
    public function search(): void {
        AuthController::requireAuth();
        
        $query = sanitize($_GET['q'] ?? '');
        $results = [];
        
        if (!empty($query)) {
            // Recherche dans les documents (métadonnées)
            $docResults = $this->documentModel->getAllPaginated(1, 50, ['search' => $query]);
            $results['documents'] = $docResults['data'];
            
            // Recherche full-text dans le contenu
            $results['content'] = $this->documentModel->searchContent($query);
        }
        
        require __DIR__ . '/../Views/pages/search.php';
    }
    
    /**
     * Page d'historique / activité
     */
    public function activity(): void {
        AuthController::requireAuth();
        
        $page = (int) ($_GET['page'] ?? 1);
        $filters = [
            'user_id' => $_GET['user_id'] ?? null,
            'action' => $_GET['action'] ?? null,
            'entity_type' => $_GET['entity_type'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null
        ];
        
        $activities = $this->activityLog->getFiltered(array_filter($filters), $page, 30);
        $users = $this->userModel->allWithTeam();
        
        require __DIR__ . '/../Views/pages/activity.php';
    }
    
    /**
     * Réinitialisation complète - Supprime toutes les données de session de travail
     * (documents, zones, annotations, activités, chat) pour TOUS les utilisateurs
     */
    public function resetAll(): void {
        AuthController::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/dashboard');
        }
        
        if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
            flash('error', __('session_expired') ?? 'Session expirée.');
            redirect('/dashboard');
        }
        
        // Vérification du code de confirmation
        $confirmCode = $_POST['confirm_code'] ?? '';
        if ($confirmCode !== 'ERASE-ALL') {
            flash('error', __('reset_invalid_code') ?? 'Code de confirmation invalide.');
            redirect('/dashboard');
        }
        
        try {
            $db = \Database::getInstance();
            
            // Désactiver les vérifications de clés étrangères temporairement
            $db->exec("SET FOREIGN_KEY_CHECKS = 0");
            
            // Liste des tables à vider (dans l'ordre)
            $tables = [
                'chat_messages',      // Messages chat
                'notifications',      // Notifications
                'activity_log',       // Historique activité
                'annotations',        // Annotations
                'document_links',     // Liaisons
                'document_zones',     // Zones
                'document_pages',     // Contenu pages (peut ne pas exister)
            ];
            
            // Vider chaque table (ignorer si elle n'existe pas)
            foreach ($tables as $table) {
                try {
                    $db->exec("DELETE FROM `{$table}`");
                } catch (\Exception $e) {
                    // Table n'existe pas, on continue
                }
            }
            
            // Supprimer les fichiers physiques des documents AVANT de vider la table
            $this->deleteAllDocumentFiles();
            
            // Vider la table documents
            try {
                $db->exec("DELETE FROM documents");
            } catch (\Exception $e) {
                // Ignorer l'erreur
            }
            
            // Réactiver les vérifications de clés étrangères
            $db->exec("SET FOREIGN_KEY_CHECKS = 1");
            
            // Réinitialiser les auto-increments (ignorer les erreurs)
            $autoIncrementTables = ['documents', 'document_zones', 'document_links', 'annotations', 'activity_log', 'notifications', 'chat_messages'];
            foreach ($autoIncrementTables as $table) {
                try {
                    $db->exec("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
                } catch (\Exception $e) {
                    // Table n'existe pas, on continue
                }
            }
            
            // Enregistrer cette action (nouvelle entrée dans le log)
            try {
                $this->activityLog->log(
                    'reset_all', 
                    'system', 
                    null, 
                    __('activity_reset_all') ?? 'Réinitialisation complète du système'
                );
            } catch (\Exception $e) {
                // Ignorer si ça échoue
            }
            
            flash('success', __('reset_success') ?? 'Toutes les données ont été supprimées avec succès.');
            
        } catch (\Exception $e) {
            flash('error', (__('reset_error') ?? 'Une erreur est survenue lors de la réinitialisation.') . ' ' . $e->getMessage());
        }
        
        redirect('/dashboard');
    }
    
    /**
     * Supprime tous les fichiers physiques des documents
     */
    private function deleteAllDocumentFiles(): void {
        $uploadDir = UPLOAD_DIR;
        
        if (is_dir($uploadDir)) {
            $files = glob($uploadDir . '*');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitkeep' && basename($file) !== 'index.html') {
                    @unlink($file);
                }
            }
        }
    }
    
    /**
     * API: Récupère les notifications
     */
    public function getNotifications(): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $notifications = $this->notificationModel->getByUser(currentUserId(), 20);
        $unreadCount = $this->notificationModel->countUnread(currentUserId());
        
        // Traduire les notifications
        $notifications = array_map(function($notif) {
            $notif['title'] = $this->translateNotificationTitle($notif['title'] ?? '');
            $notif['message'] = $this->translateNotificationMessage($notif['message'] ?? '');
            return $notif;
        }, $notifications);
        
        echo json_encode([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }
    
    /**
     * Traduit le titre d'une notification
     */
    private function translateNotificationTitle(string $title): string {
        $titleMap = [
            'Nouvelle liaison' => __('notif_title_new_link'),
            'Nouveau document' => __('notif_title_new_document'),
            'Nouvelle annotation' => __('notif_title_new_annotation'),
            'Mention' => __('notif_title_mention'),
            'Document mis à jour' => __('notif_title_document_updated'),
            'Réinitialisation système' => __('notif_title_system_reset'),
        ];
        
        return $titleMap[$title] ?? $title;
    }
    
    /**
     * Traduit le message d'une notification
     */
    private function translateNotificationMessage(string $message): string {
        // Patterns français -> anglais
        $patterns = [
            '/^(.+) a créé une liaison entre documents$/' => '$1 ' . __('notif_desc_created_link'),
            '/^(.+) a ajouté un nouveau document: (.+)$/' => '$1 ' . __('notif_desc_added_document') . ': $2',
            '/^(.+) a ajouté une annotation$/' => '$1 ' . __('notif_desc_added_annotation'),
            '/^(.+) vous a mentionné$/' => '$1 ' . __('notif_desc_mentioned_you'),
            '/^(.+) a mis à jour un document$/' => '$1 ' . __('notif_desc_updated_document'),
        ];
        
        foreach ($patterns as $pattern => $replacement) {
            if (preg_match($pattern, $message)) {
                return preg_replace($pattern, $replacement, $message);
            }
        }
        
        return $message;
    }
    
    /**
     * API: Marque une notification comme lue
     */
    public function markNotificationRead(): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $id = (int) ($_POST['id'] ?? 0);
        
        if ($id) {
            $this->notificationModel->markAsRead($id);
        }
        
        echo json_encode(['success' => true]);
    }
    
    /**
     * API: Marque toutes les notifications comme lues
     */
    public function markAllNotificationsRead(): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $this->notificationModel->markAllAsRead(currentUserId());
        
        echo json_encode(['success' => true]);
    }
    
    /**
     * API: Poll pour nouvelles notifications
     */
    public function pollNotifications(): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $since = $_GET['since'] ?? null;
        $notifications = [];
        
        if ($since) {
            $notifications = $this->notificationModel->getNewSince(currentUserId(), $since);
            
            // Traduire les notifications
            $notifications = array_map(function($notif) {
                $notif['title'] = $this->translateNotificationTitle($notif['title'] ?? '');
                $notif['message'] = $this->translateNotificationMessage($notif['message'] ?? '');
                return $notif;
            }, $notifications);
        }
        
        $unreadCount = $this->notificationModel->countUnread(currentUserId());
        
        echo json_encode([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}
