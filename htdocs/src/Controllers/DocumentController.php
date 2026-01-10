<?php
/**
 * DocuFlow - Contrôleur Documents
 * Gestion des documents PDF
 */

namespace App\Controllers;

use App\Models\Document;
use App\Models\DocumentZone;
use App\Models\DocumentLink;
use App\Models\Annotation;
use App\Models\Team;
use App\Models\ActivityLog;
use App\Models\Notification;

class DocumentController {
    private Document $documentModel;
    private DocumentZone $zoneModel;
    private DocumentLink $linkModel;
    private Annotation $annotationModel;
    private Team $teamModel;
    private ActivityLog $activityLog;
    private Notification $notificationModel;
    
    public function __construct() {
        $this->documentModel = new Document();
        $this->zoneModel = new DocumentZone();
        $this->linkModel = new DocumentLink();
        $this->annotationModel = new Annotation();
        $this->teamModel = new Team();
        $this->activityLog = new ActivityLog();
        $this->notificationModel = new Notification();
    }
    
    /**
     * Liste des documents
     */
    public function index(): void {
        AuthController::requireAuth();
        
        $page = (int) ($_GET['page'] ?? 1);
        $filters = [
            'type' => $_GET['type'] ?? null,
            'team_id' => $_GET['team_id'] ?? null,
            'search' => $_GET['search'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null
        ];
        
        $documents = $this->documentModel->getAllPaginated($page, 20, array_filter($filters));
        $teams = $this->teamModel->all();
        $documentTypes = Document::TYPES;
        
        require __DIR__ . '/../Views/pages/documents/index.php';
    }
    
    /**
     * Affiche un document
     */
    public function show(int $id): void {
        AuthController::requireAuth();
        
        $document = $this->documentModel->findWithDetails($id);
        
        if (!$document) {
            flash('error', 'Document introuvable.');
            redirect('/documents');
        }
        
        // Documents disponibles pour le mapping
        $availableDocuments = $this->documentModel->all();
        
        // Historique du document
        $history = $this->activityLog->getByEntity('document', $id);
        
        require __DIR__ . '/../Views/pages/documents/show.php';
    }
    
    /**
     * Formulaire d'upload
     */
    public function create(): void {
        AuthController::requireAuth();
        
        $teams = $this->teamModel->all();
        $documentTypes = Document::TYPES;
        
        require __DIR__ . '/../Views/pages/documents/create.php';
    }
    
    /**
     * Upload d'un document (formulaire standard)
     */
    public function store(): void {
        AuthController::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/documents/create');
        }
        
        if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
            flash('error', 'Session expirée.');
            redirect('/documents/create');
        }
        
