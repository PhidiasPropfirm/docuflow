<?php
/**
 * DocuFlow - Modèle DocumentZone
 * Gestion des zones sélectionnées dans les documents
 */

namespace App\Models;

class DocumentZone extends BaseModel {
    protected string $table = 'document_zones';
    protected array $fillable = [
        'document_id', 'page_number', 'x', 'y', 'width', 'height',
        'label', 'extracted_text', 'zone_type', 'color', 'description', 'created_by'
    ];
    
    public const TYPES = [
        'line' => 'Ligne',
        'amount' => 'Montant',
        'reference' => 'Référence',
        'date' => 'Date',
        'signature' => 'Signature',
        'header' => 'En-tête',
        'footer' => 'Pied de page',
        'table' => 'Tableau',
        'custom' => 'Personnalisé'
    ];
    
    public const COLORS = [
        '#3B82F6' => 'Bleu',
        '#10B981' => 'Vert',
        '#F59E0B' => 'Orange',
        '#EF4444' => 'Rouge',
        '#8B5CF6' => 'Violet',
        '#EC4899' => 'Rose',
        '#6366F1' => 'Indigo',
        '#14B8A6' => 'Turquoise'
    ];
    
    /**
     * Récupère une zone avec ses liens
     */
    public function findWithLinks(int $id): ?array {
        $zone = $this->find($id);
        
        if ($zone) {
            // Liens sortants (cette zone pointe vers)
            $sql = "SELECT dl.*, d.title as target_title
                    FROM document_links dl
                    JOIN documents d ON dl.target_document_id = d.id
                    WHERE dl.source_zone_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $zone['links_out'] = $stmt->fetchAll();
            
            // Liens entrants (pointent vers cette zone)
            $sql = "SELECT dl.*, dz.label as source_label, d.title as source_title
                    FROM document_links dl
                    JOIN document_zones dz ON dl.source_zone_id = dz.id
                    JOIN documents d ON dz.document_id = d.id
                    WHERE dl.target_zone_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $zone['links_in'] = $stmt->fetchAll();
        }
        
        return $zone;
    }
    
    /**
     * Récupère les zones d'une page spécifique
     */
    public function getByPage(int $documentId, int $pageNumber): array {
        return $this->where([
            'document_id' => $documentId,
            'page_number' => $pageNumber
        ], 'y ASC, x ASC');
    }
    
    /**
     * Crée une zone et retourne l'objet complet
     */
    public function createAndReturn(array $data): array {
        $id = $this->create($data);
        return $this->find($id);
    }
    
    /**
     * Vérifie si une zone a des liens
     */
    public function hasLinks(int $zoneId): bool {
        $sql = "SELECT COUNT(*) as count FROM document_links 
                WHERE source_zone_id = ? OR target_zone_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$zoneId, $zoneId]);
        return (int) $stmt->fetch()['count'] > 0;
    }
    
    /**
     * Supprime une zone et ses liens associés
     */
    public function deleteWithLinks(int $id): bool {
        // Les liens seront supprimés automatiquement grâce à ON DELETE CASCADE
        return $this->delete($id);
    }
    
    /**
     * Met à jour une zone
     */
    public function updateZone(int $id, array $data): bool {
        $allowedFields = ['label', 'zone_type', 'color', 'description', 'extracted_text'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));
        
        if (empty($updateData)) {
            return false;
        }
        
        return $this->update($id, $updateData);
    }
}
