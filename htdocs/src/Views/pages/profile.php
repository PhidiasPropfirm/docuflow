<?php
/**
 * DocuFlow - Page Mon Profil
 * Fichier: htdocs/src/Views/pages/profile.php
 * 
 * CORRECTIONS:
 * - Gestion des variables manquantes ($stats, team_name, last_login_at)
 * - Ajout des traductions FR/EN
 * - Protection contre les erreurs "undefined"
 */

$pageTitle = __('profile_title');
ob_start();

// Valeurs par défaut pour éviter les erreurs
$stats = $stats ?? ['documents' => 0, 'annotations' => 0, 'links' => 0];
$team = $team ?? null;
?>

<style>
.page-header {
    margin-bottom: 24px;
}

.page-header h1 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary, #111827);
    margin: 0;
}

.page-header p {
    color: var(--text-secondary, #6B7280);
    font-size: 0.875rem;
    margin: 4px 0 0;
}

.profile-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 24px;
}

/* Sidebar */
.profile-sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.profile-card {
    background: var(--bg-primary, white);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 12px;
    padding: 24px;
    text-align: center;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #3B82F6, #1D4ED8);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    font-weight: 600;
    margin: 0 auto 16px;
}

.profile-card h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary, #111827);
    margin: 0 0 4px;
}

