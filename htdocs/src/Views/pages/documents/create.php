<?php
/**
 * DocuFlow - Page de création de document (bilingue + upload multiple)
 * Fichier: htdocs/src/Views/pages/documents/create.php
 */
$pageTitle = __('new_document');
ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/documents" class="back-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
            <?= __('back_to_documents') ?>
        </a>
        <h1><?= __('new_document') ?></h1>
    </div>
    
    <!-- Toggle mode simple/multiple -->
    <div class="upload-mode-toggle">
        <button type="button" class="mode-btn active" data-mode="single" onclick="setUploadMode('single')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
            <?= __('single_document') ?>
        </button>
        <button type="button" class="mode-btn" data-mode="multiple" onclick="setUploadMode('multiple')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="9" rx="1"/>
                <rect x="14" y="3" width="7" height="9" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            <?= __('multiple_documents') ?>
        </button>
    </div>
</div>

<!-- ======================= -->
<!-- MODE SINGLE (par défaut) -->
<!-- ======================= -->
<div id="singleMode" class="upload-mode">
    <div class="form-container">
        <form action="/documents" method="POST" enctype="multipart/form-data" class="document-form">
            <?= csrf_field() ?>
            <input type="hidden" name="upload_mode" value="single">
            
            <!-- Zone d'upload -->
            <div class="upload-zone" id="uploadZone">
                <input type="file" name="document" id="documentFile" accept=".pdf" required>
                <div class="upload-content">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <h3><?= __('drop_pdf_here') ?></h3>
                    <p><?= __('or_click_to_browse') ?></p>
                    <span class="upload-hint"><?= __('pdf_only') ?> • <?= __('max_size') ?> <?= formatFileSize(MAX_FILE_SIZE) ?></span>
                </div>
                <div class="upload-preview" id="uploadPreview" style="display: none;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <span class="file-name" id="fileName"></span>
                    <span class="file-size" id="fileSize"></span>
                    <button type="button" class="remove-file" onclick="removeFile()">&times;</button>
                </div>
            </div>
            
            <!-- Informations du document -->
            <div class="form-section">
                <h2><?= __('informations') ?></h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="title"><?= __('document_title') ?> *</label>
                        <input type="text" id="title" name="title" required 
                               placeholder="<?= __('document_title_placeholder') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="document_type"><?= __('document_type') ?></label>
                        <select id="document_type" name="document_type">
                            <?php foreach ($documentTypes as $key => $label): ?>
                            <option value="<?= $key ?>"><?= __('type_' . $key) !== 'type_' . $key ? __('type_' . $key) : $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description"><?= __('description') ?></label>
                    <textarea id="description" name="description" rows="3" 
                              placeholder="<?= __('document_description_placeholder') ?>"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="reference_number"><?= __('reference_number') ?></label>
                        <input type="text" id="reference_number" name="reference_number" 
                               placeholder="<?= __('reference_placeholder') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="document_date"><?= __('document_date') ?></label>
                        <input type="date" id="document_date" name="document_date">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="total_amount"><?= __('amount') ?></label>
                        <input type="number" id="total_amount" name="total_amount" step="0.01" min="0" 
                               placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label for="currency"><?= __('currency') ?></label>
                        <select id="currency" name="currency">
                            <option value="EUR">EUR (€)</option>
                            <option value="USD">USD ($)</option>
                            <option value="GBP">GBP (£)</option>
                            <option value="CHF">CHF</option>
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
                        <option value="<?= $team['id'] ?>"><?= sanitize($team['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-hint"><?= __('team_visibility_hint') ?></small>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="form-actions">
                <a href="/documents" class="btn btn-ghost"><?= __('cancel') ?></a>
                <button type="submit" class="btn btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <?= __('upload_document') ?>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================= -->
<!-- MODE MULTIPLE -->
<!-- ======================= -->
<div id="multipleMode" class="upload-mode" style="display: none;">
    <div class="form-container">
        <form action="/documents/bulk" method="POST" enctype="multipart/form-data" class="document-form" id="bulkUploadForm">
            <?= csrf_field() ?>
            <input type="hidden" name="upload_mode" value="multiple">
            
            <!-- Zone d'upload multiple -->
            <div class="upload-zone upload-zone-multiple" id="uploadZoneMultiple">
                <input type="file" name="documents[]" id="documentsFiles" accept=".pdf" multiple>
                <div class="upload-content" id="uploadContentMultiple">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <h3><?= __('drop_pdfs_here') ?></h3>
                    <p><?= __('or_click_to_browse_multiple') ?></p>
                    <span class="upload-hint"><?= __('pdf_only') ?> • <?= __('max_size') ?> <?= formatFileSize(MAX_FILE_SIZE) ?> <?= __('per_file') ?></span>
                </div>
            </div>
            
            <!-- Liste des fichiers sélectionnés -->
            <div class="files-list-container" id="filesListContainer" style="display: none;">
                <div class="files-list-header">
                    <h3>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                        <span id="filesCount">0</span> <?= __('files_selected') ?>
                    </h3>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="clearAllFiles()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        <?= __('clear_all') ?>
                    </button>
                </div>
                <div class="files-list" id="filesList"></div>
                <button type="button" class="btn btn-ghost btn-sm add-more-btn" onclick="document.getElementById('documentsFiles').click()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <?= __('add_more_files') ?>
                </button>
            </div>
            
            <!-- Options communes -->
            <div class="form-section" id="bulkOptions" style="display: none;">
                <h2><?= __('common_options') ?></h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="bulk_document_type"><?= __('document_type') ?></label>
                        <select id="bulk_document_type" name="document_type">
                            <option value=""><?= __('keep_default') ?></option>
                            <?php foreach ($documentTypes as $key => $label): ?>
                            <option value="<?= $key ?>"><?= __('type_' . $key) !== 'type_' . $key ? __('type_' . $key) : $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-hint"><?= __('apply_to_all_files') ?></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="bulk_team_id"><?= __('team') ?></label>
                        <select id="bulk_team_id" name="team_id">
                            <option value=""><?= __('no_team') ?></option>
                            <?php foreach ($teams as $team): ?>
                            <option value="<?= $team['id'] ?>"><?= sanitize($team['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-hint"><?= __('apply_to_all_files') ?></small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="use_filename_as_title" value="1" checked>
                        <span><?= __('use_filename_as_title') ?></span>
                    </label>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="form-actions" id="bulkActions" style="display: none;">
                <a href="/documents" class="btn btn-ghost"><?= __('cancel') ?></a>
                <button type="submit" class="btn btn-primary" id="bulkSubmitBtn" disabled>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <?= __('upload_documents') ?> (<span id="uploadCount">0</span>)
                </button>
            </div>
        </form>
    </div>
    
    <!-- Progress modal -->
    <div class="modal" id="uploadProgressModal">
        <div class="modal-overlay"></div>
        <div class="modal-content upload-progress-modal">
            <div class="modal-header">
                <h3><?= __('uploading_documents') ?></h3>
            </div>
            <div class="modal-body">
                <div class="upload-progress-info">
                    <span id="uploadProgressText"><?= __('preparing') ?>...</span>
                    <span id="uploadProgressPercent">0%</span>
                </div>
                <div class="upload-progress-bar">
                    <div class="upload-progress-fill" id="uploadProgressFill"></div>
                </div>
                <div class="upload-progress-details" id="uploadProgressDetails"></div>
            </div>
        </div>
    </div>
</div>

<style>
/* Mode Toggle */
.upload-mode-toggle {
    display: flex;
    gap: 8px;
    background: var(--bg-secondary);
    padding: 4px;
    border-radius: 10px;
    border: 1px solid var(--border-color);
}

.mode-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: none;
    background: transparent;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.2s;
}

.mode-btn:hover {
    color: var(--text-primary);
    background: var(--bg-tertiary);
}

.mode-btn.active {
    background: var(--bg-primary);
    color: var(--primary);
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.mode-btn svg {
    flex-shrink: 0;
}

/* Upload Zone Multiple */
.upload-zone-multiple {
    min-height: 200px;
}

/* Files List */
.files-list-container {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 24px;
}

.files-list-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
}

.files-list-header h3 {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
}

.files-list {
    max-height: 400px;
    overflow-y: auto;
}

.file-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    border-bottom: 1px solid var(--border-color);
    transition: background 0.15s;
}

.file-item:last-child {
    border-bottom: none;
}

.file-item:hover {
    background: var(--bg-secondary);
}

.file-item-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #3B82F6, #2563EB);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.file-item-info {
    flex: 1;
    min-width: 0;
}

.file-item-name {
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}

.file-item-meta {
    font-size: 0.8125rem;
    color: var(--text-muted);
}

.file-item-status {
    display: flex;
    align-items: center;
    gap: 8px;
}

.file-item-status .status-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.file-item-status .status-icon.pending {
    background: var(--bg-tertiary);
    color: var(--text-muted);
}

.file-item-status .status-icon.success {
    background: #D1FAE5;
    color: #059669;
}

.file-item-status .status-icon.error {
    background: #FEE2E2;
    color: #DC2626;
}

.file-item-remove {
    background: none;
    border: none;
    color: var(--text-muted);
    padding: 8px;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.15s;
}

.file-item-remove:hover {
    background: #FEE2E2;
    color: #DC2626;
}

.add-more-btn {
    width: 100%;
    justify-content: center;
    border-top: 1px dashed var(--border-color);
    border-radius: 0;
    padding: 16px;
}

/* Common Options */
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: var(--primary);
}

