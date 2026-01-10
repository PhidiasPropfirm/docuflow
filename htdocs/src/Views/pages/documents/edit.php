<?php
$pageTitle = 'Modifier : ' . $document['title'];
ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/documents/<?= $document['id'] ?>" class="back-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
            Retour au document
        </a>
        <h1>Modifier le document</h1>
    </div>
</div>

<div class="form-container">
    <form action="/documents/<?= $document['id'] ?>/update" method="POST" class="document-form">
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
                <span class="file-meta"><?= formatFileSize($document['file_size']) ?> • Uploadé le <?= formatDate($document['created_at']) ?></span>
            </div>
            <a href="/uploads/<?= $document['filename'] ?>" target="_blank" class="btn btn-ghost btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                    <polyline points="15 3 21 3 21 9"/>
                    <line x1="10" y1="14" x2="21" y2="3"/>
                </svg>
                Voir le PDF
            </a>
        </div>
        
        <!-- Informations du document -->
        <div class="form-section">
            <h2>Informations</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="title">Titre du document *</label>
                    <input type="text" id="title" name="title" required 
                           value="<?= sanitize($document['title']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="document_type">Type de document</label>
                    <select id="document_type" name="document_type">
                        <?php foreach ($documentTypes as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $document['document_type'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"><?= sanitize($document['description']) ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="reference_number">Numéro de référence</label>
                    <input type="text" id="reference_number" name="reference_number" 
                           value="<?= sanitize($document['reference_number']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="document_date">Date du document</label>
                    <input type="date" id="document_date" name="document_date" 
                           value="<?= $document['document_date'] ? date('Y-m-d', strtotime($document['document_date'])) : '' ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="total_amount">Montant</label>
                    <input type="number" id="total_amount" name="total_amount" step="0.01" 
                           value="<?= $document['total_amount'] ?>">
                </div>
                
                <div class="form-group">
                    <label for="currency">Devise</label>
                    <select id="currency" name="currency">
                        <option value="EUR" <?= $document['currency'] === 'EUR' ? 'selected' : '' ?>>EUR (€)</option>
                        <option value="USD" <?= $document['currency'] === 'USD' ? 'selected' : '' ?>>USD ($)</option>
                        <option value="GBP" <?= $document['currency'] === 'GBP' ? 'selected' : '' ?>>GBP (£)</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Attribution -->
        <div class="form-section">
            <h2>Attribution</h2>
            
            <div class="form-group">
                <label for="team_id">Équipe</label>
                <select id="team_id" name="team_id">
                    <option value="">Aucune équipe</option>
                    <?php foreach ($teams as $team): ?>
                    <option value="<?= $team['id'] ?>" <?= $document['team_id'] == $team['id'] ? 'selected' : '' ?>><?= sanitize($team['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="form-actions">
            <a href="/documents/<?= $document['id'] ?>" class="btn btn-ghost">Annuler</a>
            <button type="submit" class="btn btn-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Enregistrer
            </button>
        </div>
    </form>
    
    <!-- Zone de danger -->
    <div class="form-section danger-zone">
        <h2>Zone de danger</h2>
        <p>La suppression d'un document est irréversible. Toutes les zones, liaisons et annotations associées seront également supprimées.</p>
        <form action="/documents/<?= $document['id'] ?>/delete" method="POST" data-confirm="Êtes-vous sûr de vouloir supprimer ce document ? Cette action est irréversible.">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-danger">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    <line x1="10" y1="11" x2="10" y2="17"/>
                    <line x1="14" y1="11" x2="14" y2="17"/>
                </svg>
                Supprimer ce document
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
    border-color: var(--danger);
    background: rgba(239, 68, 68, 0.05);
}
.danger-zone h2 {
    color: var(--danger);
}
.danger-zone p {
    color: var(--text-secondary);
    margin-bottom: 1rem;
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
