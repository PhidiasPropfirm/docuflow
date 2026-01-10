<?php
/**
 * DocuFlow - Vue document avec traductions FR/EN et synchronisation temps réel
 * Fichier complet - htdocs/src/Views/pages/documents/show.php
 */

// Traductions DocuFlow intégrées
$docuflowTranslations = [
    'fr' => [
        'zones' => 'Zones', 'zone' => 'Zone', 'links' => 'Liaisons', 'notes' => 'Notes', 'info' => 'Infos',
        'selected_zones' => 'Zones sélectionnées', 'no_zones_selected' => 'Aucune zone sélectionnée',
        'zones_hint' => 'Utilisez l\'outil zone pour sélectionner des parties du document',
        'new_zone' => 'Nouvelle zone', 'edit_zone' => 'Modifier la zone',
        'create_link' => 'Créer une liaison', 'create_link_title' => 'Créer une liaison',
        'target_document' => 'Document cible', 'target_zone' => 'Zone cible',
        'entire_document' => 'Document entier', 'select_placeholder' => 'Sélectionner...',
        'select_document' => 'Sélectionnez un document', 'select_user' => 'Sélectionner un utilisateur',
        'label' => 'Label', 'color' => 'Couleur', 'type' => 'Type', 'description' => 'Description',
        'optional' => 'optionnel', 'save' => 'Sauvegarder', 'cancel' => 'Annuler', 'create' => 'Créer',
        'add' => 'Ajouter', 'delete' => 'Supprimer', 'edit' => 'Modifier', 'download' => 'Télécharger',
        'loading' => 'Chargement...', 'adjust' => 'Ajuster', 'select_tool' => 'Sélectionner',
        'zone_tool' => 'Créer une zone', 'run_ocr' => 'Lancer l\'OCR', 'reference' => 'Référence',
        'document' => 'Document', 'no_links' => 'Aucune liaison',
        'points_to' => 'Ce document pointe vers', 'pointed_by' => 'Pointent vers ce document',
        'annotations' => 'Annotations', 'no_annotations' => 'Aucune annotation',
        'add_annotation_title' => 'Ajouter une annotation', 'annotation_content' => 'Contenu',
        'resolve' => 'Résoudre', 'document_details' => 'Détails du document',
        'filename' => 'Nom du fichier', 'file_size' => 'Taille', 'created_on' => 'Créé le',
        'extracted_text' => 'Texte extrait', 'mention_user' => 'Mentionner',
        'mention_user_title' => 'Mentionner un utilisateur', 'concerned_zone' => 'Zone concernée',
        'user_to_notify' => 'Utilisateur à notifier', 'message' => 'Message',
        'send_notification' => 'Envoyer', 'notification_sent' => 'Notification envoyée !',
        'delete_zone_confirm' => 'Supprimer cette zone ?', 'ocr_confirm' => 'Lancer l\'OCR sur tout le document ?',
        'ocr_complete' => 'OCR terminé !', 'error_zone_not_found' => 'Zone introuvable',
        'content_required' => 'Contenu requis', 'sync_updated' => 'Document mis à jour par un collaborateur',
    ],
    'en' => [
        'zones' => 'Zones', 'zone' => 'Zone', 'links' => 'Links', 'notes' => 'Notes', 'info' => 'Info',
        'selected_zones' => 'Selected zones', 'no_zones_selected' => 'No zones selected',
        'zones_hint' => 'Use the zone tool to select parts of the document',
        'new_zone' => 'New zone', 'edit_zone' => 'Edit zone',
        'create_link' => 'Create a link', 'create_link_title' => 'Create a link',
        'target_document' => 'Target document', 'target_zone' => 'Target zone',
        'entire_document' => 'Entire document', 'select_placeholder' => 'Select...',
        'select_document' => 'Select a document', 'select_user' => 'Select a user',
        'label' => 'Label', 'color' => 'Color', 'type' => 'Type', 'description' => 'Description',
        'optional' => 'optional', 'save' => 'Save', 'cancel' => 'Cancel', 'create' => 'Create',
        'add' => 'Add', 'delete' => 'Delete', 'edit' => 'Edit', 'download' => 'Download',
        'loading' => 'Loading...', 'adjust' => 'Fit', 'select_tool' => 'Select',
        'zone_tool' => 'Create zone', 'run_ocr' => 'Run OCR', 'reference' => 'Reference',
        'document' => 'Document', 'no_links' => 'No links',
        'points_to' => 'This document points to', 'pointed_by' => 'Point to this document',
        'annotations' => 'Annotations', 'no_annotations' => 'No annotations',
        'add_annotation_title' => 'Add an annotation', 'annotation_content' => 'Content',
        'resolve' => 'Resolve', 'document_details' => 'Document details',
        'filename' => 'Filename', 'file_size' => 'Size', 'created_on' => 'Created on',
        'extracted_text' => 'Extracted text', 'mention_user' => 'Mention',
        'mention_user_title' => 'Mention a user', 'concerned_zone' => 'Concerned zone',
        'user_to_notify' => 'User to notify', 'message' => 'Message',
        'send_notification' => 'Send', 'notification_sent' => 'Notification sent!',
        'delete_zone_confirm' => 'Delete this zone?', 'ocr_confirm' => 'Run OCR on the entire document?',
        'ocr_complete' => 'OCR complete!', 'error_zone_not_found' => 'Zone not found',
        'content_required' => 'Content required', 'sync_updated' => 'Document updated by a collaborator',
    ]
];

function _t($key) {
    global $docuflowTranslations;
    $lang = $_SESSION['language'] ?? 'fr';
    return $docuflowTranslations[$lang][$key] ?? $docuflowTranslations['fr'][$key] ?? $key;
}

$pageTitle = $document['title'] ?? 'Document';
$zones = $document['zones'] ?? [];
$linksFrom = $document['links_from'] ?? [];
$linksTo = $document['links_to'] ?? [];
$annotations = $document['annotations'] ?? [];

ob_start();
?>

