<?php
namespace App\Controllers;

use App\Models\Document;
use App\Models\Zone;
use App\Models\Annotation;
use App\Models\DocumentLink;

/**
 * Contrôleur API pour les mises à jour en temps réel des documents
 */
class DocumentSyncController {
    private Document $documentModel;
    private Zone $zoneModel;
    private Annotation $annotationModel;
    private DocumentLink $linkModel;
    
    public function __construct() {
        $this->documentModel = new Document();
        $this->zoneModel = new Zone();
        $this->annotationModel = new Annotation();
        $this->linkModel = new DocumentLink();
    }
    
    /**
     * Récupère les mises à jour d'un document depuis un timestamp
     */
    public function getUpdates(): void {
        AuthController::requireAuth();
        
        header('Content-Type: application/json');
        
        $documentId = (int)($_GET['document_id'] ?? 0);
        $since = $_GET['since'] ?? date('Y-m-d H:i:s', strtotime('-5 minutes'));
        
        if (!$documentId) {
            echo json_encode(['success' => false, 'error' => 'Document ID requis']);
            return;
        }
        
        try {
            $db = db();
            
            // Récupérer les zones modifiées/créées (table = document_zones)
            $stmt = $db->prepare("
                SELECT z.*, u.first_name, u.last_name 
                FROM document_zones z
                LEFT JOIN users u ON u.id = z.created_by
                WHERE z.document_id = ? 
                AND (z.created_at > ? OR z.updated_at > ?)
                ORDER BY z.created_at DESC
            ");
            $stmt->execute([$documentId, $since, $since]);
            $zones = $stmt->fetchAll();
            
            // Récupérer les annotations modifiées/créées
            $stmt = $db->prepare("
                SELECT a.*, u.first_name, u.last_name,
                       z.name as zone_name
                FROM annotations a
                JOIN document_zones z ON z.id = a.zone_id
                LEFT JOIN users u ON u.id = a.user_id
                WHERE z.document_id = ?
                AND a.created_at > ?
                ORDER BY a.created_at DESC
            ");
            $stmt->execute([$documentId, $since]);
            $annotations = $stmt->fetchAll();
            
            // Récupérer les liaisons modifiées/créées
            $stmt = $db->prepare("
                SELECT dl.*, 
                       d1.title as source_title, d2.title as target_title,
                       z1.name as source_zone_name, z2.name as target_zone_name
                FROM document_links dl
                JOIN document_zones z1 ON z1.id = dl.source_zone_id
                JOIN document_zones z2 ON z2.id = dl.target_zone_id
                JOIN documents d1 ON d1.id = z1.document_id
                JOIN documents d2 ON d2.id = z2.document_id
                WHERE (z1.document_id = ? OR z2.document_id = ?)
                AND dl.created_at > ?
                ORDER BY dl.created_at DESC
            ");
            $stmt->execute([$documentId, $documentId, $since]);
            $links = $stmt->fetchAll();
            
            // Récupérer les zones supprimées (si on a une table de log)
            $deletedZones = [];
            
            // Timestamp actuel pour la prochaine requête
            $newSince = date('Y-m-d H:i:s');
            
            echo json_encode([
                'success' => true,
                'timestamp' => $newSince,
                'updates' => [
                    'zones' => array_map(function($z) {
                        return [
                            'id' => (int)$z['id'],
                            'name' => $z['name'],
                            'x' => (float)$z['x'],
                            'y' => (float)$z['y'],
                            'width' => (float)$z['width'],
                            'height' => (float)$z['height'],
                            'page' => (int)$z['page'],
                            'color' => $z['color'],
                            'type' => $z['type'],
                            'tooltip' => $z['tooltip'],
                            'created_by' => $z['first_name'] . ' ' . $z['last_name'],
                            'created_at' => $z['created_at']
                        ];
                    }, $zones),
                    'annotations' => array_map(function($a) {
                        return [
                            'id' => (int)$a['id'],
                            'zone_id' => (int)$a['zone_id'],
                            'zone_name' => $a['zone_name'],
                            'content' => $a['content'],
                            'user_name' => $a['first_name'] . ' ' . $a['last_name'],
                            'created_at' => $a['created_at']
                        ];
                    }, $annotations),
                    'links' => array_map(function($l) {
                        return [
                            'id' => (int)$l['id'],
                            'source_zone_id' => (int)$l['source_zone_id'],
                            'target_zone_id' => (int)$l['target_zone_id'],
                            'source_title' => $l['source_title'],
                            'target_title' => $l['target_title'],
                            'source_zone_name' => $l['source_zone_name'],
                            'target_zone_name' => $l['target_zone_name'],
                            'created_at' => $l['created_at']
                        ];
                    }, $links),
                    'deleted_zones' => $deletedZones
                ],
                'has_updates' => !empty($zones) || !empty($annotations) || !empty($links)
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Récupère les utilisateurs actuellement sur le document
     */
    public function getViewers(): void {
        AuthController::requireAuth();
        
        header('Content-Type: application/json');
        
        $documentId = (int)($_GET['document_id'] ?? 0);
        
        if (!$documentId) {
            echo json_encode(['success' => false, 'error' => 'Document ID requis']);
            return;
        }
        
        try {
            $db = db();
            
            // Mettre à jour notre présence
            $stmt = $db->prepare("
                INSERT INTO document_viewers (document_id, user_id, last_seen)
                VALUES (?, ?, NOW())
                ON DUPLICATE KEY UPDATE last_seen = NOW()
            ");
            $stmt->execute([$documentId, currentUserId()]);
            
            // Récupérer les viewers actifs (vus dans les 30 dernières secondes)
            $stmt = $db->prepare("
                SELECT dv.*, u.first_name, u.last_name
                FROM document_viewers dv
                JOIN users u ON u.id = dv.user_id
                WHERE dv.document_id = ?
                AND dv.last_seen > DATE_SUB(NOW(), INTERVAL 30 SECOND)
                ORDER BY u.first_name
            ");
            $stmt->execute([$documentId]);
            $viewers = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'viewers' => array_map(function($v) {
                    return [
                        'id' => (int)$v['user_id'],
                        'name' => $v['first_name'] . ' ' . $v['last_name'],
                        'initials' => strtoupper(substr($v['first_name'], 0, 1) . substr($v['last_name'], 0, 1)),
                        'is_me' => (int)$v['user_id'] === currentUserId()
                    ];
                }, $viewers),
                'count' => count($viewers)
            ]);
            
        } catch (\Exception $e) {
            // Table n'existe peut-être pas encore
            echo json_encode([
                'success' => true,
                'viewers' => [],
                'count' => 0
            ]);
        }
    }
    
    /**
     * Quitter le document (cleanup)
     */
    public function leaveDocument(): void {
        AuthController::requireAuth();
        
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $documentId = (int)($input['document_id'] ?? 0);
        
        if ($documentId) {
            try {
                $db = db();
                $stmt = $db->prepare("DELETE FROM document_viewers WHERE document_id = ? AND user_id = ?");
                $stmt->execute([$documentId, currentUserId()]);
            } catch (\Exception $e) {
                // Ignorer les erreurs
            }
        }
        
        echo json_encode(['success' => true]);
    }
}
