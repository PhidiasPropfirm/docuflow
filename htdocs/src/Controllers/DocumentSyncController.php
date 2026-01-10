<?php
namespace App\Controllers;

/**
 * Contrôleur API pour les mises à jour en temps réel des documents
 * Fichier: htdocs/src/Controllers/DocumentSyncController.php
 */
class DocumentSyncController {
    
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
            
            // Récupérer les zones créées après le timestamp
            // Note: on utilise les vrais noms de colonnes de la table document_zones
            $stmt = $db->prepare("
                SELECT z.id, z.document_id, z.page_number, z.x, z.y, z.width, z.height,
                       z.label, z.zone_type, z.color, z.description, z.extracted_text,
                       z.created_at, z.created_by,
                       u.first_name, u.last_name 
                FROM document_zones z
                LEFT JOIN users u ON u.id = z.created_by
                WHERE z.document_id = ? 
                AND z.created_at > ?
                ORDER BY z.created_at DESC
            ");
            $stmt->execute([$documentId, $since]);
            $zones = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Récupérer les annotations créées après le timestamp
            $stmt = $db->prepare("
                SELECT a.id, a.document_id, a.zone_id, a.content, a.annotation_type,
                       a.is_resolved, a.created_at,
                       u.first_name, u.last_name
                FROM annotations a
                LEFT JOIN users u ON u.id = a.user_id
                WHERE a.document_id = ?
                AND a.created_at > ?
                ORDER BY a.created_at DESC
            ");
            $stmt->execute([$documentId, $since]);
            $annotations = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Récupérer les liaisons créées après le timestamp
            $links = [];
            try {
                $stmt = $db->prepare("
                    SELECT dl.id, dl.source_zone_id, dl.target_document_id, dl.target_zone_id,
                           dl.link_type, dl.description, dl.created_at,
                           d.title as target_doc_title
                    FROM document_links dl
                    JOIN documents d ON d.id = dl.target_document_id
                    JOIN document_zones z ON z.id = dl.source_zone_id
                    WHERE z.document_id = ?
                    AND dl.created_at > ?
                    ORDER BY dl.created_at DESC
                ");
                $stmt->execute([$documentId, $since]);
                $links = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e) {
                // Table peut ne pas exister, ignorer
            }
            
            // Timestamp MySQL actuel pour la prochaine requête (éviter décalage timezone)
            $stmt = $db->query("SELECT NOW() as now");
            $newTimestamp = $stmt->fetch()['now'];
            
            // Formater les zones pour le JavaScript
            $formattedZones = array_map(function($z) {
                return [
                    'id' => (int)$z['id'],
                    'document_id' => (int)$z['document_id'],
                    'page_number' => (int)$z['page_number'],
                    'x' => (float)$z['x'],
                    'y' => (float)$z['y'],
                    'width' => (float)$z['width'],
                    'height' => (float)$z['height'],
                    'label' => $z['label'] ?? '',
                    'zone_type' => $z['zone_type'] ?? 'custom',
                    'color' => $z['color'] ?? '#10B981',
                    'description' => $z['description'] ?? '',
                    'extracted_text' => $z['extracted_text'] ?? '',
                    'created_by' => trim(($z['first_name'] ?? '') . ' ' . ($z['last_name'] ?? '')),
                    'created_at' => $z['created_at']
                ];
            }, $zones);
            
            // Formater les annotations
            $formattedAnnotations = array_map(function($a) {
                return [
                    'id' => (int)$a['id'],
                    'document_id' => (int)$a['document_id'],
                    'zone_id' => $a['zone_id'] ? (int)$a['zone_id'] : null,
                    'content' => $a['content'] ?? '',
                    'annotation_type' => $a['annotation_type'] ?? 'note',
                    'is_resolved' => (bool)($a['is_resolved'] ?? false),
                    'user_name' => trim(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? '')),
                    'created_at' => $a['created_at']
                ];
            }, $annotations);
            
            // Formater les liaisons
            $formattedLinks = array_map(function($l) {
                return [
                    'id' => (int)$l['id'],
                    'source_zone_id' => (int)$l['source_zone_id'],
                    'target_document_id' => (int)$l['target_document_id'],
                    'target_zone_id' => $l['target_zone_id'] ? (int)$l['target_zone_id'] : null,
                    'link_type' => $l['link_type'] ?? 'reference',
                    'target_doc_title' => $l['target_doc_title'] ?? '',
                    'created_at' => $l['created_at']
                ];
            }, $links);
            
            $hasUpdates = !empty($zones) || !empty($annotations) || !empty($links);
            
            echo json_encode([
                'success' => true,
                'timestamp' => $newTimestamp,
                'has_updates' => $hasUpdates,
                'updates' => [
                    'zones' => $formattedZones,
                    'annotations' => $formattedAnnotations,
                    'links' => $formattedLinks
                ]
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            // En cas d'erreur, retourner un timestamp qui ne bloquera pas les futures requêtes
            echo json_encode([
                'success' => false, 
                'error' => $e->getMessage(),
                'timestamp' => $since, // Garder le même timestamp pour réessayer
                'has_updates' => false,
                'updates' => ['zones' => [], 'annotations' => [], 'links' => []]
            ]);
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
            $currentUserId = currentUserId();
            
            // Mettre à jour notre présence
            $stmt = $db->prepare("
                INSERT INTO document_viewers (document_id, user_id, last_seen)
                VALUES (?, ?, NOW())
                ON DUPLICATE KEY UPDATE last_seen = NOW()
            ");
            $stmt->execute([$documentId, $currentUserId]);
            
            // Récupérer les viewers actifs (vus dans les 30 dernières secondes)
            $stmt = $db->prepare("
                SELECT dv.user_id, u.first_name, u.last_name, dv.last_seen
                FROM document_viewers dv
                JOIN users u ON u.id = dv.user_id
                WHERE dv.document_id = ?
                AND dv.last_seen > DATE_SUB(NOW(), INTERVAL 30 SECOND)
                ORDER BY dv.last_seen DESC
            ");
            $stmt->execute([$documentId]);
            $viewers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Formater les viewers
            $formattedViewers = array_map(function($v) use ($currentUserId) {
                $firstName = $v['first_name'] ?? '';
                $lastName = $v['last_name'] ?? '';
                $initials = strtoupper(
                    substr($firstName, 0, 1) . substr($lastName, 0, 1)
                );
                
                return [
                    'user_id' => (int)$v['user_id'],
                    'name' => trim($firstName . ' ' . $lastName),
                    'initials' => $initials ?: '?',
                    'is_me' => (int)$v['user_id'] === $currentUserId,
                    'last_seen' => $v['last_seen']
                ];
            }, $viewers);
            
            echo json_encode([
                'success' => true,
                'viewers' => $formattedViewers,
                'count' => count($formattedViewers)
            ]);
            
        } catch (\Exception $e) {
            // Si la table n'existe pas, retourner une liste vide
            echo json_encode([
                'success' => true,
                'viewers' => [],
                'count' => 0
            ]);
        }
    }
    
    /**
     * Signale qu'un utilisateur quitte le document
     */
    public function leaveDocument(): void {
        header('Content-Type: application/json');
        
        // Lire les données POST (peut être JSON ou form-data)
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        $documentId = (int)($data['document_id'] ?? $_POST['document_id'] ?? 0);
        
        if (!$documentId) {
            echo json_encode(['success' => false, 'error' => 'Document ID requis']);
            return;
        }
        
        try {
            $db = db();
            $currentUserId = currentUserId();
            
            if ($currentUserId) {
                // Supprimer l'entrée du viewer
                $stmt = $db->prepare("
                    DELETE FROM document_viewers 
                    WHERE document_id = ? AND user_id = ?
                ");
                $stmt->execute([$documentId, $currentUserId]);
            }
            
            echo json_encode(['success' => true]);
            
        } catch (\Exception $e) {
            echo json_encode(['success' => true]); // Ignorer les erreurs
        }
    }
}