<div class="document-viewer-page">
    <div class="document-header">
        <div class="document-header-left">
            <a href="/documents" class="back-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                </svg>
            </a>
            <div class="document-title-section">
                <h1><?= htmlspecialchars($document['title'] ?? '') ?></h1>
                <div class="document-meta-inline">
                    <span class="badge badge-<?= $document['document_type'] ?? 'other' ?>"><?= \App\Models\Document::TYPES[$document['document_type']] ?? _t('document') ?></span>
                    <?php if (!empty($document['team_name'])): ?>
                    <span class="team-badge" style="--team-color: <?= $document['team_color'] ?? '#3B82F6' ?>"><?= htmlspecialchars($document['team_name']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="document-header-actions">
            <!-- Indicateur des viewers (sync temps réel) -->
            <div id="documentViewers" class="document-viewers"></div>
            
            <button class="btn btn-ghost btn-sm" onclick="toggleOCR()" title="<?= _t('run_ocr') ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 7 4 4 20 4 20 7"/><line x1="9" y1="20" x2="15" y2="20"/><line x1="12" y1="4" x2="12" y2="20"/></svg> OCR
            </button>
            <a href="/uploads/<?= $document['filename'] ?? '' ?>" download class="btn btn-ghost btn-sm">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> <?= _t('download') ?>
            </a>
        </div>
    </div>
    
    <div class="document-viewer-container">
        <div class="pdf-viewer-panel">
            <div class="pdf-toolbar">
                <div class="pdf-nav">
                    <button onclick="previousPage()" id="prevBtn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></button>
                    <div class="page-info"><input type="number" id="pageInput" value="1" min="1"> / <span id="pageCount">1</span></div>
                    <button onclick="nextPage()" id="nextBtn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></button>
                </div>
                <div class="pdf-zoom">
                    <button onclick="zoomOut()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg></button>
                    <span id="zoomLevel">100%</span>
                    <button onclick="zoomIn()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg></button>
                    <button onclick="fitToWidth()"><?= _t('adjust') ?></button>
                </div>
                <div class="pdf-tools">
                    <button class="tool-btn active" data-tool="select" onclick="setTool('select')" title="<?= _t('select_tool') ?>"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3l7.07 16.97 2.51-7.39 7.39-2.51L3 3z"/></svg></button>
                    <button class="tool-btn" data-tool="zone" onclick="setTool('zone')" title="<?= _t('zone_tool') ?>"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/></svg></button>
                </div>
            </div>
            <div class="pdf-canvas-container" id="pdfContainer">
                <div class="pdf-canvas-wrapper" id="pdfWrapper"><canvas id="pdfCanvas"></canvas><canvas id="overlayCanvas"></canvas></div>
                <div class="pdf-loading" id="pdfLoading"><div class="spinner"></div><span><?= _t('loading') ?></span></div>
            </div>
        </div>
        
        <div class="document-sidebar">
            <div class="sidebar-tabs">
                <button class="tab-btn active" data-tab="zones" onclick="showTab('zones')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/></svg> <?= _t('zones') ?></button>
                <button class="tab-btn" data-tab="links" onclick="showTab('links')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg> <?= _t('links') ?></button>
                <button class="tab-btn" data-tab="annotations" onclick="showTab('annotations')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> <?= _t('notes') ?></button>
                <button class="tab-btn" data-tab="info" onclick="showTab('info')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg> <?= _t('info') ?></button>
            </div>
            
            <div class="sidebar-content">
                <!-- Tab Zones -->
                <div class="tab-content active" id="tab-zones">
                    <div class="tab-header"><h3><?= _t('selected_zones') ?></h3><span class="badge" id="zonesBadge"><?= count($zones) ?></span></div>
                    <div class="zones-list" id="zonesList">
                        <?php if (empty($zones)): ?>
                        <div class="empty-state small"><p><?= _t('no_zones_selected') ?></p><p class="hint"><?= _t('zones_hint') ?></p></div>
                        <?php else: foreach ($zones as $zone): ?>
                        <div class="zone-item" data-zone-id="<?= $zone['id'] ?>" data-zone-page="<?= $zone['page_number'] ?>" style="border-left: 4px solid <?= $zone['color'] ?? '#10B981' ?>;">
                            <div class="zone-header"><span class="zone-label"><?= htmlspecialchars($zone['label'] ?: _t('zone') . ' ' . $zone['id']) ?></span><span class="zone-page">P.<?= $zone['page_number'] ?></span></div>
                            <?php if (!empty($zone['description'])): ?><div class="zone-description"><?= htmlspecialchars(substr($zone['description'], 0, 80)) ?>...</div>
                            <?php elseif (!empty($zone['extracted_text'])): ?><div class="zone-text"><?= htmlspecialchars(substr($zone['extracted_text'], 0, 100)) ?>...</div><?php endif; ?>
                            <div class="zone-actions">
                                <button onclick="event.stopPropagation(); openEditZoneModal(<?= $zone['id'] ?>)" title="<?= _t('edit_zone') ?>"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                                <button onclick="event.stopPropagation(); createLinkFromZone(<?= $zone['id'] ?>)" title="<?= _t('create_link') ?>"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></button>
                                <button onclick="event.stopPropagation(); mentionUserOnZone(<?= $zone['id'] ?>, '<?= addslashes($zone['label'] ?: _t('zone') . ' ' . $zone['id']) ?>')" title="<?= _t('mention_user') ?>"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"/></svg></button>
                                <button onclick="event.stopPropagation(); deleteZone(<?= $zone['id'] ?>)" title="<?= _t('delete') ?>"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                            </div>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
                
                <!-- Tab Links -->
                <div class="tab-content" id="tab-links">
                    <div class="tab-header"><h3><?= _t('links') ?></h3><span class="badge"><?= count($linksFrom) + count($linksTo) ?></span></div>
                    <?php if (!empty($linksFrom)): ?><div class="links-section"><h4><?= _t('points_to') ?></h4>
                    <?php foreach ($linksFrom as $link): ?><div class="link-item-detailed outgoing"><div class="link-source" onclick="focusZone(<?= $link['source_zone_id'] ?>)"><span>📍</span><span><?= htmlspecialchars($link['source_zone_label'] ?? _t('zone') . ' ' . $link['source_zone_id']) ?></span></div><div class="link-arrow">→</div><a href="/documents/<?= $link['target_doc_id'] ?>"><?= htmlspecialchars($link['target_doc_title']) ?></a></div><?php endforeach; ?></div><?php endif; ?>
                    <?php if (!empty($linksTo)): ?><div class="links-section"><h4><?= _t('pointed_by') ?></h4>
                    <?php foreach ($linksTo as $link): ?><div class="link-item-detailed incoming"><a href="/documents/<?= $link['source_doc_id'] ?>"><?= htmlspecialchars($link['source_doc_title']) ?></a><div class="link-arrow">→</div><div><?= _t('document') ?></div></div><?php endforeach; ?></div><?php endif; ?>
                    <?php if (empty($linksFrom) && empty($linksTo)): ?><div class="empty-state small"><p><?= _t('no_links') ?></p></div><?php endif; ?>
                </div>
                
                <!-- Tab Annotations -->
                <div class="tab-content" id="tab-annotations">
                    <div class="tab-header"><h3><?= _t('annotations') ?></h3><button class="btn btn-sm btn-primary" onclick="openAnnotationModal()">+ <?= _t('add') ?></button></div>
                    <div class="annotations-list" id="annotationsList"><?php if (empty($annotations)): ?><div class="empty-state small"><p><?= _t('no_annotations') ?></p></div>
                    <?php else: foreach ($annotations as $a): ?><div class="annotation-item <?= !empty($a['is_resolved']) ? 'resolved' : '' ?>" data-annotation-id="<?= $a['id'] ?>"><div class="annotation-header"><span><?= htmlspecialchars($a['first_name'] ?? '') ?></span><span class="badge"><?= $a['annotation_type'] ?? 'note' ?></span></div><div class="annotation-content"><?= nl2br(htmlspecialchars($a['content'] ?? '')) ?></div><div class="annotation-footer"><?php if (empty($a['is_resolved'])): ?><button onclick="resolveAnnotation(<?= $a['id'] ?>)"><?= _t('resolve') ?></button><?php endif; ?></div></div><?php endforeach; endif; ?></div>
                </div>
                
                <!-- Tab Info -->
                <div class="tab-content" id="tab-info">
                    <div class="tab-header"><h3><?= _t('document_details') ?></h3></div>
                    <div class="info-details"><dl><dt><?= _t('filename') ?></dt><dd><?= htmlspecialchars($document['filename'] ?? '') ?></dd><dt><?= _t('file_size') ?></dt><dd><?= isset($document['file_size']) ? number_format($document['file_size']/1024, 0) . ' Ko' : '-' ?></dd><dt><?= _t('type') ?></dt><dd><?= \App\Models\Document::TYPES[$document['document_type']] ?? _t('document') ?></dd><dt><?= _t('created_on') ?></dt><dd><?= isset($document['created_at']) ? date('d/m/Y', strtotime($document['created_at'])) : '-' ?></dd></dl></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal création zone -->
<div class="modal" id="zoneModal"><div class="modal-overlay" onclick="closeZoneModal()"></div><div class="modal-content">
<div class="modal-header"><h3><?= _t('new_zone') ?></h3><button onclick="closeZoneModal()" class="modal-close">&times;</button></div>
<div class="modal-body">
<div class="form-row"><div class="form-group"><label><?= _t('label') ?></label><input type="text" id="zoneLabel" placeholder="Ex: TVA..."></div><div class="form-group"><label><?= _t('color') ?></label><div class="color-picker-wrapper"><input type="color" id="zoneColor" value="#3B82F6"><div class="color-presets"><button type="button" class="color-preset" style="background:#3B82F6" onclick="setZoneColor('#3B82F6')"></button><button type="button" class="color-preset" style="background:#10B981" onclick="setZoneColor('#10B981')"></button><button type="button" class="color-preset" style="background:#F59E0B" onclick="setZoneColor('#F59E0B')"></button><button type="button" class="color-preset" style="background:#EF4444" onclick="setZoneColor('#EF4444')"></button><button type="button" class="color-preset" style="background:#8B5CF6" onclick="setZoneColor('#8B5CF6')"></button><button type="button" class="color-preset" style="background:#EC4899" onclick="setZoneColor('#EC4899')"></button></div></div></div></div>
<div class="form-group"><label><?= _t('type') ?></label><select id="zoneType"><option value="custom">Custom</option><option value="date">Date</option><option value="amount">Amount</option><option value="reference"><?= _t('reference') ?></option><option value="signature">Signature</option></select></div>
<div class="form-group"><label><?= _t('description') ?> (<?= _t('optional') ?>)</label><textarea id="zoneDescription" rows="2"></textarea></div>
<div class="form-group"><label><?= _t('extracted_text') ?></label><textarea id="zoneText" rows="3" readonly></textarea></div>
</div>
<div class="modal-footer"><button onclick="closeZoneModal()" class="btn btn-ghost"><?= _t('cancel') ?></button><button onclick="saveZone()" class="btn btn-primary"><?= _t('save') ?></button></div>
</div></div>

<!-- Modal édition zone -->
<div class="modal" id="editZoneModal"><div class="modal-overlay" onclick="closeEditZoneModal()"></div><div class="modal-content">
<div class="modal-header"><h3><?= _t('edit_zone') ?></h3><button onclick="closeEditZoneModal()" class="modal-close">&times;</button></div>
<div class="modal-body"><input type="hidden" id="editZoneId">
<div class="form-row"><div class="form-group"><label><?= _t('label') ?></label><input type="text" id="editZoneLabel"></div><div class="form-group"><label><?= _t('color') ?></label><div class="color-picker-wrapper"><input type="color" id="editZoneColor" value="#10B981"><div class="color-presets"><button type="button" class="color-preset" style="background:#3B82F6" onclick="setEditZoneColor('#3B82F6')"></button><button type="button" class="color-preset" style="background:#10B981" onclick="setEditZoneColor('#10B981')"></button><button type="button" class="color-preset" style="background:#F59E0B" onclick="setEditZoneColor('#F59E0B')"></button><button type="button" class="color-preset" style="background:#EF4444" onclick="setEditZoneColor('#EF4444')"></button><button type="button" class="color-preset" style="background:#8B5CF6" onclick="setEditZoneColor('#8B5CF6')"></button><button type="button" class="color-preset" style="background:#EC4899" onclick="setEditZoneColor('#EC4899')"></button></div></div></div></div>
<div class="form-group"><label><?= _t('type') ?></label><select id="editZoneType"><option value="custom">Custom</option><option value="date">Date</option><option value="amount">Amount</option><option value="reference"><?= _t('reference') ?></option><option value="signature">Signature</option></select></div>
<div class="form-group"><label><?= _t('description') ?> (<?= _t('optional') ?>)</label><textarea id="editZoneDescription" rows="2"></textarea></div>
<div class="form-group"><label><?= _t('extracted_text') ?></label><textarea id="editZoneText" rows="2"></textarea></div>
</div>
<div class="modal-footer"><button onclick="closeEditZoneModal()" class="btn btn-ghost"><?= _t('cancel') ?></button><button onclick="updateZone()" class="btn btn-primary"><?= _t('save') ?></button></div>
</div></div>

<!-- Modal liaison -->
<div class="modal" id="linkModal"><div class="modal-overlay" onclick="closeLinkModal()"></div><div class="modal-content">
<div class="modal-header"><h3><?= _t('create_link_title') ?></h3><button onclick="closeLinkModal()" class="modal-close">&times;</button></div>
<div class="modal-body"><input type="hidden" id="linkSourceZoneId">
<div class="form-group"><label><?= _t('target_document') ?></label><select id="linkTargetDocument" onchange="loadTargetZones()"><option value=""><?= _t('select_placeholder') ?></option><?php if (!empty($availableDocuments)): foreach ($availableDocuments as $doc): if ($doc['id'] != $document['id']): ?><option value="<?= $doc['id'] ?>"><?= htmlspecialchars($doc['title']) ?></option><?php endif; endforeach; endif; ?></select></div>
<div class="form-group" id="targetZoneGroup" style="display:none;"><label><?= _t('target_zone') ?> (<?= _t('optional') ?>)</label><select id="linkTargetZone"><option value=""><?= _t('entire_document') ?></option></select></div>
<div class="form-group"><label><?= _t('type') ?></label><select id="linkType"><option value="reference"><?= _t('reference') ?></option><option value="annex">Annex</option><option value="related">Related</option><option value="supersedes">Supersedes</option><option value="amends">Amends</option></select></div>
<div class="form-group"><label><?= _t('description') ?> (<?= _t('optional') ?>)</label><textarea id="linkDescription" rows="2"></textarea></div>
</div>
<div class="modal-footer"><button onclick="closeLinkModal()" class="btn btn-ghost"><?= _t('cancel') ?></button><button onclick="createLink()" class="btn btn-primary"><?= _t('create') ?></button></div>
</div></div>

<!-- Modal annotation -->
<div class="modal" id="annotationModal"><div class="modal-overlay" onclick="closeAnnotationModal()"></div><div class="modal-content">
<div class="modal-header"><h3><?= _t('add_annotation_title') ?></h3><button onclick="closeAnnotationModal()" class="modal-close">&times;</button></div>
<div class="modal-body">
<div class="form-group"><label><?= _t('type') ?></label><select id="annotationType"><option value="note">Note</option><option value="comment">Comment</option><option value="question">Question</option><option value="validation">Validation</option><option value="rejection">Rejection</option></select></div>
<div class="form-group"><label><?= _t('zone') ?> (<?= _t('optional') ?>)</label><select id="annotationZone"><option value=""><?= _t('entire_document') ?></option><?php foreach ($zones as $z): ?><option value="<?= $z['id'] ?>"><?= htmlspecialchars($z['label'] ?: _t('zone') . ' ' . $z['id']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label><?= _t('annotation_content') ?> *</label><textarea id="annotationContent" rows="4" required></textarea></div>
</div>
<div class="modal-footer"><button onclick="closeAnnotationModal()" class="btn btn-ghost"><?= _t('cancel') ?></button><button onclick="createAnnotation()" class="btn btn-primary"><?= _t('add') ?></button></div>
</div></div>

<!-- Modal Mention -->
<div class="modal" id="mentionModal"><div class="modal-overlay" onclick="closeMentionModal()"></div><div class="modal-content">
<div class="modal-header"><h3><?= _t('mention_user_title') ?></h3><button onclick="closeMentionModal()" class="modal-close">&times;</button></div>
<div class="modal-body"><input type="hidden" id="mentionZoneId">
<div class="form-group"><label><?= _t('concerned_zone') ?></label><input type="text" id="mentionZoneName" readonly></div>
<div class="form-group"><label><?= _t('user_to_notify') ?> *</label><select id="mentionUserId" required><option value=""><?= _t('select_user') ?></option><?php if (class_exists('\App\Models\User')) { try { $userModel = new \App\Models\User(); $allUsers = $userModel->all(); $currentId = function_exists('currentUserId') ? currentUserId() : 0; foreach ($allUsers as $u): if ($u['id'] != $currentId): ?><option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></option><?php endif; endforeach; } catch (Exception $e) {} } ?></select></div>
<div class="form-group"><label><?= _t('message') ?> (<?= _t('optional') ?>)</label><textarea id="mentionMessage" rows="3"></textarea></div>
</div>
<div class="modal-footer"><button onclick="closeMentionModal()" class="btn btn-ghost"><?= _t('cancel') ?></button><button onclick="sendMention()" class="btn btn-primary"><?= _t('send_notification') ?></button></div>
</div></div>

<!-- Styles synchronisation temps réel -->
<style>
.document-viewers { display: inline-flex; align-items: center; margin-right: 15px; }
.viewers-avatars { display: flex; }
.viewer-avatar { width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, #3B82F6, #8B5CF6); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600; border: 2px solid white; margin-left: -8px; cursor: default; transition: transform 0.2s; }
.viewer-avatar:first-child { margin-left: 0; }
.viewer-avatar:hover { transform: scale(1.1); z-index: 10; }
.viewer-avatar.is-me { background: linear-gradient(135deg, #10B981, #059669); }
.viewers-count { margin-left: 8px; font-size: 0.75rem; color: var(--text-muted); }
.sync-notification { position: fixed; bottom: 20px; right: 20px; background: white; padding: 12px 20px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 10px; z-index: 9999; animation: slideInRight 0.3s ease; }
@keyframes slideInRight { from { transform: translateX(100px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
.sync-notification.hide { animation: slideOutRight 0.3s ease forwards; }
@keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100px); opacity: 0; } }
.sync-dot { width: 8px; height: 8px; background: #10B981; border-radius: 50%; animation: pulse 1.5s infinite; }
@keyframes pulse { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.3); opacity: 0.7; } }
.zone-item.sync-new { animation: flashNew 0.5s ease 3; }
@keyframes flashNew { 0%, 100% { background: var(--bg-secondary); } 50% { background: rgba(16, 185, 129, 0.3); } }
</style>

<script>
const DOCUMENT_ID = <?= $document['id'] ?>;
const PDF_URL = '/uploads/<?= rawurlencode($document['filename'] ?? '') ?>';
const LANG = { 
    zone: '<?= _t('zone') ?>', 
    delete_zone_confirm: '<?= _t('delete_zone_confirm') ?>', 
    ocr_confirm: '<?= _t('ocr_confirm') ?>', 
    ocr_complete: '<?= _t('ocr_complete') ?>', 
    error_zone_not_found: '<?= _t('error_zone_not_found') ?>', 
    select_document: '<?= _t('select_document') ?>', 
    content_required: '<?= _t('content_required') ?>', 
    select_user: '<?= _t('select_user') ?>', 
    notification_sent: '<?= _t('notification_sent') ?>', 
    entire_document: '<?= _t('entire_document') ?>', 
    sync_updated: '<?= _t('sync_updated') ?>' 
};

let pdfDoc = null, currentPage = 1, totalPages = 0, scale = 1.5, renderScale = window.devicePixelRatio || 2;
let currentTool = 'select', fabricCanvas = null, zones = <?= json_encode($zones) ?>;
let isDrawing = false, startPoint = null, currentRect = null, pendingZoneData = null;

pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

// ==========================================
// FONCTIONS SIDEBAR ZONES
// ==========================================
function highlightSidebarZone(zoneId) { 
    document.querySelectorAll('.zone-item.highlighted').forEach(el => el.classList.remove('highlighted')); 
    const item = document.querySelector(`.zone-item[data-zone-id="${zoneId}"]`); 
    if (item) { item.classList.add('highlighted'); item.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); } 
}

function unhighlightSidebarZone(zoneId) { 
    const item = document.querySelector(`.zone-item[data-zone-id="${zoneId}"]`); 
    if (item && !item.classList.contains('active')) item.classList.remove('highlighted'); 
}

function flashCanvasZone(zoneId) { 
    if (!fabricCanvas) return; 
    fabricCanvas.getObjects().forEach(obj => { 
        if (obj.data && String(obj.data.zoneId) === String(zoneId)) { 
            let count = 0; 
            const baseFill = obj.fill; 
            const interval = setInterval(() => { 
                if (count >= 6) { clearInterval(interval); obj.set({ fill: baseFill, strokeWidth: 2 }); fabricCanvas.renderAll(); return; } 
                obj.set(count % 2 === 0 ? { fill: baseFill.replace('0.15', '0.6'), strokeWidth: 5 } : { fill: baseFill, strokeWidth: 2 }); 
                fabricCanvas.renderAll(); 
                count++; 
            }, 150); 
        } 
    }); 
}

function initZoneSyncEvents() { 
    document.querySelectorAll('.zone-item').forEach(item => { 
        const zoneId = item.dataset.zoneId; 
        item.addEventListener('mouseenter', () => item.classList.add('highlighted')); 
        item.addEventListener('mouseleave', () => { if (!item.classList.contains('active')) item.classList.remove('highlighted'); }); 
        item.addEventListener('click', (e) => { 
            if (e.target.closest('.zone-actions')) return; 
            document.querySelectorAll('.zone-item').forEach(el => el.classList.remove('active')); 
            item.classList.add('active'); 
            const zone = zones.find(z => String(z.id) === zoneId); 
            if (!zone) return; 
            if (parseInt(zone.page_number) !== currentPage) { 
                currentPage = parseInt(zone.page_number); 
                document.getElementById('pageInput').value = currentPage; 
                renderPage(currentPage).then(() => setTimeout(() => flashCanvasZone(zoneId), 150)); 
            } else { 
                flashCanvasZone(zoneId); 
            } 
        }); 
    }); 
}

// ==========================================
// FONCTIONS PDF VIEWER
// ==========================================
async function loadPDF() { 
    try { 
        pdfDoc = await pdfjsLib.getDocument(PDF_URL).promise; 
        totalPages = pdfDoc.numPages; 
        document.getElementById('pageCount').textContent = totalPages; 
        document.getElementById('pageInput').max = totalPages; 
        document.getElementById('pdfLoading').style.display = 'none'; 
        await renderPage(currentPage); 
        initZoneSyncEvents(); 
        startRealTimeSync(); 
    } catch (e) { 
        console.error(e); 
        document.getElementById('pdfLoading').innerHTML = '<span class="error">Erreur chargement PDF</span>'; 
    } 
}

async function renderPage(pageNum) { 
    const page = await pdfDoc.getPage(pageNum); 
    const canvas = document.getElementById('pdfCanvas'); 
    const ctx = canvas.getContext('2d'); 
    const viewport = page.getViewport({ scale }); 
    canvas.width = Math.floor(viewport.width * renderScale); 
    canvas.height = Math.floor(viewport.height * renderScale); 
    canvas.style.width = Math.floor(viewport.width) + 'px'; 
    canvas.style.height = Math.floor(viewport.height) + 'px'; 
    await page.render({ canvasContext: ctx, viewport, transform: [renderScale, 0, 0, renderScale, 0, 0] }).promise; 
    initFabricCanvas(); 
    updateNavButtons(); 
}

function initFabricCanvas() { 
    const c = document.getElementById('pdfCanvas'), o = document.getElementById('overlayCanvas'); 
    const w = parseInt(c.style.width), h = parseInt(c.style.height); 
    o.width = w; o.height = h; o.style.width = w + 'px'; o.style.height = h + 'px'; 
    if (fabricCanvas) fabricCanvas.dispose(); 
    fabricCanvas = new fabric.Canvas('overlayCanvas', { selection: false }); 
    fabricCanvas.on('mouse:down', onMouseDown); 
    fabricCanvas.on('mouse:move', onMouseMove); 
    fabricCanvas.on('mouse:up', onMouseUp); 
    renderZones(); 
}

function renderZones() { 
    if (!fabricCanvas) return; 
    fabricCanvas.clear(); 
    const c = document.getElementById('pdfCanvas'); 
    const w = parseInt(c.style.width), h = parseInt(c.style.height); 
    zones.filter(z => parseInt(z.page_number) === currentPage).forEach(zone => { 
        const color = zone.color || '#10B981'; 
        const r = parseInt(color.slice(1,3),16), g = parseInt(color.slice(3,5),16), b = parseInt(color.slice(5,7),16); 
        const fill = `rgba(${r},${g},${b},0.15)`; 
        const rect = new fabric.Rect({ 
            left: (zone.x/100)*w, top: (zone.y/100)*h, width: (zone.width/100)*w, height: (zone.height/100)*h, 
            fill, stroke: color, strokeWidth: 2, selectable: false, hoverCursor: 'pointer', data: { zoneId: zone.id, color } 
        }); 
        rect.on('mouseover', function() { highlightSidebarZone(zone.id); this.set({ strokeWidth: 4, fill: fill.replace('0.15','0.35') }); fabricCanvas.renderAll(); }); 
        rect.on('mouseout', function() { unhighlightSidebarZone(zone.id); this.set({ strokeWidth: 2, fill }); fabricCanvas.renderAll(); }); 
        rect.on('mousedown', function() { 
            if (currentTool !== 'select') return; 
            document.querySelectorAll('.zone-item').forEach(el => el.classList.remove('active')); 
            const item = document.querySelector(`.zone-item[data-zone-id="${zone.id}"]`); 
            if (item) { item.classList.add('active', 'flash'); item.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); setTimeout(() => item.classList.remove('flash'), 800); } 
            flashCanvasZone(zone.id); 
        }); 
        fabricCanvas.add(rect); 
    }); 
    fabricCanvas.renderAll(); 
}

function onMouseDown(e) { 
    if (currentTool !== 'zone') return; 
    isDrawing = true; 
    const p = fabricCanvas.getPointer(e.e); 
    startPoint = { x: p.x, y: p.y }; 
    currentRect = new fabric.Rect({ left: p.x, top: p.y, width: 0, height: 0, fill: 'rgba(59,130,246,0.3)', stroke: '#3B82F6', strokeWidth: 2, selectable: false, evented: false }); 
    fabricCanvas.add(currentRect); 
}

function onMouseMove(e) { 
    if (!isDrawing || !currentRect) return; 
    const p = fabricCanvas.getPointer(e.e); 
    if (p.x < startPoint.x) currentRect.set({ left: p.x }); 
    if (p.y < startPoint.y) currentRect.set({ top: p.y }); 
    currentRect.set({ width: Math.abs(p.x - startPoint.x), height: Math.abs(p.y - startPoint.y) }); 
    fabricCanvas.renderAll(); 
}

function onMouseUp() { 
    if (!isDrawing || !currentRect) return; 
    isDrawing = false; 
    if (currentRect.width < 10 || currentRect.height < 10) { fabricCanvas.remove(currentRect); currentRect = null; return; } 
    const c = document.getElementById('pdfCanvas'); 
    const w = parseInt(c.style.width), h = parseInt(c.style.height); 
    pendingZoneData = { page_number: currentPage, x: (currentRect.left/w)*100, y: (currentRect.top/h)*100, width: (currentRect.width/w)*100, height: (currentRect.height/h)*100 }; 
    extractTextFromZone(currentRect); 
    openZoneModal(); 
    fabricCanvas.remove(currentRect); 
    currentRect = null; 
}

async function extractTextFromZone(rect) { 
    const c = document.getElementById('pdfCanvas'); 
    const sx = c.width/parseInt(c.style.width), sy = c.height/parseInt(c.style.height); 
    const temp = document.createElement('canvas'); 
    temp.width = rect.width*sx*2; temp.height = rect.height*sy*2; 
    temp.getContext('2d').drawImage(c, rect.left*sx, rect.top*sy, rect.width*sx, rect.height*sy, 0, 0, temp.width, temp.height); 
    try { const r = await Tesseract.recognize(temp, 'fra'); document.getElementById('zoneText').value = r.data.text.trim(); } catch(e) { console.error(e); } 
}

// Navigation
function previousPage() { if (currentPage > 1) { currentPage--; document.getElementById('pageInput').value = currentPage; renderPage(currentPage); } }
function nextPage() { if (currentPage < totalPages) { currentPage++; document.getElementById('pageInput').value = currentPage; renderPage(currentPage); } }
function updateNavButtons() { document.getElementById('prevBtn').disabled = currentPage === 1; document.getElementById('nextBtn').disabled = currentPage === totalPages; }
document.getElementById('pageInput').addEventListener('change', function() { let p = parseInt(this.value); if (p >= 1 && p <= totalPages) { currentPage = p; renderPage(p); } else this.value = currentPage; });

// Zoom
function zoomIn() { scale = Math.min(scale + 0.25, 3); renderPage(currentPage); document.getElementById('zoomLevel').textContent = Math.round(scale*100)+'%'; }
function zoomOut() { scale = Math.max(scale - 0.25, 0.5); renderPage(currentPage); document.getElementById('zoomLevel').textContent = Math.round(scale*100)+'%'; }
function fitToWidth() { scale = 1; renderPage(currentPage); document.getElementById('zoomLevel').textContent = '100%'; }

// Outils
function setTool(tool) { 
    currentTool = tool; 
    document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active')); 
    document.querySelector(`[data-tool="${tool}"]`).classList.add('active'); 
    document.getElementById('pdfContainer').style.cursor = tool === 'zone' ? 'crosshair' : 'default'; 
    if (fabricCanvas) { fabricCanvas.defaultCursor = tool === 'zone' ? 'crosshair' : 'default'; fabricCanvas.hoverCursor = tool === 'zone' ? 'crosshair' : 'pointer'; } 
}

// Tabs
function showTab(id) { 
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active')); 
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active')); 
    document.querySelector(`[data-tab="${id}"]`).classList.add('active'); 
    document.getElementById('tab-' + id).classList.add('active'); 
}

// Modales
function setZoneColor(c) { document.getElementById('zoneColor').value = c; }
function setEditZoneColor(c) { document.getElementById('editZoneColor').value = c; }
function openZoneModal() { document.getElementById('zoneModal').classList.add('open'); }
function closeZoneModal() { document.getElementById('zoneModal').classList.remove('open'); document.getElementById('zoneLabel').value = ''; document.getElementById('zoneDescription').value = ''; document.getElementById('zoneText').value = ''; pendingZoneData = null; }
function openLinkModal() { document.getElementById('linkModal').classList.add('open'); }
function closeLinkModal() { document.getElementById('linkModal').classList.remove('open'); }
function openAnnotationModal() { document.getElementById('annotationModal').classList.add('open'); }
function closeAnnotationModal() { document.getElementById('annotationModal').classList.remove('open'); document.getElementById('annotationContent').value = ''; }
function closeEditZoneModal() { document.getElementById('editZoneModal').classList.remove('open'); }

// CRUD Zones
async function openEditZoneModal(id) { 
    try { 
        const r = await fetch(`/api/zones/${id}`).then(r => r.json()); 
        if (!r.success) return alert(LANG.error_zone_not_found); 
        const z = r.zone; 
        document.getElementById('editZoneId').value = z.id; 
        document.getElementById('editZoneLabel').value = z.label || ''; 
        document.getElementById('editZoneColor').value = z.color || '#10B981'; 
        document.getElementById('editZoneType').value = z.zone_type || 'custom'; 
        document.getElementById('editZoneDescription').value = z.description || ''; 
        document.getElementById('editZoneText').value = z.extracted_text || ''; 
        document.getElementById('editZoneModal').classList.add('open'); 
    } catch(e) { console.error(e); } 
}

async function saveZone() { 
    if (!pendingZoneData) return; 
    const data = { document_id: DOCUMENT_ID, ...pendingZoneData, label: document.getElementById('zoneLabel').value, zone_type: document.getElementById('zoneType').value, color: document.getElementById('zoneColor').value, description: document.getElementById('zoneDescription').value, extracted_text: document.getElementById('zoneText').value }; 
    const r = await fetch('/api/zones', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) }).then(r => r.json()); 
    if (r.success) { zones.push(r.zone); renderZones(); addZoneToSidebarSync(r.zone); closeZoneModal(); } 
}

