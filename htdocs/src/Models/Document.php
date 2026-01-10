<?php
/**
 * DocuFlow - Modèle Document
 * Gestion des documents PDF
 */

namespace App\Models;

class Document extends BaseModel {
    protected string $table = 'documents';
    protected array $fillable = [
        'user_id', 'team_id', 'title', 'description', 'filename', 
        'original_name', 'file_size', 'file_path', 'mime_type',
        'page_count', 'document_type', 'reference_number', 
        'document_date', 'total_amount', 'currency', 'is_ocr_processed'
    ];
    
    /**
     * Types de documents disponibles
     */
    public const TYPES = [
        'report' => 'Rapport',
        'invoice' => 'Facture',
        'receipt' => 'Reçu',
        'contract' => 'Contrat',
        'other' => 'Autre'
    ];
    
    /**
     * Récupère un document avec ses informations complètes
     */
    public function findWithDetails(int $id): ?array {
        $sql = "SELECT d.*, 
                       u.first_name, u.last_name, u.username,
                       t.name as team_name, t.color as team_color
                FROM {$this->table} d
                JOIN users u ON d.user_id = u.id
                LEFT JOIN teams t ON d.team_id = t.id
                WHERE d.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $doc = $stmt->fetch();
        
        if ($doc) {
            // Récupère les zones
            $doc['zones'] = $this->getZones($id);
            // Récupère les liaisons entrantes et sortantes
            $doc['links_from'] = $this->getLinksFrom($id);
            $doc['links_to'] = $this->getLinksTo($id);
            // Récupère les annotations
            $doc['annotations'] = $this->getAnnotations($id);
        }
        
        return $doc ?: null;
    }
    
