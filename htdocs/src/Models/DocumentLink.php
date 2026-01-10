<?php
/**
 * DocuFlow - Modèle DocumentLink
 * Gestion des liaisons entre documents
 */

namespace App\Models;

class DocumentLink extends BaseModel {
    protected string $table = 'document_links';
    protected array $fillable = [
        'source_zone_id', 'target_document_id', 'target_zone_id',
        'link_type', 'description', 'created_by'
    ];
    
    public const TYPES = [
        'reference' => 'Référence',
        'justification' => 'Justificatif',
        'annexe' => 'Annexe',
        'related' => 'Document lié'
    ];
    
    /**
     * Récupère un lien avec tous les détails
     */
    public function findWithDetails(int $id): ?array {
        $sql = "SELECT dl.*,
                       sz.document_id as source_document_id,
                       sz.page_number as source_page,
                       sz.label as source_label,
                       sz.x as source_x, sz.y as source_y,
                       sz.width as source_width, sz.height as source_height,
                       sd.title as source_document_title,
                       td.title as target_document_title,
                       tz.page_number as target_page,
                       tz.label as target_label,
                       u.first_name, u.last_name
                FROM {$this->table} dl
                JOIN document_zones sz ON dl.source_zone_id = sz.id
                JOIN documents sd ON sz.document_id = sd.id
                JOIN documents td ON dl.target_document_id = td.id
                LEFT JOIN document_zones tz ON dl.target_zone_id = tz.id
                JOIN users u ON dl.created_by = u.id
                WHERE dl.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Crée un lien bidirectionnel (optionnel)
     */
    public function createBidirectional(array $data, bool $bidirectional = false): array {
        $linkId = $this->create($data);
        $links = [$linkId];
        
        if ($bidirectional && !empty($data['target_zone_id'])) {
            // Crée le lien inverse
            $reverseData = [
                'source_zone_id' => $data['target_zone_id'],
                'target_document_id' => $this->getDocumentIdFromZone($data['source_zone_id']),
                'target_zone_id' => $data['source_zone_id'],
                'link_type' => $data['link_type'],
                'description' => $data['description'] ?? null,
                'created_by' => $data['created_by']
            ];
            $links[] = $this->create($reverseData);
        }
        
        return $links;
    }
    
    /**
     * Récupère l'ID du document à partir d'une zone
     */
    private function getDocumentIdFromZone(int $zoneId): int {
        $sql = "SELECT document_id FROM document_zones WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$zoneId]);
        return (int) $stmt->fetch()['document_id'];
    }
    
    /**
     * Vérifie si un lien existe déjà
     */
    public function linkExists(int $sourceZoneId, int $targetDocumentId): bool {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE source_zone_id = ? AND target_document_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sourceZoneId, $targetDocumentId]);
        return (int) $stmt->fetch()['count'] > 0;
    }
    
    /**
     * Récupère tous les liens récents
     */
    public function getRecent(int $limit = 10): array {
        $sql = "SELECT dl.*,
                       sd.title as source_title,
                       td.title as target_title,
                       u.first_name, u.last_name
                FROM {$this->table} dl
                JOIN document_zones sz ON dl.source_zone_id = sz.id
                JOIN documents sd ON sz.document_id = sd.id
                JOIN documents td ON dl.target_document_id = td.id
                JOIN users u ON dl.created_by = u.id
                ORDER BY dl.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Statistiques des liens
     */
    public function getStats(): array {
        $stats = [];
        
        // Total
        $stats['total'] = $this->count();
        
        // Par type
        $sql = "SELECT link_type, COUNT(*) as count FROM {$this->table} GROUP BY link_type";
        $stmt = $this->db->query($sql);
        $stats['by_type'] = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        
        // Créés ce mois
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')";
        $stmt = $this->db->query($sql);
        $stats['this_month'] = (int) $stmt->fetch()['count'];
        
        return $stats;
    }
}
