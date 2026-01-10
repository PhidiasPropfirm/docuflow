<?php
$pageTitle = __('documents_title');
ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= __('documents') ?></h1>
        <p><?= __('documents_count', ['count' => count($documents ?? [])]) ?></p>
    </div>
    <button onclick="openUploadModal()" class="btn btn-primary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="17 8 12 3 7 8"/>
            <line x1="12" y1="3" x2="12" y2="15"/>
        </svg>
        <?= __('upload') ?>
    </button>
</div>

<!-- Filtres -->
<div class="filters-bar">
    <div class="filter-group">
        <input type="text" 
               class="filter-input" 
               placeholder="<?= __('search') ?>" 
               id="searchInput"
               value="<?= sanitize($_GET['q'] ?? '') ?>">
    </div>
    
    <div class="filter-group">
        <select class="filter-select" id="typeFilter">
            <option value=""><?= __('all_types') ?></option>
            <option value="invoice" <?= ($_GET['type'] ?? '') === 'invoice' ? 'selected' : '' ?>><?= __('type_invoice') ?></option>
            <option value="quote" <?= ($_GET['type'] ?? '') === 'quote' ? 'selected' : '' ?>><?= __('type_quote') ?></option>
            <option value="contract" <?= ($_GET['type'] ?? '') === 'contract' ? 'selected' : '' ?>><?= __('type_contract') ?></option>
            <option value="report" <?= ($_GET['type'] ?? '') === 'report' ? 'selected' : '' ?>><?= __('type_report') ?></option>
            <option value="other" <?= ($_GET['type'] ?? '') === 'other' ? 'selected' : '' ?>><?= __('type_other') ?></option>
        </select>
    </div>
    
    <div class="filter-group">
        <select class="filter-select" id="teamFilter">
            <option value=""><?= __('all_teams') ?></option>
            <?php foreach ($teams ?? [] as $team): ?>
            <option value="<?= $team['id'] ?>" <?= ($_GET['team'] ?? '') == $team['id'] ? 'selected' : '' ?>>
                <?= sanitize($team['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="filter-group">
        <input type="date" 
               class="filter-input" 
               id="dateFrom"
               placeholder="<?= __('date_format_placeholder') ?>"
               title="<?= __('date_from') ?>"
               value="<?= sanitize($_GET['from'] ?? '') ?>">
    </div>
    
    <div class="filter-group">
        <input type="date" 
               class="filter-input" 
               id="dateTo"
               placeholder="<?= __('date_format_placeholder') ?>"
               title="<?= __('date_to') ?>"
               value="<?= sanitize($_GET['to'] ?? '') ?>">
    </div>
    
    <button type="button" onclick="applyFilters()" class="btn btn-secondary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
        </svg>
        <?= __('filter') ?>
    </button>
</div>

<!-- Grille de documents -->
<div class="documents-grid">
    <?php if (!empty($documents)): ?>
        <?php foreach ($documents as $doc): ?>
        <div class="document-card" data-id="<?= $doc['id'] ?>">
            <div class="document-preview">
                <?php if ($doc['thumbnail'] ?? false): ?>
                <img src="<?= $doc['thumbnail'] ?>" alt="<?= sanitize($doc['name']) ?>">
                <?php else: ?>
                <div class="document-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                <?php endif; ?>
            </div>
            <div class="document-info">
                <h3 class="document-name" title="<?= sanitize($doc['name']) ?>">
                    <?= sanitize($doc['name']) ?>
                </h3>
                <div class="document-meta">
                    <span class="document-type"><?= __('type_' . ($doc['type'] ?? 'other')) ?></span>
                    <span class="document-date"><?= date(__('date_format'), strtotime($doc['created_at'])) ?></span>
                </div>
            </div>
            <div class="document-actions">
                <a href="/documents/<?= $doc['id'] ?>" class="btn btn-ghost btn-xs" title="<?= __('view') ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </a>
                <a href="/documents/<?= $doc['id'] ?>/download" class="btn btn-ghost btn-xs" title="<?= __('document_download') ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                </a>
                <button onclick="deleteDocument(<?= $doc['id'] ?>)" class="btn btn-ghost btn-xs btn-danger" title="<?= __('delete') ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
    <div class="empty-state large">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="12" y1="18" x2="12" y2="12"/>
            <line x1="9" y1="15" x2="15" y2="15"/>
        </svg>
        <h3><?= __('no_documents') ?></h3>
        <p><?= __('upload_first_document') ?></p>
        <button onclick="openUploadModal()" class="btn btn-primary"><?= __('upload_document') ?></button>
    </div>
    <?php endif; ?>
</div>

<!-- Modal upload -->
<div class="modal" id="uploadModal">
    <div class="modal-overlay" onclick="closeUploadModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3><?= __('upload_document') ?></h3>
            <button onclick="closeUploadModal()" class="modal-close">&times;</button>
        </div>
        <form action="/documents/upload" method="POST" enctype="multipart/form-data" id="uploadForm">
            <?= csrf_field() ?>
            
            <div class="modal-body">
                <div class="upload-zone" id="uploadZone">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <p><?= __('drag_drop') ?></p>
                    <input type="file" name="document" id="fileInput" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>
                
                <div class="form-group">
                    <label for="docType"><?= __('document_type') ?></label>
                    <select name="type" id="docType" required>
                        <option value=""><?= __('select_type') ?></option>
                        <option value="invoice"><?= __('type_invoice') ?></option>
                        <option value="quote"><?= __('type_quote') ?></option>
                        <option value="contract"><?= __('type_contract') ?></option>
                        <option value="report"><?= __('type_report') ?></option>
                        <option value="other"><?= __('type_other') ?></option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="docTeam"><?= __('team') ?></label>
                    <select name="team_id" id="docTeam">
                        <option value=""><?= __('select_team') ?></option>
                        <?php foreach ($teams ?? [] as $team): ?>
                        <option value="<?= $team['id'] ?>"><?= sanitize($team['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="closeUploadModal()" class="btn btn-secondary"><?= __('cancel') ?></button>
                <button type="submit" class="btn btn-primary"><?= __('upload') ?></button>
            </div>
        </form>
    </div>
</div>

<script>
const TRANSLATIONS = {
    delete_document_confirm: '<?= __('delete_document_confirm') ?>',
    document_deleted: '<?= __('document_deleted') ?>'
};

function openUploadModal() {
    document.getElementById('uploadModal').classList.add('open');
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.remove('open');
    document.getElementById('uploadForm').reset();
}

function deleteDocument(id) {
    if (confirm(TRANSLATIONS.delete_document_confirm)) {
        fetch(`/documents/${id}`, { method: 'DELETE' })
            .then(() => {
                document.querySelector(`.document-card[data-id="${id}"]`).remove();
            });
    }
}

function applyFilters() {
    const params = new URLSearchParams();
    
    const search = document.getElementById('searchInput').value;
    const type = document.getElementById('typeFilter').value;
    const team = document.getElementById('teamFilter').value;
    const from = document.getElementById('dateFrom').value;
    const to = document.getElementById('dateTo').value;
    
    if (search) params.set('q', search);
    if (type) params.set('type', type);
    if (team) params.set('team', team);
    if (from) params.set('from', from);
    if (to) params.set('to', to);
    
    window.location.href = '/documents?' + params.toString();
}

// Drag & Drop
const uploadZone = document.getElementById('uploadZone');
const fileInput = document.getElementById('fileInput');

uploadZone.addEventListener('click', () => fileInput.click());

uploadZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadZone.classList.add('dragover');
});

uploadZone.addEventListener('dragleave', () => {
    uploadZone.classList.remove('dragover');
});

uploadZone.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadZone.classList.remove('dragover');
    if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
    }
});

// Filter on Enter
document.getElementById('searchInput').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') applyFilters();
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