async function updateZone() { 
    const id = document.getElementById('editZoneId').value; 
    const data = { label: document.getElementById('editZoneLabel').value, color: document.getElementById('editZoneColor').value, zone_type: document.getElementById('editZoneType').value, description: document.getElementById('editZoneDescription').value, extracted_text: document.getElementById('editZoneText').value }; 
    const r = await fetch(`/api/zones/${id}/update`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) }).then(r => r.json()); 
    if (r.success) { closeEditZoneModal(); location.reload(); } else alert(r.error || 'Error'); 
}

async function deleteZone(id) { 
    if (!confirm(LANG.delete_zone_confirm)) return; 
    await fetch(`/api/zones/${id}/delete`, { method: 'POST' }); 
    zones = zones.filter(z => String(z.id) !== String(id)); 
    renderZones(); 
    document.querySelector(`[data-zone-id="${id}"]`)?.remove(); 
    updateZonesBadge(); 
}

// Links
function createLinkFromZone(id) { document.getElementById('linkSourceZoneId').value = id; openLinkModal(); }

async function loadTargetZones() { 
    const docId = document.getElementById('linkTargetDocument').value; 
    const grp = document.getElementById('targetZoneGroup'), sel = document.getElementById('linkTargetZone'); 
    if (!docId) { grp.style.display = 'none'; return; } 
    const r = await fetch(`/api/documents/${docId}/zones`).then(r => r.json()); 
    sel.innerHTML = `<option value="">${LANG.entire_document}</option>`; 
    if (r.zones?.length) { r.zones.forEach(z => sel.innerHTML += `<option value="${z.id}">${z.label || LANG.zone + ' ' + z.id}</option>`); grp.style.display = 'block'; } 
}

