<?php
$pageTitle = 'Nouveau document';
ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/documents" class="back-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
            Retour aux documents
        </a>
        <h1>Nouveau document</h1>
    </div>
</div>

<div class="form-container">
    <form action="/documents" method="POST" enctype="multipart/form-data" class="document-form">
        <?= csrf_field() ?>
        
        <!-- Zone d'upload -->
        <div class="upload-zone" id="uploadZone">
            <input type="file" name="document" id="documentFile" accept=".pdf" required>
            <div class="upload-content">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                <h3>Glissez votre fichier PDF ici</h3>
                <p>ou cliquez pour parcourir</p>
                <span class="upload-hint">PDF uniquement • Max <?= formatFileSize(MAX_FILE_SIZE) ?></span>
            </div>
            <div class="upload-preview" id="uploadPreview" style="display: none;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <span class="file-name" id="fileName"></span>
                <span class="file-size" id="fileSize"></span>
                <button type="button" class="remove-file" onclick="removeFile()">×</button>
            </div>
        </div>
        
        <!-- Informations du document -->
        <div class="form-section">
            <h2>Informations</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="title">Titre du document *</label>
                    <input type="text" id="title" name="title" required placeholder="Ex: Facture fournisseur Mars 2024">
                </div>
                
                <div class="form-group">
                    <label for="document_type">Type de document</label>
                    <select id="document_type" name="document_type">
                        <?php foreach ($documentTypes as $key => $label): ?>
                        <option value="<?= $key ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" placeholder="Description optionnelle du document..."></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="reference_number">Numéro de référence</label>
                    <input type="text" id="reference_number" name="reference_number" placeholder="Ex: FAC-2024-001">
                </div>
                
                <div class="form-group">
                    <label for="document_date">Date du document</label>
                    <input type="date" id="document_date" name="document_date">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="total_amount">Montant</label>
                    <input type="number" id="total_amount" name="total_amount" step="0.01" placeholder="0.00">
                </div>
                
                <div class="form-group">
                    <label for="currency">Devise</label>
                    <select id="currency" name="currency">
                        <option value="EUR">EUR (€)</option>
                        <option value="USD">USD ($)</option>
                        <option value="GBP">GBP (£)</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Attribution -->
        <div class="form-section">
            <h2>Attribution</h2>
            
            <div class="form-group">
                <label for="team_id">Équipe (optionnel)</label>
                <select id="team_id" name="team_id">
                    <option value="">Aucune équipe</option>
                    <?php foreach ($teams as $team): ?>
                    <option value="<?= $team['id'] ?>"><?= sanitize($team['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="form-hint">Le document sera visible par tous, mais peut être associé à une équipe</span>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="form-actions">
            <a href="/documents" class="btn btn-ghost">Annuler</a>
            <button type="submit" class="btn btn-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Uploader le document
            </button>
        </div>
    </form>
</div>

<script>
// Gestion du drag & drop
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
    const dt = e.dataTransfer;
    const files = dt.files;
    
    // Assigner le fichier au input
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
        
        // Vérification du type
        if (file.type !== 'application/pdf') {
            alert('Seuls les fichiers PDF sont autorisés.');
            return;
        }
        
        // Vérification de la taille
        if (file.size > <?= MAX_FILE_SIZE ?>) {
            alert('Le fichier est trop volumineux (max <?= formatFileSize(MAX_FILE_SIZE) ?>).');
            return;
        }
        
        // Affiche la preview
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = formatFileSize(file.size);
        uploadContent.style.display = 'none';
        uploadPreview.style.display = 'flex';
        
        // Pré-remplit le titre si vide
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

function formatFileSize(bytes) {
    if (bytes === 0) return '0 o';
    const k = 1024;
    const sizes = ['o', 'Ko', 'Mo', 'Go'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
