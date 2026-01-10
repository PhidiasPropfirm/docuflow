<?php
$pageTitle = 'Gestion des rôles';
ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Gestion des rôles</h1>
        <p><?= count($roles) ?> rôle(s) configuré(s)</p>
    </div>
    <a href="/roles/create" class="btn btn-primary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="16"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Nouveau rôle
    </a>
</div>

<div class="roles-grid">
    <?php foreach ($roles as $role): ?>
    <div class="role-card">
        <div class="role-header" style="--role-color: <?= $role['color'] ?>">
            <div class="role-color-badge" style="background: <?= $role['color'] ?>"></div>
            <div class="role-info">
                <h3><?= sanitize($role['display_name']) ?></h3>
                <span class="role-name">@<?= sanitize($role['name']) ?></span>
            </div>
            <?php if ($role['is_system']): ?>
            <span class="badge badge-system" title="Rôle système">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </span>
            <?php endif; ?>
        </div>
        
        <div class="role-body">
            <?php if ($role['description']): ?>
            <p class="role-description"><?= sanitize($role['description']) ?></p>
            <?php endif; ?>
            
            <div class="role-stats">
                <div class="role-stat">
                    <span class="role-stat-value"><?= $role['user_count'] ?></span>
                    <span class="role-stat-label">Utilisateur(s)</span>
                </div>
            </div>
        </div>
        
        <div class="role-actions">
            <a href="/roles/<?= $role['id'] ?>" class="btn btn-ghost btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                Voir
            </a>
            <a href="/roles/<?= $role['id'] ?>/edit" class="btn btn-ghost btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Modifier
            </a>
            <?php if (!$role['is_system']): ?>
            <form action="/roles/<?= $role['id'] ?>/delete" method="POST" class="inline" 
                  onsubmit="return confirm('Supprimer ce rôle ? Les utilisateurs seront basculés vers Membre.')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-ghost btn-sm text-danger">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<style>
.roles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.role-card {
    background: var(--bg-primary);
    border-radius: var(--border-radius-lg);
    border: 1px solid var(--border-color);
    overflow: hidden;
    transition: box-shadow 0.2s;
}

.role-card:hover {
    box-shadow: var(--shadow-md);
}

.role-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border-bottom: 1px solid var(--border-color);
    background: linear-gradient(135deg, color-mix(in srgb, var(--role-color) 5%, transparent), transparent);
}

.role-color-badge {
    width: 8px;
    height: 40px;
    border-radius: 4px;
    flex-shrink: 0;
}

.role-info {
    flex: 1;
    min-width: 0;
}

.role-info h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.role-name {
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.badge-system {
    background: var(--bg-tertiary);
    padding: 0.35rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.badge-system svg {
    color: var(--text-secondary);
}

.role-body {
    padding: 1.25rem;
}

.role-description {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 1rem;
    line-height: 1.5;
}

.role-stats {
    display: flex;
    gap: 1.5rem;
}

.role-stat {
    display: flex;
    flex-direction: column;
}

.role-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
}

.role-stat-label {
    font-size: 0.75rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.role-actions {
    display: flex;
    gap: 0.5rem;
    padding: 1rem 1.25rem;
    border-top: 1px solid var(--border-color);
    background: var(--bg-secondary);
}

.role-actions .btn-sm {
    padding: 0.4rem 0.75rem;
    font-size: 0.8rem;
}

.inline {
    display: inline;
}

.text-danger {
    color: var(--danger) !important;
}

.text-danger:hover {
    background: rgba(239, 68, 68, 0.1) !important;
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