async function createLink() { 
    const data = { source_zone_id: document.getElementById('linkSourceZoneId').value, target_document_id: document.getElementById('linkTargetDocument').value, target_zone_id: document.getElementById('linkTargetZone').value || null, link_type: document.getElementById('linkType').value, description: document.getElementById('linkDescription').value }; 
    if (!data.target_document_id) return alert(LANG.select_document); 
    const r = await fetch('/api/links', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) }).then(r => r.json()); 
    if (r.success) { closeLinkModal(); location.reload(); } else alert(r.error || 'Error'); 
}

// Annotations
async function createAnnotation() { 
    const content = document.getElementById('annotationContent').value; 
    if (!content.trim()) return alert(LANG.content_required); 
    const data = { document_id: DOCUMENT_ID, zone_id: document.getElementById('annotationZone').value || null, annotation_type: document.getElementById('annotationType').value, content }; 
    const r = await fetch('/api/annotations', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) }).then(r => r.json()); 
    if (r.success) { closeAnnotationModal(); location.reload(); } 
}

async function resolveAnnotation(id) { 
    await fetch(`/api/annotations/${id}/resolve`, { method: 'POST' }); 
    document.querySelector(`[data-annotation-id="${id}"]`)?.classList.add('resolved'); 
}

function focusZone(zoneId) { 
    const zone = zones.find(z => String(z.id) === String(zoneId)); 
    if (!zone) return; 
    document.querySelectorAll('.zone-item').forEach(el => el.classList.remove('active')); 
    document.querySelector(`.zone-item[data-zone-id="${zoneId}"]`)?.classList.add('active'); 
    if (parseInt(zone.page_number) !== currentPage) { 
        currentPage = parseInt(zone.page_number); 
        document.getElementById('pageInput').value = currentPage; 
        renderPage(currentPage).then(() => setTimeout(() => flashCanvasZone(zoneId), 100)); 
    } else { flashCanvasZone(zoneId); } 
}