    /**
     * Récupère tous les documents avec pagination
     */
    public function getAllPaginated(int $page = 1, int $perPage = 20, array $filters = []): array {
        $where = [];
        $params = [];
        
        if (!empty($filters['type'])) {
            $where[] = "d.document_type = ?";
            $params[] = $filters['type'];
        }
        
        if (!empty($filters['team_id'])) {
            $where[] = "d.team_id = ?";
            $params[] = $filters['team_id'];
        }
        
        if (!empty($filters['user_id'])) {
            $where[] = "d.user_id = ?";
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = "(d.title LIKE ? OR d.description LIKE ? OR d.reference_number LIKE ?)";
            $like = "%{$filters['search']}%";
            $params = array_merge($params, [$like, $like, $like]);
        }
        
        if (!empty($filters['date_from'])) {
            $where[] = "d.created_at >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where[] = "d.created_at <= ?";
            $params[] = $filters['date_to'] . ' 23:59:59';
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset = ($page - 1) * $perPage;
        
        // Compte total
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} d {$whereClause}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch()['total'];
        
        // Documents paginés
        $sql = "SELECT d.*, 
                       u.first_name, u.last_name,
                       t.name as team_name, t.color as team_color,
                       (SELECT COUNT(*) FROM document_zones WHERE document_id = d.id) as zone_count,
                       (SELECT COUNT(*) FROM document_links dl 
                        JOIN document_zones dz ON dl.source_zone_id = dz.id 
                        WHERE dz.document_id = d.id) as link_count
                FROM {$this->table} d
                JOIN users u ON d.user_id = u.id
                LEFT JOIN teams t ON d.team_id = t.id
                {$whereClause}
                ORDER BY d.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage)
        ];
    }
    
    /**
     * Recherche full-text dans le contenu des documents
     */
    public function searchContent(string $query): array {
        $sql = "SELECT d.*, u.first_name, u.last_name,
                       t.name as team_name,
                       dc.page_number,
                       MATCH(dc.content) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance
                FROM document_content dc
                JOIN {$this->table} d ON dc.document_id = d.id
                JOIN users u ON d.user_id = u.id
                LEFT JOIN teams t ON d.team_id = t.id
                WHERE MATCH(dc.content) AGAINST(? IN NATURAL LANGUAGE MODE)
                ORDER BY relevance DESC
                LIMIT 50";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$query, $query]);
        return $stmt->fetchAll();
    }
    
    /**
     * Sauvegarde le contenu extrait d'une page
     */
    public function savePageContent(int $documentId, int $pageNumber, string $content): int {
        $sql = "INSERT INTO document_content (document_id, page_number, content) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE content = VALUES(content)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$documentId, $pageNumber, $content]);
        return (int) $this->db->lastInsertId();
    }
    
    /**
     * Récupère les zones d'un document
     */
    public function getZones(int $documentId): array {
        $sql = "SELECT dz.*, u.first_name, u.last_name
                FROM document_zones dz
                JOIN users u ON dz.created_by = u.id
                WHERE dz.document_id = ?
                ORDER BY dz.page_number, dz.y";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$documentId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les liaisons partant de ce document
     */
    public function getLinksFrom(int $documentId): array {
        $sql = "SELECT dl.*, 
                       dz.id as source_zone_id,
                       dz.page_number as source_zone_page, 
                       dz.label as source_zone_label,
                       d.id as target_doc_id, 
                       d.title as target_doc_title,
                       tz.id as target_zone_id,
                       tz.page_number as target_zone_page,
                       tz.label as target_zone_label,
                       u.first_name, u.last_name
                FROM document_links dl
                JOIN document_zones dz ON dl.source_zone_id = dz.id
                JOIN documents d ON dl.target_document_id = d.id
                LEFT JOIN document_zones tz ON dl.target_zone_id = tz.id
                JOIN users u ON dl.created_by = u.id
                WHERE dz.document_id = ?
                ORDER BY dz.page_number, dl.created_at";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$documentId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les liaisons pointant vers ce document
     */
    public function getLinksTo(int $documentId): array {
        $sql = "SELECT dl.*, 
                       dz.id as source_zone_id,
                       dz.page_number as source_zone_page, 
                       dz.label as source_zone_label,
                       d.id as source_doc_id, 
                       d.title as source_doc_title,
                       tz.id as target_zone_id,
                       tz.page_number as target_zone_page,
                       tz.label as target_zone_label,
                       u.first_name, u.last_name
                FROM document_links dl
                JOIN document_zones dz ON dl.source_zone_id = dz.id
                JOIN documents d ON dz.document_id = d.id
                LEFT JOIN document_zones tz ON dl.target_zone_id = tz.id
                JOIN users u ON dl.created_by = u.id
                WHERE dl.target_document_id = ?
                ORDER BY dl.created_at";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$documentId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les annotations d'un document
     */
    public function getAnnotations(int $documentId): array {
        $sql = "SELECT a.*, u.first_name, u.last_name,
                       dz.page_number, dz.x, dz.y, dz.width, dz.height
                FROM annotations a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN document_zones dz ON a.zone_id = dz.id
                WHERE a.document_id = ?
                ORDER BY a.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$documentId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Documents récents
     */
    public function getRecent(int $limit = 10): array {
        $sql = "SELECT d.*, u.first_name, u.last_name, t.name as team_name, t.color as team_color
                FROM {$this->table} d
                JOIN users u ON d.user_id = u.id
                LEFT JOIN teams t ON d.team_id = t.id
                ORDER BY d.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Statistiques globales
     */
    public function getStats(): array {
        $stats = [];
        
        // Total documents
        $stats['total'] = $this->count();
        
        // Par type
        $sql = "SELECT document_type, COUNT(*) as count FROM {$this->table} GROUP BY document_type";
        $stmt = $this->db->query($sql);
        $stats['by_type'] = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        
        // Documents ce mois
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')";
        $stmt = $this->db->query($sql);
        $stats['this_month'] = (int) $stmt->fetch()['count'];
        
        // Total liaisons
        $sql = "SELECT COUNT(*) as count FROM document_links";
        $stmt = $this->db->query($sql);
        $stats['total_links'] = (int) $stmt->fetch()['count'];
        
        // Total annotations
        $sql = "SELECT COUNT(*) as count FROM annotations";
        $stmt = $this->db->query($sql);
        $stats['total_annotations'] = (int) $stmt->fetch()['count'];
        
        // Espace utilisé
        $sql = "SELECT SUM(file_size) as total FROM {$this->table}";
        $stmt = $this->db->query($sql);
        $stats['storage_used'] = (int) $stmt->fetch()['total'];
        
        return $stats;
    }
    
    /**
     * Génère un nom de fichier unique
     */
    public function generateFilename(string $originalName): string {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid('doc_') . '_' . time() . '.' . strtolower($extension);
    }
    
    /**
     * Supprime un document et son fichier
     */
    public function deleteWithFile(int $id): bool {
        $doc = $this->find($id);
        
        if ($doc) {
            $filePath = UPLOAD_DIR . $doc['filename'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            return $this->delete($id);
        }
        
        return false;
    }
}