/* Progress Modal */
.upload-progress-modal {
    max-width: 480px;
}

.upload-progress-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    font-size: 0.875rem;
}

.upload-progress-bar {
    height: 8px;
    background: var(--bg-tertiary);
    border-radius: 4px;
    overflow: hidden;
}

.upload-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #3B82F6, #8B5CF6);
    border-radius: 4px;
    width: 0%;
    transition: width 0.3s ease;
}

.upload-progress-details {
    margin-top: 16px;
    max-height: 200px;
    overflow-y: auto;
    font-size: 0.8125rem;
}

.progress-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 0;
    color: var(--text-secondary);
}

.progress-item.success {
    color: #059669;
}

.progress-item.error {
    color: #DC2626;
}

.progress-summary {
    padding: 12px;
    background: var(--bg-secondary);
    border-radius: 8px;
    margin-bottom: 12px;
    text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
    .upload-mode-toggle {
        width: 100%;
    }
    
    .mode-btn {
        flex: 1;
        justify-content: center;
    }
    
    .page-header {
        flex-direction: column;
        gap: 16px;
    }
}
</style>

<script>
// Traductions pour JavaScript
const LANG = {
    pdf_only_alert: '<?= __('pdf_only_alert') ?>',
    file_too_large: '<?= __('file_too_large') ?>',
    uploading: '<?= __('uploading') ?>',
    upload_complete: '<?= __('upload_complete') ?>',
    files_uploaded_successfully: '<?= __('files_uploaded_successfully') ?>'
};