// OCR
async function toggleOCR() { 
    if (!confirm(LANG.ocr_confirm)) return; 
    for (let i = 1; i <= totalPages; i++) { 
        const page = await pdfDoc.getPage(i); 
        const vp = page.getViewport({ scale: 2 }); 
        const c = document.createElement('canvas'); 
        c.width = vp.width; c.height = vp.height; 
        await page.render({ canvasContext: c.getContext('2d'), viewport: vp }).promise; 
        const r = await Tesseract.recognize(c, 'fra'); 
        await fetch('/api/documents/content', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ document_id: DOCUMENT_ID, page_number: i, content: r.data.text }) }); 
    } 
    alert(LANG.ocr_complete); 
}

// Mentions
function mentionUserOnZone(id, name) { document.getElementById('mentionZoneId').value = id; document.getElementById('mentionZoneName').value = name; document.getElementById('mentionModal').classList.add('open'); }
function closeMentionModal() { document.getElementById('mentionModal').classList.remove('open'); }
async function sendMention() { 
    const userId = document.getElementById('mentionUserId').value; 
    if (!userId) return alert(LANG.select_user); 
    const r = await fetch('/api/notifications/mention', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ user_id: userId, zone_id: document.getElementById('mentionZoneId').value, document_id: DOCUMENT_ID, message: document.getElementById('mentionMessage').value }) }).then(r => r.json()); 
    if (r.success) { closeMentionModal(); alert(LANG.notification_sent); } else alert(r.error || 'Error'); 
}

