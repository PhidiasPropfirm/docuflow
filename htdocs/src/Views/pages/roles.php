<?php
$pageTitle = __('roles_title');
ob_start();

// Fonction helper pour obtenir le nom traduit
function getRoleDisplayName($role) {
    $lang = currentLang();
    if ($lang === 'en' && !empty($role['display_name_en'])) {
        return $role['display_name_en'];
    }
    return $role['display_name'];
}

function getRoleDescription($role) {
    $lang = currentLang();
    if ($lang === 'en' && !empty($role['description_en'])) {
        return $role['description_en'];
    }
    return $role['description'] ?? '';
}
?>

<style>
/* Roles Page Styles */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.page-header-content h1 {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0 0 4px 0;
}

.page-header-content p {
    color: var(--text-secondary, #6B7280);
    font-size: 0.875rem;
    margin: 0;
}

.roles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.role-card {
    background: var(--bg-primary, white);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 12px;
    padding: 20px;
    transition: box-shadow 0.2s, transform 0.2s;
}

.role-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.role-card-header {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 12px;
}

.role-color-indicator {
    width: 4px;
    height: 40px;
    border-radius: 2px;
    flex-shrink: 0;
}

.role-card-title {
    flex: 1;
    min-width: 0;
}

.role-card-title h3 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 2px 0;
    color: var(--text-primary, #111827);
}

.role-slug {
    font-size: 0.75rem;
    color: var(--text-tertiary, #9CA3AF);
    font-family: monospace;
}

.role-system-badge {
    color: var(--text-tertiary, #9CA3AF);
}

.role-description {
    font-size: 0.85rem;
    color: var(--text-secondary, #6B7280);
    margin: 0 0 16px 0;
    line-height: 1.4;
    min-height: 40px;
}

.role-card-stats {
    display: flex;
    align-items: baseline;
    gap: 8px;
    padding: 12px 0;
    border-top: 1px solid var(--border-color, #e5e7eb);
    border-bottom: 1px solid var(--border-color, #e5e7eb);
    margin-bottom: 16px;
}

.role-card-stats .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary, #111827);
}

.role-card-stats .stat-label {
    font-size: 0.7rem;
    color: var(--text-secondary, #6B7280);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.role-card-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: all 0.15s;
    text-decoration: none;
}

.btn-primary {
    background: var(--primary, #3B82F6);
    color: white;
}

.btn-primary:hover {
    background: #2563EB;
}

.btn-secondary {
    background: var(--bg-secondary, #f3f4f6);
    color: var(--text-primary, #111827);
}

.btn-secondary:hover {
    background: var(--bg-tertiary, #e5e7eb);
}

.btn-ghost {
    background: transparent;
    color: var(--text-secondary, #6B7280);
    padding: 8px 12px;
}

.btn-ghost:hover {
    background: var(--bg-secondary, #f3f4f6);
    color: var(--text-primary, #111827);
}

.btn-xs {
    padding: 6px 10px;
    font-size: 0.8rem;
}

.btn-danger {
    color: #EF4444;
}

.btn-danger:hover {
    background: #FEE2E2;
    color: #DC2626;
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal.open {
    display: flex;
}

.modal-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
}

.modal-content {
    position: relative;
    background: var(--bg-primary, white);
    border-radius: 16px;
    width: 100%;
    max-width: 600px;
    max-height: 90vh;
    overflow: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-lg {
    max-width: 700px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color, #e5e7eb);
}

.modal-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
}

.modal-close {
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-secondary, #6B7280);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    background: var(--bg-secondary, #f3f4f6);
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 16px 24px;
    border-top: 1px solid var(--border-color, #e5e7eb);
}

/* Form styles */
.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.form-group label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 8px;
    color: var(--text-primary, #111827);
}

.form-group input[type="text"],
.form-group textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 8px;
    font-size: 0.9rem;
    transition: border-color 0.15s;
    box-sizing: border-box;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary, #3B82F6);
}

.form-hint {
    font-size: 0.75rem;
    color: var(--text-tertiary, #9CA3AF);
    margin-top: 4px;
    display: block;
}

.color-picker-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
}

.color-picker-wrapper input[type="color"] {
    width: 50px;
    height: 40px;
    padding: 0;
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 8px;
    cursor: pointer;
}

.color-value {
    font-family: monospace;
    font-size: 0.9rem;
    color: var(--text-secondary, #6B7280);
}

/* Permissions */
.permissions-actions {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
}

.permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
    max-height: 250px;
    overflow-y: auto;
    padding: 16px;
    background: var(--bg-secondary, #f9fafb);
    border-radius: 8px;
}

.permission-category h4 {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-secondary, #6B7280);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 0 0 8px 0;
    padding-bottom: 4px;
    border-bottom: 1px solid var(--border-color, #e5e7eb);
}

.permission-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    padding: 4px 0;
    cursor: pointer;
}

.permission-item input[type="checkbox"] {
    width: 16px;
    height: 16px;
}

@media (max-width: 768px) {
    .roles-grid {
        grid-template-columns: 1fr;
    }
    
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= __('roles_title') ?></h1>
        <p><?= __('roles_count', ['count' => count($roles)]) ?></p>
    </div>
    <button onclick="openRoleModal()" class="btn btn-primary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
        </svg>
        <?= __('new_role') ?>
    </button>
</div>

<!-- Liste des rôles -->
<div class="roles-grid">
    <?php foreach ($roles as $role): ?>
    <div class="role-card">
        <div class="role-card-header">
            <div class="role-color-indicator" style="background-color: <?= $role['color'] ?? '#6B7280' ?>"></div>
            <div class="role-card-title">
                <h3><?= sanitize(getRoleDisplayName($role)) ?></h3>
                <span class="role-slug">@<?= sanitize($role['name']) ?></span>
            </div>
            <?php if ($role['is_system'] ?? false): ?>
            <div class="role-system-badge" title="<?= __('role_system') ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <?php endif; ?>
        </div>
        
        <p class="role-description"><?= sanitize(getRoleDescription($role)) ?></p>
        
        <div class="role-card-stats">
            <span class="stat-value"><?= $role['user_count'] ?? 0 ?></span>
            <span class="stat-label"><?= __('role_users_label') ?></span>
        </div>
        
        <div class="role-card-actions">
            <a href="/roles/<?= $role['id'] ?>" class="btn btn-ghost btn-xs">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <?= __('view') ?>
            </a>
            <button onclick="editRole(<?= htmlspecialchars(json_encode($role), ENT_QUOTES) ?>)" class="btn btn-ghost btn-xs">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                <?= __('edit') ?>
            </button>
            <?php if (!($role['is_system'] ?? false)): ?>
            <button onclick="deleteRole(<?= $role['id'] ?>)" class="btn btn-ghost btn-xs btn-danger">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                </svg>
            </button>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal rôle -->
<div class="modal" id="roleModal">
    <div class="modal-overlay" onclick="closeRoleModal()"></div>
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3 id="roleModalTitle"><?= __('new_role') ?></h3>
            <button onclick="closeRoleModal()" class="modal-close">&times;</button>
        </div>
        <form action="/roles" method="POST" id="roleForm">
            <?= csrf_field() ?>
            <input type="hidden" name="role_id" id="roleId">
            
            <div class="modal-body">
                <div class="form-group">
                    <label for="roleName"><?= __('role_name') ?></label>
                    <input type="text" id="roleName" name="name" required maxlength="50" pattern="[a-z0-9_]+" placeholder="exemple_role">
                    <small class="form-hint"><?= __('role_name_hint') ?></small>
                </div>
                
                <!-- Display Name FR / EN -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="roleDisplayName"><?= __('role_display_name') ?> (FR)</label>
                        <input type="text" id="roleDisplayName" name="display_name" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="roleDisplayNameEn"><?= __('role_display_name') ?> (EN)</label>
                        <input type="text" id="roleDisplayNameEn" name="display_name_en" maxlength="100">
                    </div>
                </div>
                
                <!-- Description FR / EN -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="roleDescription"><?= __('role_description') ?> (FR)</label>
                        <textarea id="roleDescription" name="description" rows="2" maxlength="500"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="roleDescriptionEn"><?= __('role_description') ?> (EN)</label>
                        <textarea id="roleDescriptionEn" name="description_en" rows="2" maxlength="500"></textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="roleColor"><?= __('role_color') ?></label>
                    <div class="color-picker-wrapper">
                        <input type="color" id="roleColor" name="color" value="#3B82F6">
                        <span class="color-value">#3B82F6</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><?= __('permissions') ?></label>
                    <div class="permissions-actions">
                        <button type="button" onclick="selectAllPermissions()" class="btn btn-xs btn-secondary"><?= __('select_all') ?></button>
                        <button type="button" onclick="deselectAllPermissions()" class="btn btn-xs btn-secondary"><?= __('deselect_all') ?></button>
                    </div>
                    
                    <div class="permissions-grid">
                        <?php 
                        $permissionCategories = [
                            'perm_documents' => ['documents.view', 'documents.create', 'documents.edit', 'documents.delete'],
                            'perm_zones' => ['zones.view', 'zones.create', 'zones.edit', 'zones.delete'],
                            'perm_links' => ['links.view', 'links.create', 'links.delete'],
                            'perm_users' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
                            'perm_teams' => ['teams.view', 'teams.create', 'teams.edit', 'teams.delete'],
                            'perm_roles' => ['roles.view', 'roles.create', 'roles.edit', 'roles.delete'],
                            'perm_admin' => ['admin.access', 'admin.settings']
                        ];
                        foreach ($permissionCategories as $category => $perms): 
                        ?>
                        <div class="permission-category">
                            <h4><?= __($category) ?></h4>
                            <?php foreach ($perms as $perm): ?>
                            <label class="permission-item">
                                <input type="checkbox" name="permissions[]" value="<?= $perm ?>">
                                <span><?= $perm ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="closeRoleModal()" class="btn btn-secondary"><?= __('cancel') ?></button>
                <button type="submit" class="btn btn-primary"><?= __('save') ?></button>
            </div>
        </form>
    </div>
</div>

<script>
const TRANSLATIONS = {
    new_role: '<?= __('new_role') ?>',
    edit_role: '<?= __('edit_role') ?>',
    delete_role_confirm: '<?= __('delete_role_confirm') ?>'
};

function openRoleModal() {
    document.getElementById('roleModal').classList.add('open');
    document.getElementById('roleModalTitle').textContent = TRANSLATIONS.new_role;
    document.getElementById('roleForm').reset();
    document.getElementById('roleId').value = '';
    document.querySelector('.color-value').textContent = '#3B82F6';
    deselectAllPermissions();
}

function closeRoleModal() {
    document.getElementById('roleModal').classList.remove('open');
}

function editRole(role) {
    document.getElementById('roleModal').classList.add('open');
    document.getElementById('roleModalTitle').textContent = TRANSLATIONS.edit_role;
    document.getElementById('roleId').value = role.id;
    document.getElementById('roleName').value = role.name || '';
    document.getElementById('roleDisplayName').value = role.display_name || '';
    document.getElementById('roleDisplayNameEn').value = role.display_name_en || '';
    document.getElementById('roleDescription').value = role.description || '';
    document.getElementById('roleDescriptionEn').value = role.description_en || '';
    document.getElementById('roleColor').value = role.color || '#3B82F6';
    document.querySelector('.color-value').textContent = role.color || '#3B82F6';
    
    deselectAllPermissions();
    if (role.permissions) {
        role.permissions.forEach(perm => {
            const checkbox = document.querySelector(`input[value="${perm}"]`);
            if (checkbox) checkbox.checked = true;
        });
    }
}

function deleteRole(id) {
    if (confirm(TRANSLATIONS.delete_role_confirm)) {
        fetch(`/roles/${id}`, { method: 'DELETE' })
            .then(() => window.location.reload());
    }
}

function selectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = true);
}

function deselectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
}

document.getElementById('roleColor').addEventListener('input', function() {
    document.querySelector('.color-value').textContent = this.value;
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
