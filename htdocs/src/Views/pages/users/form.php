<?php
/**
 * DocuFlow - Formulaire utilisateur (création/édition)
 * Fichier: htdocs/src/Views/pages/users/form.php
 * 
 * CORRECTIONS:
 * - Ajout champ hidden is_active pour préserver la valeur
 * - Traductions complètes FR/EN
 */

$isEdit = isset($user);
$pageTitle = $isEdit 
    ? __('edit_user') . ' : ' . ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')
    : __('new_user');
ob_start();
?>

<style>
.page-header {
    margin-bottom: 24px;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--text-secondary, #6B7280);
    text-decoration: none;
    font-size: 0.875rem;
    margin-bottom: 8px;
    transition: color 0.15s;
}

.back-link:hover {
    color: var(--primary, #3B82F6);
}

.page-header h1 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary, #111827);
    margin: 0;
}

.form-container {
    max-width: 800px;
    margin: 0 auto;
}

.form-section {
    background: var(--bg-primary, white);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
}

.form-section h2 {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-primary, #111827);
    margin: 0 0 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-color, #e5e7eb);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 16px;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary, #111827);
    margin-bottom: 6px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 8px;
    font-size: 0.9rem;
    background: var(--bg-primary, white);
    color: var(--text-primary, #111827);
    transition: border-color 0.15s, box-shadow 0.15s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary, #3B82F6);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-group input[readonly] {
    background: var(--bg-secondary, #f3f4f6);
    cursor: not-allowed;
}

.form-hint {
    display: block;
    font-size: 0.75rem;
    color: var(--text-secondary, #6B7280);
    margin-top: 4px;
}

.info-box {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: rgba(59, 130, 246, 0.1);
    color: var(--primary, #3B82F6);
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.875rem;
}

.info-box svg {
    flex-shrink: 0;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding-top: 20px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 20px;
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

.btn-ghost {
    background: transparent;
    color: var(--text-secondary, #6B7280);
}

.btn-ghost:hover {
    background: var(--bg-secondary, #f3f4f6);
    color: var(--text-primary, #111827);
}

/* Checkbox pour statut actif */
.checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: var(--bg-secondary, #f9fafb);
    border-radius: 8px;
    margin-top: 16px;
}

.checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.checkbox-group label {
    margin: 0;
    cursor: pointer;
}

.checkbox-group .form-hint {
    margin: 0;
    margin-left: auto;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
    }
}
</style>

<div class="page-header">
    <div class="page-header-content">
        <a href="/users" class="back-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
            <?= __('back_to_users') ?>
        </a>
        <h1><?= $isEdit ? __('edit_user') : __('new_user') ?></h1>
    </div>
</div>

<div class="form-container">
    <form action="<?= $isEdit ? '/users/' . $user['id'] : '/users' ?>" method="POST" id="userForm">
        <?= csrf_field() ?>
        
        <!-- Champ caché pour préserver is_active en mode édition -->
        <?php if ($isEdit): ?>
        <input type="hidden" name="preserve_is_active" value="1">
        <?php endif; ?>
        
        <!-- Informations personnelles -->
        <div class="form-section">
            <h2><?= __('personal_info') ?></h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name"><?= __('first_name') ?> *</label>
                    <input type="text" id="first_name" name="first_name" required 
                           value="<?= sanitize($user['first_name'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="last_name"><?= __('last_name') ?> *</label>
                    <input type="text" id="last_name" name="last_name" required 
                           value="<?= sanitize($user['last_name'] ?? '') ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="username"><?= __('username') ?> *</label>
                    <input type="text" id="username" name="username" required 
                           pattern="[a-zA-Z0-9_]+"
                           <?= $isEdit ? 'readonly' : '' ?>
                           value="<?= sanitize($user['username'] ?? '') ?>">
                    <span class="form-hint"><?= __('username_hint') ?></span>
                </div>
                
                <div class="form-group">
                    <label for="email"><?= __('email') ?> *</label>
                    <input type="email" id="email" name="email" required 
                           value="<?= sanitize($user['email'] ?? '') ?>">
                </div>
            </div>
        </div>
        
        <!-- Sécurité -->
        <div class="form-section">
            <h2><?= __('security') ?></h2>
            
            <?php if ($isEdit): ?>
            <div class="info-box">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="16" x2="12" y2="12"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <span><?= __('password_leave_empty') ?></span>
            </div>
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="password"><?= __('password') ?> <?= $isEdit ? '' : '*' ?></label>
                    <input type="password" id="password" name="password" 
                           <?= $isEdit ? '' : 'required' ?>
                           minlength="8"
                           autocomplete="new-password">
                    <span class="form-hint"><?= __('password_min_chars') ?></span>
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation"><?= __('confirm_password') ?> <?= $isEdit ? '' : '*' ?></label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           <?= $isEdit ? '' : 'required' ?>
                           autocomplete="new-password">
                </div>
            </div>
        </div>
        
        <!-- Rôle et équipe -->
        <div class="form-section">
            <h2><?= __('role_and_team') ?></h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="role"><?= __('role') ?> *</label>
                    <select id="role" name="role" required>
                        <option value="member" <?= ($user['role'] ?? 'member') === 'member' ? 'selected' : '' ?>><?= __('role_member') ?></option>
                        <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>><?= __('role_admin') ?></option>
                    </select>
                    <span class="form-hint"><?= __('admin_access_hint') ?></span>
                </div>
                
                <div class="form-group">
                    <label for="team_id"><?= __('team') ?></label>
                    <select id="team_id" name="team_id">
                        <option value=""><?= __('no_team') ?></option>
                        <?php foreach ($teams as $team): ?>
                        <option value="<?= $team['id'] ?>" <?= ($user['team_id'] ?? '') == $team['id'] ? 'selected' : '' ?>>
                            <?= sanitize($team['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <?php if ($isEdit): ?>
            <!-- Statut actif (seulement en mode édition) -->
            <div class="checkbox-group">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       <?= ($user['is_active'] ?? 1) ? 'checked' : '' ?>>
                <label for="is_active"><?= __('user_active') ?></label>
                <span class="form-hint"><?= __('user_active_hint') ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Actions -->
        <div class="form-actions">
            <a href="/users" class="btn btn-ghost"><?= __('cancel') ?></a>
            <button type="submit" class="btn btn-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <?= $isEdit ? __('save') : __('create_user') ?>
            </button>
        </div>
    </form>
</div>

<script>
// Validation du mot de passe
document.getElementById('userForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password');
    const confirmation = document.getElementById('password_confirmation');
    
    if (password.value && password.value !== confirmation.value) {
        e.preventDefault();
        alert('<?= __('passwords_not_match') ?>');
        confirmation.focus();
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
