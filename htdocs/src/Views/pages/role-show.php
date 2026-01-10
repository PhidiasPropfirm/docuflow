<?php
$pageTitle = $role['display_name'] ?? __('role');
ob_start();

// Déterminer le nom à afficher selon la langue
$displayName = $role['display_name'] ?? $role['name'];
if (currentLang() === 'en' && !empty($role['display_name_en'])) {
    $displayName = $role['display_name_en'];
}

$description = $role['description'] ?? '';
if (currentLang() === 'en' && !empty($role['description_en'])) {
    $description = $role['description_en'];
}
?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    gap: 16px;
}

.page-header-content h1 {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0 0 4px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-header-content p {
    color: var(--text-secondary, #6B7280);
    font-size: 0.875rem;
    margin: 0;
}

.role-badge {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: all 0.15s;
}

.btn-secondary {
    background: var(--bg-secondary, #f3f4f6);
    color: var(--text-primary, #111827);
}

.btn-secondary:hover {
    background: var(--bg-tertiary, #e5e7eb);
}

.content-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 24px;
}

@media (max-width: 968px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
}

.card {
    background: var(--bg-primary, white);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 12px;
    padding: 24px;
}

.card-title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 16px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid var(--border-color, #e5e7eb);
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    color: var(--text-secondary, #6B7280);
    font-size: 0.875rem;
}

.info-value {
    font-weight: 500;
}

.permission-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.permission-tag {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    background: var(--bg-secondary, #f3f4f6);
    border-radius: 6px;
    font-size: 0.8rem;
    color: var(--text-secondary, #6B7280);
}

.users-table {
    width: 100%;
    border-collapse: collapse;
}

.users-table th,
.users-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid var(--border-color, #e5e7eb);
}

.users-table th {
    font-weight: 600;
    font-size: 0.8rem;
    color: var(--text-secondary, #6B7280);
    text-transform: uppercase;
}

.users-table tr:hover {
    background: var(--bg-secondary, #f9fafb);
}

.user-avatar {
    width: 32px;
    height: 32px;
    background: var(--primary, #3B82F6);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-name {
    font-weight: 500;
}

.user-email {
    font-size: 0.8rem;
    color: var(--text-secondary, #6B7280);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.active {
    background: #D1FAE5;
    color: #059669;
}

.status-badge.inactive {
    background: #FEE2E2;
    color: #DC2626;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-secondary, #6B7280);
}

.system-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    background: #FEF3C7;
    color: #D97706;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}
</style>

<div class="page-header">
    <div class="page-header-content">
        <h1>
            <span class="role-badge" style="background-color: <?= $role['color'] ?? '#3B82F6' ?>"></span>
            <?= sanitize($displayName) ?>
            <?php if (!empty($role['is_system'])): ?>
            <span class="system-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <?= __('system') ?>
            </span>
            <?php endif; ?>
        </h1>
        <p><?= sanitize($description) ?: __('no_description') ?></p>
    </div>
    
    <a href="/roles" class="btn btn-secondary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
        </svg>
        <?= __('back') ?>
    </a>
</div>

<div class="content-grid">
    <!-- Informations du rôle -->
    <div>
        <div class="card">
            <h3 class="card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="16" x2="12" y2="12"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <?= __('information') ?>
            </h3>
            
            <div class="info-list">
                <div class="info-item">
                    <span class="info-label"><?= __('name') ?></span>
                    <span class="info-value">@<?= sanitize($role['name']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label"><?= __('color') ?></span>
                    <span class="info-value">
                        <span class="role-badge" style="background-color: <?= $role['color'] ?? '#3B82F6' ?>; width: 16px; height: 16px;"></span>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label"><?= __('users') ?></span>
                    <span class="info-value"><?= count($users) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label"><?= __('created_at') ?></span>
                    <span class="info-value"><?= !empty($role['created_at']) ? formatDate($role['created_at'], 'd/m/Y') : '-' ?></span>
                </div>
            </div>
        </div>
        
        <?php if (!empty($role['permissions'])): ?>
        <div class="card" style="margin-top: 16px;">
            <h3 class="card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                <?= __('permissions') ?>
            </h3>
            
            <div class="permission-list">
                <?php foreach ($role['permissions'] as $perm): ?>
                <span class="permission-tag"><?= sanitize($perm) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Utilisateurs avec ce rôle -->
    <div class="card">
        <h3 class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <?= __('users') ?> (<?= count($users) ?>)
        </h3>
        
        <?php if (!empty($users)): ?>
        <table class="users-table">
            <thead>
                <tr>
                    <th><?= __('name') ?></th>
                    <th><?= __('email') ?></th>
                    <th><?= __('status') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <div class="user-info">
                            <div class="user-avatar">
                                <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                            </div>
                            <span class="user-name"><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></span>
                        </div>
                    </td>
                    <td class="user-email"><?= sanitize($user['email']) ?></td>
                    <td>
                        <span class="status-badge <?= $user['is_active'] ? 'active' : 'inactive' ?>">
                            <?= $user['is_active'] ? __('active') : __('inactive') ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
            </svg>
            <p><?= __('no_users') ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
