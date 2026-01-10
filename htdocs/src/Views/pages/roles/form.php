<?php
$isEdit = isset($role);
$pageTitle = $isEdit ? 'Modifier : ' . $role['display_name'] : 'Nouveau rôle';
ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/roles" class="back-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
            Retour aux rôles
        </a>
        <h1><?= $isEdit ? 'Modifier le rôle' : 'Nouveau rôle' ?></h1>
    </div>
</div>

<form action="<?= $isEdit ? '/roles/' . $role['id'] : '/roles' ?>" method="POST" class="role-form">
    <?= csrf_field() ?>
    
    <div class="form-grid">
        <!-- Colonne de gauche : Informations du rôle -->
        <div class="form-section">
            <div class="card">
                <div class="card-header">
                    <h2>Informations du rôle</h2>
                </div>
                <div class="card-body">
                    <?php if (!$isEdit): ?>
                    <div class="form-group">
                        <label for="name">Identifiant technique *</label>
                        <input type="text" id="name" name="name" required
                               pattern="[a-z0-9_]+"
                               placeholder="ex: comptable, manager_ventes"
                               value="<?= sanitize($role['name'] ?? '') ?>"
                               <?= $isEdit ? 'readonly' : '' ?>>
                        <span class="form-hint">Lettres minuscules, chiffres et underscores uniquement. Non modifiable.</span>
                    </div>
                    <?php else: ?>
                    <div class="form-group">
                        <label>Identifiant technique</label>
                        <input type="text" value="<?= sanitize($role['name']) ?>" readonly class="input-readonly">
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="display_name">Nom d'affichage *</label>
                        <input type="text" id="display_name" name="display_name" required
                               placeholder="ex: Comptable, Manager des ventes"
                               value="<?= sanitize($role['display_name'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  placeholder="Décrivez le rôle et ses responsabilités..."><?= sanitize($role['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="color">Couleur</label>
                        <div class="color-picker-group">
                            <input type="color" id="color" name="color" 
                                   value="<?= $role['color'] ?? '#3B82F6' ?>">
                            <input type="text" id="color_hex" 
                                   value="<?= $role['color'] ?? '#3B82F6' ?>"
                                   pattern="^#[0-9A-Fa-f]{6}$"
                                   placeholder="#000000">
                        </div>
                    </div>
                    
                    <?php if ($isEdit && $role['is_system']): ?>
                    <div class="alert alert-info">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                        <span>Ceci est un rôle système. Il ne peut pas être supprimé.</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Colonne de droite : Permissions -->
        <div class="form-section permissions-section">
            <div class="card">
                <div class="card-header">
                    <h2>Permissions</h2>
                    <div class="permissions-actions">
                        <button type="button" onclick="selectAllPermissions()" class="btn btn-ghost btn-sm">Tout cocher</button>
                        <button type="button" onclick="deselectAllPermissions()" class="btn btn-ghost btn-sm">Tout décocher</button>
                    </div>
                </div>
                <div class="card-body">
                    <?php foreach ($permissions as $category => $perms): ?>
                    <div class="permission-category">
                        <div class="permission-category-header" onclick="toggleCategory('<?= $category ?>')">
                            <svg class="chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                            <h4><?= $categoryLabels[$category] ?? ucfirst($category) ?></h4>
                            <span class="permission-count" id="count-<?= $category ?>">0/<?= count($perms) ?></span>
                        </div>
                        <div class="permission-list" id="category-<?= $category ?>">
                            <?php foreach ($perms as $perm): ?>
                            <label class="permission-item">
                                <input type="checkbox" name="permissions[]" value="<?= $perm['id'] ?>"
                                       data-category="<?= $category ?>"
                                       <?= (isset($rolePermissionIds) && in_array($perm['id'], $rolePermissionIds)) ? 'checked' : '' ?>>
                                <div class="permission-info">
                                    <span class="permission-name"><?= sanitize($perm['display_name']) ?></span>
                                    <span class="permission-desc"><?= sanitize($perm['description']) ?></span>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="form-actions">
        <a href="/roles" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn btn-primary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg>
            <?= $isEdit ? 'Enregistrer les modifications' : 'Créer le rôle' ?>
        </button>
    </div>
</form>

<style>
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.color-picker-group {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.color-picker-group input[type="color"] {
    width: 50px;
    height: 40px;
    padding: 0;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    cursor: pointer;
}

.color-picker-group input[type="text"] {
    width: 100px;
    font-family: monospace;
}

.permissions-section .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.permissions-actions {
    display: flex;
    gap: 0.5rem;
}

.permission-category {
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    margin-bottom: 1rem;
    overflow: hidden;
}

.permission-category:last-child {
    margin-bottom: 0;
}

.permission-category-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    background: var(--bg-secondary);
    cursor: pointer;
    user-select: none;
    transition: background 0.2s;
}

.permission-category-header:hover {
    background: var(--bg-tertiary);
}

.permission-category-header h4 {
    flex: 1;
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0;
}

.permission-category-header .chevron {
    transition: transform 0.2s;
}

.permission-category.collapsed .chevron {
    transform: rotate(-90deg);
}

.permission-category.collapsed .permission-list {
    display: none;
}

.permission-count {
    font-size: 0.75rem;
    color: var(--text-secondary);
    background: var(--bg-primary);
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
}

.permission-list {
    padding: 0.5rem;
}

.permission-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: background 0.15s;
}

.permission-item:hover {
    background: var(--bg-secondary);
}

.permission-item input[type="checkbox"] {
    margin-top: 0.2rem;
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.permission-info {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.permission-name {
    font-size: 0.875rem;
    font-weight: 500;
}

.permission-desc {
    font-size: 0.75rem;
    color: var(--text-secondary);
}

.alert-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: var(--border-radius);
    color: var(--primary);
    font-size: 0.875rem;
    margin-top: 1rem;
}

.input-readonly {
    background: var(--bg-secondary) !important;
    cursor: not-allowed;
}

@media (max-width: 1024px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Synchroniser le color picker avec le champ texte
const colorInput = document.getElementById('color');
const colorHex = document.getElementById('color_hex');

colorInput.addEventListener('input', () => {
    colorHex.value = colorInput.value;
});

colorHex.addEventListener('input', () => {
    if (/^#[0-9A-Fa-f]{6}$/.test(colorHex.value)) {
        colorInput.value = colorHex.value;
    }
});

// Toggle catégorie
function toggleCategory(category) {
    const el = document.querySelector(`.permission-category:has(#category-${category})`);
    el.classList.toggle('collapsed');
}

// Sélectionner/désélectionner toutes les permissions
function selectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = true);
    updateAllCounts();
}

function deselectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
    updateAllCounts();
}

// Compter les permissions par catégorie
function updateCategoryCount(category) {
    const checkboxes = document.querySelectorAll(`input[data-category="${category}"]`);
    const checked = document.querySelectorAll(`input[data-category="${category}"]:checked`).length;
    const countEl = document.getElementById(`count-${category}`);
    if (countEl) {
        countEl.textContent = `${checked}/${checkboxes.length}`;
    }
}

function updateAllCounts() {
    const categories = [...new Set([...document.querySelectorAll('input[data-category]')].map(cb => cb.dataset.category))];
    categories.forEach(updateCategoryCount);
}

// Mettre à jour au changement
document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
    cb.addEventListener('change', () => updateCategoryCount(cb.dataset.category));
});

// Initialiser les compteurs
updateAllCounts();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
