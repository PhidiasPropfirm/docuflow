<?php
/**
 * DocuFlow - Contrôleur Dashboard
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
     * API: Récupère les notifications
     */
    public function getNotifications(): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $notifications = $this->notificationModel->getByUser(currentUserId(), 20);
        $unreadCount = $this->notificationModel->countUnread(currentUserId());
        
        echo json_encode([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
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
        
        $since = $_GET['since'] ?? date('Y-m-d H:i:s', strtotime('-1 minute'));
        
        $newNotifications = $this->notificationModel->getNewSince(currentUserId(), $since);
        $unreadCount = $this->notificationModel->countUnread(currentUserId());
        
        echo json_encode([
            'success' => true,
            'notifications' => $newNotifications,
            'unread_count' => $unreadCount,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}
