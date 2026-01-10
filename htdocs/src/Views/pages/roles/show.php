<?php
$pageTitle = $role['display_name'];
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
        <div class="page-title-with-badge">
            <span class="role-badge-lg" style="background: <?= $role['color'] ?>"></span>
            <h1><?= sanitize($role['display_name']) ?></h1>
            <?php if ($role['is_system']): ?>
            <span class="badge badge-system">Système</span>
            <?php endif; ?>
        </div>
        <p class="role-identifier">@<?= sanitize($role['name']) ?></p>
    </div>
    <div class="page-header-actions">
        <a href="/roles/<?= $role['id'] ?>/edit" class="btn btn-primary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Modifier
        </a>
    </div>
</div>

<div class="role-details-grid">
    <!-- Informations -->
    <div class="card">
        <div class="card-header">
            <h2>Informations</h2>
        </div>
        <div class="card-body">
            <?php if ($role['description']): ?>
            <p class="role-description"><?= sanitize($role['description']) ?></p>
            <?php else: ?>
            <p class="text-muted">Aucune description</p>
            <?php endif; ?>
            
            <div class="info-list" style="margin-top: 1.5rem;">
                <dt>Créé le</dt>
                <dd><?= formatDate($role['created_at']) ?></dd>
                
                <dt>Modifié le</dt>
                <dd><?= formatDate($role['updated_at']) ?></dd>
            </div>
        </div>
    </div>
    
    <!-- Utilisateurs avec ce rôle -->
    <div class="card">
        <div class="card-header">
            <h2>Utilisateurs</h2>
            <span class="badge"><?= count($users) ?></span>
        </div>
        <div class="card-body">
            <?php if (!empty($users)): ?>
            <div class="users-list">
                <?php foreach ($users as $user): ?>
                <a href="/users/<?= $user['id'] ?>" class="user-list-item">
                    <div class="avatar-sm">
                        <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                    </div>
                    <div class="user-list-info">
                        <span class="user-list-name"><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></span>
                        <span class="user-list-email"><?= sanitize($user['email']) ?></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state small">
                <p>Aucun utilisateur avec ce rôle</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Permissions -->
    <div class="card permissions-card">
        <div class="card-header">
            <h2>Permissions</h2>
            <span class="badge"><?= count($role['permissions']) ?></span>
        </div>
        <div class="card-body">
            <?php if (!empty($role['permissions'])): ?>
            <?php
            // Grouper par catégorie
            $grouped = [];
            foreach ($role['permissions'] as $perm) {
                $cat = $perm['category'];
                if (!isset($grouped[$cat])) $grouped[$cat] = [];
                $grouped[$cat][] = $perm;
            }
            ?>
            
            <?php foreach ($grouped as $category => $perms): ?>
            <div class="permission-group">
                <h4 class="permission-group-title"><?= $categoryLabels[$category] ?? ucfirst($category) ?></h4>
                <div class="permission-tags">
                    <?php foreach ($perms as $perm): ?>
                    <span class="permission-tag" title="<?= sanitize($perm['description']) ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?= sanitize($perm['display_name']) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php else: ?>
            <div class="empty-state small">
                <p>Aucune permission attribuée</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.page-title-with-badge {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.role-badge-lg {
    width: 12px;
    height: 32px;
    border-radius: 4px;
}

.role-identifier {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-top: 0.25rem;
}

.badge-system {
    background: var(--bg-tertiary);
    color: var(--text-secondary);
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

.role-details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.role-details-grid .permissions-card {
    grid-column: span 2;
}

.role-description {
    color: var(--text-secondary);
    line-height: 1.6;
}

.users-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.user-list-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: var(--border-radius);
    text-decoration: none;
    color: inherit;
    transition: background 0.15s;
}

.user-list-item:hover {
    background: var(--bg-secondary);
}

.avatar-sm {
    width: 36px;
    height: 36px;
    background: var(--primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
}

.user-list-info {
    display: flex;
    flex-direction: column;
}

.user-list-name {
    font-weight: 500;
    font-size: 0.9rem;
}

.user-list-email {
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.permission-group {
    margin-bottom: 1.5rem;
}

.permission-group:last-child {
    margin-bottom: 0;
}

.permission-group-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border-color);
}

.permission-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.permission-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.4rem 0.75rem;
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    border-radius: 20px;
    font-size: 0.8rem;
    cursor: help;
}

.permission-tag svg {
    color: #10B981;
}

@media (max-width: 768px) {
    .role-details-grid {
        grid-template-columns: 1fr;
    }
    
    .role-details-grid .permissions-card {
        grid-column: span 1;
    }
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