        // Validation du fichier
        if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'Erreur lors de l\'upload du fichier.');
            redirect('/documents/create');
        }
        
        $file = $_FILES['document'];
        
        // Vérification de l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            flash('error', 'Seuls les fichiers PDF sont autorisés.');
            redirect('/documents/create');
        }
        
        // Vérification de la taille
        if ($file['size'] > MAX_FILE_SIZE) {
            flash('error', 'Le fichier est trop volumineux (max ' . formatFileSize(MAX_FILE_SIZE) . ').');
            redirect('/documents/create');
        }
        
        // Vérification du type MIME
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if ($mimeType !== 'application/pdf') {
            flash('error', 'Le fichier doit être un PDF valide.');
            redirect('/documents/create');
        }
        
        // Génération du nom de fichier
        $filename = $this->documentModel->generateFilename($file['name']);
        $uploadPath = UPLOAD_DIR . $filename;
        
        // Création du dossier si nécessaire
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }
        
        // Déplacement du fichier
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            flash('error', 'Erreur lors de l\'enregistrement du fichier.');
            redirect('/documents/create');
        }
        
        // Création de l'enregistrement
        $documentId = $this->documentModel->create([
            'user_id' => currentUserId(),
            'team_id' => !empty($_POST['team_id']) ? (int) $_POST['team_id'] : null,
            'title' => sanitize($_POST['title'] ?? pathinfo($file['name'], PATHINFO_FILENAME)),
            'description' => sanitize($_POST['description'] ?? ''),
            'filename' => $filename,
            'original_name' => $file['name'],
            'file_size' => $file['size'],
            'file_path' => 'uploads/' . $filename,
            'mime_type' => $mimeType,
            'document_type' => $_POST['document_type'] ?? 'other',
            'reference_number' => sanitize($_POST['reference_number'] ?? ''),
            'document_date' => !empty($_POST['document_date']) ? $_POST['document_date'] : null,
            'total_amount' => !empty($_POST['total_amount']) ? (float) $_POST['total_amount'] : null,
            'currency' => $_POST['currency'] ?? 'EUR'
        ]);
        
        // Log de l'activité (avec clé de traduction)
        $this->activityLog->log('upload', 'document', $documentId, __('activity_upload_document', ['name' => $file['name']]));
        
        // Notification aux autres utilisateurs
        $this->notificationModel->notifyAllExcept(
            currentUserId(),
            'Nouveau document',
            $_SESSION['full_name'] . ' a ajouté un nouveau document: ' . sanitize($_POST['title'] ?? $file['name']),
            'document',
            '/documents/' . $documentId
        );
        
        flash('success', 'Document uploadé avec succès.');
        redirect('/documents/' . $documentId);
    }
    
    /**
     * Upload AJAX d'un document (pour l'import multiple)
     * Route: POST /documents/upload-ajax
     */
    public function uploadAjax(): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        try {
            // Vérification CSRF
            if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
                echo json_encode(['success' => false, 'error' => __('session_expired') ?? 'Session expirée']);
                return;
            }
            
            // Validation du fichier
            if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => __('file_too_large') ?? 'Fichier trop volumineux',
                    UPLOAD_ERR_FORM_SIZE => __('file_too_large') ?? 'Fichier trop volumineux',
                    UPLOAD_ERR_PARTIAL => __('upload_partial') ?? 'Upload partiel',
                    UPLOAD_ERR_NO_FILE => __('no_file_selected') ?? 'Aucun fichier sélectionné',
                    UPLOAD_ERR_NO_TMP_DIR => __('upload_error') ?? 'Erreur d\'upload',
                    UPLOAD_ERR_CANT_WRITE => __('upload_error') ?? 'Erreur d\'upload',
                    UPLOAD_ERR_EXTENSION => __('upload_error') ?? 'Erreur d\'upload'
                ];
                $errorCode = $_FILES['document']['error'] ?? UPLOAD_ERR_NO_FILE;
                echo json_encode(['success' => false, 'error' => $errorMessages[$errorCode] ?? 'Erreur d\'upload']);
                return;
            }
            
            $file = $_FILES['document'];
            
            // Vérification de l'extension
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, ALLOWED_EXTENSIONS)) {
                echo json_encode(['success' => false, 'error' => __('pdf_only_alert') ?? 'Seuls les fichiers PDF sont autorisés']);
                return;
            }
            
            // Vérification de la taille
            if ($file['size'] > MAX_FILE_SIZE) {
                echo json_encode(['success' => false, 'error' => __('file_too_large') ?? 'Fichier trop volumineux']);
                return;
            }
            
            // Vérification du type MIME
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            if ($mimeType !== 'application/pdf') {
                echo json_encode(['success' => false, 'error' => __('pdf_only_alert') ?? 'Le fichier doit être un PDF valide']);
                return;
            }
            
            // Génération du nom de fichier unique
            $filename = $this->documentModel->generateFilename($file['name']);
            $uploadPath = UPLOAD_DIR . $filename;
            
            // Création du dossier si nécessaire
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0755, true);
            }
            
            // Déplacement du fichier
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                echo json_encode(['success' => false, 'error' => __('upload_error') ?? 'Erreur lors de l\'enregistrement']);
                return;
            }
            
            // Titre: utiliser le paramètre ou le nom du fichier
            $title = !empty($_POST['title']) 
                ? sanitize($_POST['title']) 
                : pathinfo($file['name'], PATHINFO_FILENAME);
            
            // Création de l'enregistrement en base
            $documentId = $this->documentModel->create([
                'user_id' => currentUserId(),
                'team_id' => !empty($_POST['team_id']) ? (int) $_POST['team_id'] : null,
                'title' => $title,
                'description' => sanitize($_POST['description'] ?? ''),
                'filename' => $filename,
                'original_name' => $file['name'],
                'file_size' => $file['size'],
                'file_path' => 'uploads/' . $filename,
                'mime_type' => $mimeType,
                'document_type' => $_POST['document_type'] ?? 'other',
                'reference_number' => sanitize($_POST['reference_number'] ?? ''),
                'document_date' => !empty($_POST['document_date']) ? $_POST['document_date'] : null,
                'total_amount' => !empty($_POST['total_amount']) ? (float) $_POST['total_amount'] : null,
                'currency' => $_POST['currency'] ?? 'EUR'
            ]);
            
            // Log de l'activité (avec clé de traduction)
            $this->activityLog->log('upload', 'document', $documentId, __('activity_upload_document', ['name' => $file['name']]));
            
            // Retour succès
            echo json_encode([
                'success' => true, 
                'document_id' => $documentId,
                'title' => $title,
                'filename' => $filename
            ]);
            
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Formulaire de modification
     */
    public function edit(int $id): void {
        AuthController::requireAuth();
        
        $document = $this->documentModel->find($id);
        
        if (!$document) {
            flash('error', 'Document introuvable.');
            redirect('/documents');
        }
        
        $teams = $this->teamModel->all();
        $documentTypes = Document::TYPES;
        
        require __DIR__ . '/../Views/pages/documents/edit.php';
    }
    
    /**
     * Met à jour un document
     */
    public function update(int $id): void {
        AuthController::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/documents/' . $id . '/edit');
        }
        
        if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
            flash('error', 'Session expirée.');
            redirect('/documents/' . $id . '/edit');
        }
        
        $document = $this->documentModel->find($id);
        
        if (!$document) {
            flash('error', 'Document introuvable.');
            redirect('/documents');
        }
        
        $this->documentModel->update($id, [
            'title' => sanitize($_POST['title'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'team_id' => !empty($_POST['team_id']) ? (int) $_POST['team_id'] : null,
            'document_type' => $_POST['document_type'] ?? 'other',
            'reference_number' => sanitize($_POST['reference_number'] ?? ''),
            'document_date' => !empty($_POST['document_date']) ? $_POST['document_date'] : null,
            'total_amount' => !empty($_POST['total_amount']) ? (float) $_POST['total_amount'] : null,
            'currency' => $_POST['currency'] ?? 'EUR'
        ]);
        
        $this->activityLog->log('update', 'document', $id, 'Modification du document');
        
        flash('success', 'Document mis à jour.');
        redirect('/documents/' . $id);
    }
    
    /**
     * Supprime un document
     */
    public function delete(int $id): void {
        AuthController::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/documents');
        }
        
        if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
            flash('error', 'Session expirée.');
            redirect('/documents');
        }
        
        $document = $this->documentModel->find($id);
        
        if (!$document) {
            flash('error', 'Document introuvable.');
            redirect('/documents');
        }
        
        $title = $document['title'];
        
        if ($this->documentModel->deleteWithFile($id)) {
            $this->activityLog->log('delete', 'document', $id, __('activity_delete_document', ['name' => $title]));
            flash('success', __('document_deleted') ?? 'Document supprimé.');
        } else {
            flash('error', 'Erreur lors de la suppression.');
        }
        
        redirect('/documents');
    }
    
    // ==========================================
    // API pour les zones
    // ==========================================
    
    /**
     * API: Crée une zone
     */
    public function createZone(): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Données invalides']);
            return;
        }
        
        $zoneId = $this->zoneModel->create([
            'document_id' => (int) $data['document_id'],
            'page_number' => (int) $data['page_number'],
            'x' => (float) $data['x'],
            'y' => (float) $data['y'],
            'width' => (float) $data['width'],
            'height' => (float) $data['height'],
            'label' => sanitize($data['label'] ?? ''),
            'extracted_text' => $data['extracted_text'] ?? null,
            'zone_type' => $data['zone_type'] ?? 'custom',
            'color' => $data['color'] ?? '#3B82F6',
            'description' => sanitize($data['description'] ?? ''),
            'created_by' => currentUserId()
        ]);
        
        $zone = $this->zoneModel->find($zoneId);
        
        $this->activityLog->log('create_zone', 'zone', $zoneId, 'Création de zone sur le document #' . $data['document_id']);
        
        echo json_encode(['success' => true, 'zone' => $zone]);
    }
    
    /**
     * API: Récupère une zone spécifique
     */
    public function getZone(int $id): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $zone = $this->zoneModel->find($id);
        
        if (!$zone) {
            echo json_encode(['success' => false, 'error' => 'Zone introuvable']);
            return;
        }
        
        echo json_encode(['success' => true, 'zone' => $zone]);
    }
    
    /**
     * API: Met à jour une zone
     */
    public function updateZone(int $id): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $zone = $this->zoneModel->find($id);
        
        if (!$zone) {
            echo json_encode(['success' => false, 'error' => 'Zone introuvable']);
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Données invalides']);
            return;
        }
        
        // Prépare les données à mettre à jour
        $updateData = [];
        
        if (isset($data['label'])) {
            $updateData['label'] = sanitize($data['label']);
        }
        
        if (isset($data['zone_type'])) {
            $updateData['zone_type'] = $data['zone_type'];
        }
        
        if (isset($data['color'])) {
            if (preg_match('/^#[0-9A-Fa-f]{6}$/', $data['color'])) {
                $updateData['color'] = $data['color'];
            }
        }
        
        if (isset($data['description'])) {
            $updateData['description'] = sanitize($data['description']);
        }
        
        if (isset($data['extracted_text'])) {
            $updateData['extracted_text'] = $data['extracted_text'];
        }
        
        if (empty($updateData)) {
            echo json_encode(['success' => false, 'error' => 'Aucune donnée à mettre à jour']);
            return;
        }
        
        $this->zoneModel->update($id, $updateData);
        
        $updatedZone = $this->zoneModel->find($id);
        
        $this->activityLog->log('update_zone', 'zone', $id, 'Modification de zone');
        
        echo json_encode(['success' => true, 'zone' => $updatedZone]);
    }
    
    /**
     * API: Supprime une zone
     */
    public function deleteZone(int $id): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $zone = $this->zoneModel->find($id);
        
        if (!$zone) {
            echo json_encode(['success' => false, 'error' => 'Zone introuvable']);
            return;
        }
        
        $this->zoneModel->deleteWithLinks($id);
        $this->activityLog->log('delete', 'zone', $id, 'Suppression de zone');
        
        echo json_encode(['success' => true]);
    }
    
    /**
     * API: Récupère les zones d'un document
     */
    public function getZones(int $documentId): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $zones = $this->documentModel->getZones($documentId);
        
        echo json_encode(['success' => true, 'zones' => $zones]);
    }
    
    // ==========================================
    // API pour les liaisons
    // ==========================================
    
    /**
     * API: Crée une liaison
     */
    public function createLink(): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || empty($data['source_zone_id']) || empty($data['target_document_id'])) {
            echo json_encode(['success' => false, 'error' => 'Données invalides']);
            return;
        }
        
        if ($this->linkModel->linkExists((int) $data['source_zone_id'], (int) $data['target_document_id'])) {
            echo json_encode(['success' => false, 'error' => 'Cette liaison existe déjà']);
            return;
        }
        
        $linkId = $this->linkModel->create([
            'source_zone_id' => (int) $data['source_zone_id'],
            'target_document_id' => (int) $data['target_document_id'],
            'target_zone_id' => !empty($data['target_zone_id']) ? (int) $data['target_zone_id'] : null,
            'link_type' => $data['link_type'] ?? 'reference',
            'description' => sanitize($data['description'] ?? ''),
            'created_by' => currentUserId()
        ]);
        
        $link = $this->linkModel->findWithDetails($linkId);
        
        $this->activityLog->log('create_link', 'link', $linkId, 'Création de liaison');
        
        $this->notificationModel->notifyAllExcept(
            currentUserId(),
            'Nouvelle liaison',
            $_SESSION['full_name'] . ' a créé une liaison entre documents',
            'link',
            '/documents/' . $data['target_document_id']
        );
        
        echo json_encode(['success' => true, 'link' => $link]);
    }
    
    /**
     * API: Supprime une liaison
     */
    public function deleteLink(int $id): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $link = $this->linkModel->find($id);
        
        if (!$link) {
            echo json_encode(['success' => false, 'error' => 'Liaison introuvable']);
            return;
        }
        
        $this->linkModel->delete($id);
        $this->activityLog->log('delete_link', 'link', $id, 'Suppression de liaison');
        
        echo json_encode(['success' => true]);
    }
    
    /**
     * API: Récupère les liaisons d'un document
     */
    public function getLinks(int $documentId): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $linksFrom = $this->documentModel->getLinksFrom($documentId);
        $linksTo = $this->documentModel->getLinksTo($documentId);
        
        echo json_encode([
            'success' => true, 
            'links_from' => $linksFrom,
            'links_to' => $linksTo
        ]);
    }
    
    // ==========================================
    // API pour les annotations
    // ==========================================
    
    /**
     * API: Crée une annotation
     */
    public function createAnnotation(): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || empty($data['document_id']) || empty($data['content'])) {
            echo json_encode(['success' => false, 'error' => 'Données invalides']);
            return;
        }
        
        $annotationId = $this->annotationModel->create([
            'document_id' => (int) $data['document_id'],
            'zone_id' => !empty($data['zone_id']) ? (int) $data['zone_id'] : null,
            'user_id' => currentUserId(),
            'content' => sanitize($data['content']),
            'annotation_type' => $data['annotation_type'] ?? 'comment',
            'color' => $data['color'] ?? '#FFEB3B'
        ]);
        
        $annotation = $this->annotationModel->findWithDetails($annotationId);
        
        $this->activityLog->log('create_annotation', 'annotation', $annotationId, 'Ajout d\'annotation');
        
        echo json_encode(['success' => true, 'annotation' => $annotation]);
    }
    
    /**
     * API: Résout une annotation
     */
    public function resolveAnnotation(int $id): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $this->annotationModel->resolve($id);
        $this->activityLog->log('resolve_annotation', 'annotation', $id, 'Résolution d\'annotation');
        
        echo json_encode(['success' => true]);
    }
    
    /**
     * API: Supprime une annotation
     */
    public function deleteAnnotation(int $id): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $this->annotationModel->delete($id);
        
        echo json_encode(['success' => true]);
    }
    
    /**
     * API: Sauvegarde le contenu OCR d'une page
     */
    public function savePageContent(): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || empty($data['document_id']) || !isset($data['page_number']) || empty($data['content'])) {
            echo json_encode(['success' => false, 'error' => 'Données invalides']);
            return;
        }
        
        $this->documentModel->savePageContent(
            (int) $data['document_id'],
            (int) $data['page_number'],
            $data['content']
        );
        
        $this->documentModel->update((int) $data['document_id'], ['is_ocr_processed' => 1]);
        
        echo json_encode(['success' => true]);
    }
    
    /**
     * API: Recherche full-text
     */
    public function searchContent(): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $query = sanitize($_GET['q'] ?? '');
        
        if (strlen($query) < 3) {
            echo json_encode(['success' => false, 'error' => 'Requête trop courte']);
            return;
        }
        
        $results = $this->documentModel->searchContent($query);
        
        echo json_encode(['success' => true, 'results' => $results]);
    }
    
    /**
     * API: Envoie une notification de mention
     */
    public function sendMention(): void {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || empty($data['user_id']) || empty($data['document_id'])) {
            echo json_encode(['success' => false, 'error' => 'Données invalides']);
            return;
        }
        
        $document = $this->documentModel->find((int) $data['document_id']);
        if (!$document) {
            echo json_encode(['success' => false, 'error' => 'Document non trouvé']);
            return;
        }
        
        $zoneInfo = '';
        if (!empty($data['zone_id'])) {
            $zone = $this->zoneModel->find((int) $data['zone_id']);
            if ($zone) {
                $zoneInfo = ' sur la zone "' . ($zone['label'] ?: 'Zone ' . $zone['id']) . '" (page ' . $zone['page_number'] . ')';
            }
        }
        
        $senderName = $_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name'];
        $message = $senderName . ' vous a mentionné' . $zoneInfo . ' dans le document "' . $document['title'] . '"';
        
        if (!empty($data['message'])) {
            $message .= ' : "' . substr($data['message'], 0, 200) . '"';
        }
        
        $notificationId = $this->notificationModel->create([
            'user_id' => (int) $data['user_id'],
            'type' => 'mention',
            'title' => 'Nouvelle mention',
            'message' => $message,
            'link' => '/documents/' . $data['document_id'] . ($data['zone_id'] ? '#zone-' . $data['zone_id'] : '')
        ]);
        
        $this->activityLog->log(
            'mention',
            'document',
            (int) $data['document_id'],
            'Mention envoyée à l\'utilisateur #' . $data['user_id']
        );
        
        echo json_encode(['success' => true, 'notification_id' => $notificationId]);
    }
}