.profile-card .username {
    color: var(--text-secondary, #6B7280);
    font-size: 0.875rem;
    margin: 0 0 16px;
}

.profile-badges {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
    margin-bottom: 20px;
}

.badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    background: var(--bg-secondary, #f3f4f6);
    color: var(--text-secondary, #6B7280);
}

.badge-admin {
    background: rgba(239, 68, 68, 0.1);
    color: #DC2626;
}

.team-badge {
    background: color-mix(in srgb, var(--team-color, #3B82F6) 15%, transparent);
    color: var(--team-color, #3B82F6);
}

.profile-stats {
    display: flex;
    justify-content: center;
    gap: 24px;
    padding: 16px 0;
    border-top: 1px solid var(--border-color, #e5e7eb);
    border-bottom: 1px solid var(--border-color, #e5e7eb);
    margin-bottom: 16px;
}

.profile-stat {
    text-align: center;
}

.profile-stat .stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary, #111827);
}

.profile-stat .stat-label {
    font-size: 0.75rem;
    color: var(--text-secondary, #6B7280);
    text-transform: uppercase;
}

.profile-meta {
    text-align: left;
}

.profile-meta p {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: var(--text-secondary, #6B7280);
    margin: 8px 0;
}

.profile-meta svg {
    flex-shrink: 0;
    opacity: 0.6;
}

/* Main content */
.profile-main {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-section {
    background: var(--bg-primary, white);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 12px;
    padding: 24px;
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

.form-group input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 8px;
    font-size: 0.9rem;
    background: var(--bg-primary, white);
    color: var(--text-primary, #111827);
    transition: border-color 0.15s, box-shadow 0.15s;
}

.form-group input:focus {
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

.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 16px;
}

/* Password section */
.password-section {
    background: var(--bg-secondary, #f9fafb);
    border-radius: 8px;
    padding: 16px;
    margin-top: 16px;
}

.password-section h3 {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary, #111827);
    margin: 0 0 16px;
}

/* Notifications */
.notification-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color, #e5e7eb);
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-info h4 {
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--text-primary, #111827);
    margin: 0 0 2px;
}

.notification-info p {
    font-size: 0.8rem;
    color: var(--text-secondary, #6B7280);
    margin: 0;
}

/* Toggle switch */
.toggle {
    position: relative;
    width: 44px;
    height: 24px;
}

.toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--bg-tertiary, #d1d5db);
    transition: 0.3s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background: white;
    transition: 0.3s;
    border-radius: 50%;
}

.toggle input:checked + .toggle-slider {
    background: var(--primary, #3B82F6);
}

.toggle input:checked + .toggle-slider:before {
    transform: translateX(20px);
}

/* Buttons */
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

@media (max-width: 900px) {
    .profile-layout {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= __('profile_title') ?></h1>
        <p><?= __('profile_subtitle') ?></p>
    </div>
</div>

<div class="profile-layout">
    <!-- Sidebar profil -->
    <div class="profile-sidebar">
        <div class="profile-card">
            <div class="profile-avatar">
                <?= strtoupper(substr($user['first_name'] ?? '', 0, 1) . substr($user['last_name'] ?? '', 0, 1)) ?>
            </div>
            <h2><?= sanitize(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></h2>
            <p class="username">@<?= sanitize($user['username'] ?? '') ?></p>
            
            <div class="profile-badges">
                <?php if (($user['role'] ?? '') === 'admin'): ?>
                <span class="badge badge-admin"><?= __('role_admin') ?></span>
                <?php else: ?>
                <span class="badge"><?= __('role_member') ?></span>
                <?php endif; ?>
                
                <?php if ($team): ?>
                <span class="team-badge" style="--team-color: <?= $team['color'] ?? '#3B82F6' ?>">
                    <?= sanitize($team['name'] ?? '') ?>
                </span>
                <?php endif; ?>
            </div>
            
            <div class="profile-stats">
                <div class="profile-stat">
                    <span class="stat-number"><?= $stats['documents'] ?? 0 ?></span>
                    <span class="stat-label"><?= __('documents') ?></span>
                </div>
                <div class="profile-stat">
                    <span class="stat-number"><?= $stats['annotations'] ?? 0 ?></span>
                    <span class="stat-label"><?= __('annotations') ?></span>
                </div>
            </div>
            
            <div class="profile-meta">
                <p>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <?= __('member_since') ?> <?= formatDate($user['created_at'] ?? '', 'd/m/Y') ?>
                </p>
                <?php 
                $lastLogin = $user['last_login_at'] ?? $user['last_login'] ?? null;
                if ($lastLogin): 
                ?>
                <p>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <?= __('last_login') ?> : <?= formatDate($lastLogin) ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Contenu principal -->
    <div class="profile-main">
        <!-- Informations personnelles -->
        <form action="/users/<?= $user['id'] ?? '' ?>" method="POST" class="profile-form">
            <?= csrf_field() ?>
            
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
                        <input type="text" id="username" name="username" readonly
                               value="<?= sanitize($user['username'] ?? '') ?>">
                        <span class="form-hint"><?= __('username_readonly') ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="email"><?= __('email') ?> *</label>
                        <input type="email" id="email" name="email" required 
                               value="<?= sanitize($user['email'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        <?= __('save') ?>
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Changer le mot de passe -->
        <form action="/users/<?= $user['id'] ?? '' ?>/password" method="POST">
            <?= csrf_field() ?>
            
            <div class="form-section">
                <h2><?= __('profile_password') ?></h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="current_password"><?= __('current_password') ?> *</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div></div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="new_password"><?= __('new_password') ?> *</label>
                        <input type="password" id="new_password" name="new_password" required minlength="8">
                        <span class="form-hint"><?= __('password_min_chars') ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password"><?= __('confirm_password') ?> *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-secondary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <?= __('profile_password') ?>
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Notifications -->
        <div class="form-section">
            <h2><?= __('notifications') ?></h2>
            
            <div class="notification-item">
                <div class="notification-info">
                    <h4><?= __('notif_new_documents') ?></h4>
                    <p><?= __('notif_new_documents_desc') ?></p>
                </div>
                <label class="toggle">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            
            <div class="notification-item">
                <div class="notification-info">
                    <h4><?= __('annotations') ?></h4>
                    <p><?= __('notif_annotations_desc') ?></p>
                </div>
                <label class="toggle">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            
            <div class="notification-item">
                <div class="notification-info">
                    <h4><?= __('links') ?></h4>
                    <p><?= __('notif_links_desc') ?></p>
                </div>
                <label class="toggle">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>
</div>

<script>
// Validation du mot de passe
document.querySelector('form[action*="/password"]').addEventListener('submit', function(e) {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('confirm_password').value;
    
    if (newPass !== confirmPass) {
        e.preventDefault();
        alert('<?= __('passwords_not_match') ?>');
        document.getElementById('confirm_password').focus();
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
