<?php
/**
 * DocuFlow - Page d'édition de document (bilingue)
 * Fichier: htdocs/src/Views/pages/documents/edit.php
 */
$pageTitle = __('edit_document') . ' : ' . $document['title'];
ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/documents/<?= $document['id'] ?>" class="back-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
            <?= __('back_to_document') ?>
        </a>
        <h1><?= __('edit_document') ?></h1>
    </div>
</div>

<div class="form-container">
    <form action="/documents/<?= $document['id'] ?>" method="POST" class="document-form">
        <?= csrf_field() ?>
        
        <!-- Aperçu du fichier -->
        <div class="current-file">
            <div class="file-preview">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <div class="file-info">
                <span class="file-name"><?= sanitize($document['original_name']) ?></span>
                <span class="file-meta"><?= formatFileSize($document['file_size']) ?> • <?= __('uploaded_on') ?> <?= formatDate($document['created_at']) ?></span>
            </div>
            <a href="/uploads/<?= $document['filename'] ?>" target="_blank" class="btn btn-ghost btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                    <polyline points="15 3 21 3 21 9"/>
                    <line x1="10" y1="14" x2="21" y2="3"/>
                </svg>
                <?= __('view_pdf') ?>
            </a>
        </div>
        
        <!-- Informations du document -->
        <div class="form-section">
            <h2><?= __('informations') ?></h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="title"><?= __('document_title') ?> *</label>
                    <input type="text" id="title" name="title" required 
                           value="<?= sanitize($document['title']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="document_type"><?= __('document_type') ?></label>
                    <select id="document_type" name="document_type">
                        <?php foreach ($documentTypes as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $document['document_type'] === $key ? 'selected' : '' ?>><?= __('type_' . $key) !== 'type_' . $key ? __('type_' . $key) : $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description"><?= __('description') ?></label>
                <textarea id="description" name="description" rows="3"><?= sanitize($document['description'] ?? '') ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="reference_number"><?= __('reference_number') ?></label>
                    <input type="text" id="reference_number" name="reference_number" 
                           value="<?= sanitize($document['reference_number'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="document_date"><?= __('document_date') ?></label>
                    <input type="date" id="document_date" name="document_date" 
                           value="<?= $document['document_date'] ?? '' ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="total_amount"><?= __('amount') ?></label>
                    <input type="number" id="total_amount" name="total_amount" step="0.01" min="0" 
                           value="<?= $document['total_amount'] ?? '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="currency"><?= __('currency') ?></label>
                    <select id="currency" name="currency">
                        <option value="EUR" <?= ($document['currency'] ?? 'EUR') === 'EUR' ? 'selected' : '' ?>>EUR (€)</option>
                        <option value="USD" <?= ($document['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>USD ($)</option>
                        <option value="GBP" <?= ($document['currency'] ?? '') === 'GBP' ? 'selected' : '' ?>>GBP (£)</option>
                        <option value="CHF" <?= ($document['currency'] ?? '') === 'CHF' ? 'selected' : '' ?>>CHF</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Attribution -->
        <div class="form-section">
            <h2><?= __('attribution') ?></h2>
            
            <div class="form-group">
                <label for="team_id"><?= __('team') ?> (<?= __('optional') ?>)</label>
                <select id="team_id" name="team_id">
                    <option value=""><?= __('no_team') ?></option>
                    <?php foreach ($teams as $team): ?>
                    <option value="<?= $team['id'] ?>" <?= ($document['team_id'] ?? '') == $team['id'] ? 'selected' : '' ?>><?= sanitize($team['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="form-actions">
            <a href="/documents/<?= $document['id'] ?>" class="btn btn-ghost"><?= __('cancel') ?></a>
            <button type="submit" class="btn btn-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <?= __('save') ?>
            </button>
        </div>
    </form>
    
    <!-- Zone de danger -->
    <div class="form-section danger-zone">
        <h2><?= __('danger_zone') ?></h2>
        <p><?= __('delete_document_warning') ?></p>
        <form action="/documents/<?= $document['id'] ?>/delete" method="POST" onsubmit="return confirm('<?= __('delete_document_confirm_full') ?>');">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-danger">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    <line x1="10" y1="11" x2="10" y2="17"/>
                    <line x1="14" y1="11" x2="14" y2="17"/>
                </svg>
                <?= __('delete_this_document') ?>
            </button>
        </form>
    </div>
</div>

<style>
.current-file {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-lg);
    margin-bottom: 1.5rem;
}
.file-preview {
    width: 64px;
    height: 64px;
    background: var(--bg-primary);
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
}
.file-preview svg {
    color: var(--primary);
}
.file-info {
    flex: 1;
}
.file-info .file-name {
    font-weight: 600;
    display: block;
    margin-bottom: 0.25rem;
}
.file-info .file-meta {
    font-size: 0.875rem;
    color: var(--text-muted);
}
.danger-zone {
    border-color: var(--danger, #EF4444);
    background: rgba(239, 68, 68, 0.05);
    margin-top: 2rem;
}
.danger-zone h2 {
    color: var(--danger, #EF4444);
}
.danger-zone p {
    color: var(--text-secondary);
    margin-bottom: 1rem;
}
.btn-danger {
    background: linear-gradient(135deg, #EF4444, #DC2626);
    color: white;
    border: none;
}
.btn-danger:hover {
    background: linear-gradient(135deg, #DC2626, #B91C1C);
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