// ==========================================
// SYNCHRONISATION TEMPS RÉEL
// ==========================================
const SYNC_INTERVAL = 5000;
const VIEWERS_INTERVAL = 10000;
// Initialiser avec l'heure MySQL (pas PHP) pour éviter les décalages de timezone
// On utilise une requête SQL pour obtenir NOW() de MySQL
let lastSyncTimestamp = '<?php 
    try { 
        $stmt = db()->query("SELECT NOW() as now"); 
        echo $stmt->fetch()["now"]; 
    } catch(Exception $e) { 
        echo date("Y-m-d H:i:s"); 
    } 
?>';
let syncTimer = null, viewersTimer = null, isSyncActive = true;

async function pollSyncUpdates() {
    if (!isSyncActive) return;
    try {
        const response = await fetch(`/api/documents/sync?document_id=${DOCUMENT_ID}&since=${encodeURIComponent(lastSyncTimestamp)}`);
        const data = await response.json();
        if (data.success) {
            lastSyncTimestamp = data.timestamp;
            if (data.has_updates) { 
                console.log('[Sync] Mises à jour reçues:', data.updates);
                processSyncUpdates(data.updates); 
            }
        }
    } catch (e) { console.error('[Sync]', e); }
}

function processSyncUpdates(updates) {
    let hasNew = false;
    
    // Traiter les nouvelles zones
    if (updates.zones && updates.zones.length > 0) {
        updates.zones.forEach(zone => {
            // Vérifier si la zone existe déjà
            const exists = zones.find(z => z.id == zone.id);
            if (!exists) {
                const newZone = { 
                    id: zone.id, 
                    document_id: DOCUMENT_ID, 
                    page_number: zone.page_number || zone.page || 1, 
                    x: parseFloat(zone.x), 
                    y: parseFloat(zone.y), 
                    width: parseFloat(zone.width), 
                    height: parseFloat(zone.height), 
                    label: zone.label || zone.name || '', 
                    color: zone.color || '#10B981', 
                    zone_type: zone.zone_type || 'custom', 
                    description: zone.description || '', 
                    extracted_text: zone.extracted_text || '' 
                };
                zones.push(newZone);
                addZoneToSidebarSync(newZone, zone.created_by);
                hasNew = true;
            }
        });
        
        // Re-dessiner les zones si on a des nouveautés
        if (hasNew) {
            renderZones();
        }
    }
    
    // Traiter les nouvelles annotations
    if (updates.annotations && updates.annotations.length > 0) {
        updates.annotations.forEach(ann => {
            if (!document.querySelector(`[data-annotation-id="${ann.id}"]`)) { 
                addAnnotationToSidebarSync(ann); 
                hasNew = true; 
            }
        });
    }
    
    // Afficher notification
    if (hasNew) {
        showSyncNotification(LANG.sync_updated);
    }
}