const MAX_FILE_SIZE = <?= MAX_FILE_SIZE ?>;

// ========================================
// MODE TOGGLE
// ========================================
function setUploadMode(mode) {
    document.querySelectorAll('.mode-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.mode === mode);
    });
    
    document.getElementById('singleMode').style.display = mode === 'single' ? 'block' : 'none';
    document.getElementById('multipleMode').style.display = mode === 'multiple' ? 'block' : 'none';
}

// ========================================
// SINGLE MODE
// ========================================
const uploadZone = document.getElementById('uploadZone');
const fileInput = document.getElementById('documentFile');
const uploadPreview = document.getElementById('uploadPreview');
const uploadContent = uploadZone.querySelector('.upload-content');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    uploadZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    uploadZone.addEventListener(eventName, () => uploadZone.classList.add('drag-over'), false);
});

['dragleave', 'drop'].forEach(eventName => {
    uploadZone.addEventListener(eventName, () => uploadZone.classList.remove('drag-over'), false);
});

uploadZone.addEventListener('drop', handleDrop, false);
fileInput.addEventListener('change', handleFileSelect, false);

function handleDrop(e) {
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(files[0]);
        fileInput.files = dataTransfer.files;
    }
    handleFiles(files);
}

function handleFileSelect(e) {
    handleFiles(e.target.files);
}

function handleFiles(files) {
    if (files.length > 0) {
        const file = files[0];
        
        if (file.type !== 'application/pdf') {
            alert(LANG.pdf_only_alert);
            return;
        }
        
        if (file.size > MAX_FILE_SIZE) {
            alert(LANG.file_too_large);
            return;
        }
        
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = formatFileSize(file.size);
        uploadContent.style.display = 'none';
        uploadPreview.style.display = 'flex';
        
        const titleInput = document.getElementById('title');
        if (!titleInput.value) {
            titleInput.value = file.name.replace('.pdf', '').replace(/_/g, ' ');
        }
    }
}

function removeFile() {
    fileInput.value = '';
    uploadContent.style.display = 'flex';
    uploadPreview.style.display = 'none';
}

// ========================================
// MULTIPLE MODE
// ========================================
let selectedFiles = [];

const uploadZoneMultiple = document.getElementById('uploadZoneMultiple');
const filesInput = document.getElementById('documentsFiles');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    uploadZoneMultiple.addEventListener(eventName, preventDefaults, false);
});

['dragenter', 'dragover'].forEach(eventName => {
    uploadZoneMultiple.addEventListener(eventName, () => uploadZoneMultiple.classList.add('drag-over'), false);
});

['dragleave', 'drop'].forEach(eventName => {
    uploadZoneMultiple.addEventListener(eventName, () => uploadZoneMultiple.classList.remove('drag-over'), false);
});

uploadZoneMultiple.addEventListener('drop', handleDropMultiple, false);
filesInput.addEventListener('change', handleFilesSelectMultiple, false);

function handleDropMultiple(e) {
    const files = Array.from(e.dataTransfer.files);
    addFilesToList(files);
}

function handleFilesSelectMultiple(e) {
    const files = Array.from(e.target.files);
    addFilesToList(files);
}

function addFilesToList(files) {
    files.forEach(file => {
        if (file.type !== 'application/pdf') return;
        if (file.size > MAX_FILE_SIZE) return;
        if (selectedFiles.some(f => f.name === file.name && f.size === file.size)) return;
        selectedFiles.push(file);
    });
    updateFilesList();
}

function updateFilesList() {
    const container = document.getElementById('filesListContainer');
    const list = document.getElementById('filesList');
    const count = document.getElementById('filesCount');
    const uploadCount = document.getElementById('uploadCount');
    const submitBtn = document.getElementById('bulkSubmitBtn');
    const options = document.getElementById('bulkOptions');
    const actions = document.getElementById('bulkActions');
    const uploadContentMult = document.getElementById('uploadContentMultiple');
    
    if (selectedFiles.length === 0) {
        container.style.display = 'none';
        options.style.display = 'none';
        actions.style.display = 'none';
        uploadContentMult.style.display = 'flex';
        submitBtn.disabled = true;
        return;
    }
    
    container.style.display = 'block';
    options.style.display = 'block';
    actions.style.display = 'flex';
    uploadContentMult.style.display = 'none';
    submitBtn.disabled = false;
    
    count.textContent = selectedFiles.length;
    uploadCount.textContent = selectedFiles.length;
    
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    filesInput.files = dataTransfer.files;
    
    list.innerHTML = selectedFiles.map((file, index) => `
        <div class="file-item" data-index="${index}">
            <div class="file-item-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <div class="file-item-info">
                <span class="file-item-name">${escapeHtml(file.name)}</span>
                <span class="file-item-meta">${formatFileSize(file.size)}</span>
            </div>
            <div class="file-item-status">
                <span class="status-icon pending">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                    </svg>
                </span>
            </div>
            <button type="button" class="file-item-remove" onclick="removeFileAt(${index})">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
    `).join('');
}

function removeFileAt(index) {
    selectedFiles.splice(index, 1);
    updateFilesList();
}

function clearAllFiles() {
    selectedFiles = [];
    updateFilesList();
}

// ========================================
// BULK UPLOAD SUBMIT
// ========================================
document.getElementById('bulkUploadForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (selectedFiles.length === 0) return;
    
    const modal = document.getElementById('uploadProgressModal');
    const progressFill = document.getElementById('uploadProgressFill');
    const progressText = document.getElementById('uploadProgressText');
    const progressPercent = document.getElementById('uploadProgressPercent');
    const progressDetails = document.getElementById('uploadProgressDetails');
    
    modal.classList.add('open');
    
    const formData = new FormData(this);
    let completed = 0;
    let results = [];
    
    for (let i = 0; i < selectedFiles.length; i++) {
        const file = selectedFiles[i];
        const singleFormData = new FormData();
        
        singleFormData.append('<?= CSRF_TOKEN_NAME ?>', formData.get('<?= CSRF_TOKEN_NAME ?>'));
        singleFormData.append('document', file);
        singleFormData.append('title', file.name.replace('.pdf', '').replace(/_/g, ' '));
        singleFormData.append('document_type', formData.get('document_type') || 'other');
        singleFormData.append('team_id', formData.get('team_id') || '');
        
        progressText.textContent = `${LANG.uploading} ${file.name}...`;
        
        try {
            const response = await fetch('/documents/upload-ajax', {
                method: 'POST',
                body: singleFormData
            });
            
            const result = await response.json();
            results.push({ file: file.name, success: result.success, error: result.error });
            
            const fileItem = document.querySelector(`.file-item[data-index="${i}"] .status-icon`);
            if (fileItem) {
                fileItem.className = result.success ? 'status-icon success' : 'status-icon error';
                fileItem.innerHTML = result.success 
                    ? '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>'
                    : '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
            }
            
        } catch (error) {
            results.push({ file: file.name, success: false, error: error.message });
        }
        
        completed++;
        const percent = Math.round((completed / selectedFiles.length) * 100);
        progressFill.style.width = percent + '%';
        progressPercent.textContent = percent + '%';
    }
    
    progressText.textContent = LANG.upload_complete;
    
    const successCount = results.filter(r => r.success).length;
    progressDetails.innerHTML = `
        <div class="progress-summary">
            <strong>${successCount}/${results.length} ${LANG.files_uploaded_successfully}</strong>
        </div>
        ${results.map(r => `
            <div class="progress-item ${r.success ? 'success' : 'error'}">
                ${r.success ? '✓' : '✗'} ${escapeHtml(r.file)}
                ${r.error ? `<small>(${escapeHtml(r.error)})</small>` : ''}
            </div>
        `).join('')}
    `;
    
    if (successCount === results.length) {
        setTimeout(() => {
            window.location.href = '/documents';
        }, 1500);
    }
});

// ========================================
// UTILITIES
// ========================================
function formatFileSize(bytes) {
    if (bytes === 0) return '0 o';
    const k = 1024;
    const sizes = ['o', 'Ko', 'Mo', 'Go'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