function addZoneToSidebarSync(zone, createdBy) {
    const list = document.getElementById('zonesList');
    if (!list) return;
    
    // Supprimer l'état vide
    const emptyState = list.querySelector('.empty-state');
    if (emptyState) emptyState.remove();
    
    // Vérifier si existe déjà
    if (list.querySelector(`[data-zone-id="${zone.id}"]`)) return;
    
    const label = zone.label || LANG.zone + ' ' + zone.id;
    const color = zone.color || '#10B981';
    const pageNum = zone.page_number || 1;
    
    // Préparer le texte à afficher (description ou texte extrait)
    let textContent = '';
    if (zone.description && zone.description.trim()) {
        textContent = `<div class="zone-description">${escapeHtml(zone.description.substring(0, 80))}...</div>`;
    } else if (zone.extracted_text && zone.extracted_text.trim()) {
        textContent = `<div class="zone-text">${escapeHtml(zone.extracted_text.substring(0, 100))}...</div>`;
    }
    
    const div = document.createElement('div');
    div.className = 'zone-item sync-new';
    div.setAttribute('data-zone-id', zone.id);
    div.setAttribute('data-zone-page', pageNum);
    div.style.borderLeft = `4px solid ${color}`;
    
    div.innerHTML = `
        <div class="zone-header">
            <span class="zone-label">${escapeHtml(label)}</span>
            <span class="zone-page">P.${pageNum}</span>
        </div>
        ${textContent}
        <div class="zone-actions">
            <button onclick="event.stopPropagation(); openEditZoneModal(${zone.id})" title="Modifier"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
            <button onclick="event.stopPropagation(); createLinkFromZone(${zone.id})" title="Liaison"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></button>
            <button onclick="event.stopPropagation(); mentionUserOnZone(${zone.id}, '${escapeHtml(label)}')" title="Mentionner"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"/></svg></button>
            <button onclick="event.stopPropagation(); deleteZone(${zone.id})" title="Supprimer"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
        </div>
    `;
    
    // Ajouter événements click
    div.addEventListener('click', (e) => { 
        if (e.target.closest('.zone-actions')) return; 
        document.querySelectorAll('.zone-item').forEach(el => el.classList.remove('active')); 
        div.classList.add('active'); 
        if (parseInt(zone.page_number) !== currentPage) { 
            currentPage = parseInt(zone.page_number); 
            document.getElementById('pageInput').value = currentPage; 
            renderPage(currentPage).then(() => setTimeout(() => flashCanvasZone(zone.id), 100)); 
        } else { 
            flashCanvasZone(zone.id); 
        } 
    });
    
    list.insertBefore(div, list.firstChild);
    updateZonesBadge();
}

function addAnnotationToSidebarSync(ann) {
    const list = document.getElementById('annotationsList');
    if (!list) return;
    
    const emptyState = list.querySelector('.empty-state');
    if (emptyState) emptyState.remove();
    
    if (list.querySelector(`[data-annotation-id="${ann.id}"]`)) return;
    
    const div = document.createElement('div');
    div.className = 'annotation-item sync-new';
    div.setAttribute('data-annotation-id', ann.id);
    div.innerHTML = `
        <div class="annotation-header">
            <span>${escapeHtml(ann.user_name || 'Utilisateur')}</span>
            <span class="badge">${ann.annotation_type || 'note'}</span>
        </div>
        <div class="annotation-content">${escapeHtml(ann.content || '')}</div>
        <div class="annotation-footer"><span>À l'instant</span></div>
    `;
    list.insertBefore(div, list.firstChild);
}

function updateZonesBadge() { 
    const badge = document.getElementById('zonesBadge'); 
    if (badge) badge.textContent = zones.length; 
}

async function pollViewers() {
    if (!isSyncActive) return;
    try {
        const response = await fetch(`/api/documents/viewers?document_id=${DOCUMENT_ID}`);
        const data = await response.json();
        if (data.success) updateViewersDisplay(data.viewers || []);
    } catch (e) { console.error('[Sync] Viewers:', e); }
}

function updateViewersDisplay(viewers) {
    const container = document.getElementById('documentViewers');
    if (!container) return;
    
    if (!viewers || viewers.length === 0) { 
        container.innerHTML = ''; 
        return; 
    }
    
    const avatars = viewers.slice(0, 5).map(v => 
        `<div class="viewer-avatar ${v.is_me ? 'is-me' : ''}" title="${escapeHtml(v.name)}${v.is_me ? ' (vous)' : ''}">${v.initials || '?'}</div>`
    ).join('');
    
    container.innerHTML = `
        <div class="viewers-avatars">${avatars}</div>
        ${viewers.length > 1 ? `<span class="viewers-count">${viewers.length} en ligne</span>` : ''}
    `;
}

function showSyncNotification(msg) {
    const old = document.querySelector('.sync-notification');
    if (old) old.remove();
    
    const n = document.createElement('div');
    n.className = 'sync-notification';
    n.innerHTML = `<div class="sync-dot"></div><span>${escapeHtml(msg)}</span>`;
    document.body.appendChild(n);
    
    setTimeout(() => { 
        n.classList.add('hide'); 
        setTimeout(() => n.remove(), 300); 
    }, 4000);
}

function escapeHtml(t) { 
    if (!t) return ''; 
    const d = document.createElement('div'); 
    d.textContent = t; 
    return d.innerHTML; 
}

function startRealTimeSync() {
    pollViewers();
    syncTimer = setInterval(pollSyncUpdates, SYNC_INTERVAL);
    viewersTimer = setInterval(pollViewers, VIEWERS_INTERVAL);
    console.log('[Sync] ✅ Synchronisation temps réel activée pour document #' + DOCUMENT_ID);
}

function stopRealTimeSync() { 
    if (syncTimer) clearInterval(syncTimer); 
    if (viewersTimer) clearInterval(viewersTimer); 
}

// Gestion visibilité onglet
document.addEventListener('visibilitychange', () => { 
    if (document.hidden) { 
        isSyncActive = false; 
        stopRealTimeSync(); 
    } else { 
        isSyncActive = true; 
        startRealTimeSync(); 
    } 
});

// Cleanup avant fermeture
window.addEventListener('beforeunload', () => { 
    stopRealTimeSync(); 
    navigator.sendBeacon('/api/documents/leave', JSON.stringify({ document_id: DOCUMENT_ID })); 
});

// ==========================================
// DÉMARRAGE
// ==========================================
loadPDF();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
