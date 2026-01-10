<?php
$pageTitle = 'Utilisateurs';
ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Utilisateurs</h1>
        <p><?= is_array($users) ? count($users) : 0 ?> utilisateur(s) enregistré(s)</p>
    </div>
    <a href="/users/create" class="btn btn-primary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="8.5" cy="7" r="4"/>
            <line x1="20" y1="8" x2="20" y2="14"/>
            <line x1="23" y1="11" x2="17" y2="11"/>
        </svg>
        Nouvel utilisateur
    </a>
</div>

<!-- Filtres -->
<div class="filters-bar">
    <form action="/users" method="GET" class="filters-form">
        <div class="filter-group">
            <input type="text" name="search" placeholder="Rechercher..." value="<?= sanitize($filters['search'] ?? '') ?>">
        </div>
        
        <div class="filter-group">
            <select name="role">
                <option value="">Tous les rôles</option>
                <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrateurs</option>
                <option value="member" <?= ($filters['role'] ?? '') === 'member' ? 'selected' : '' ?>>Membres</option>
            </select>
        </div>
        
        <div class="filter-group">
            <select name="team_id">
                <option value="">Toutes les équipes</option>
                <?php foreach ($teams as $team): ?>
                <option value="<?= $team['id'] ?>" <?= ($filters['team_id'] ?? '') == $team['id'] ? 'selected' : '' ?>><?= sanitize($team['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            Filtrer
        </button>
        
        <?php if (!empty(array_filter($filters ?? []))): ?>
        <a href="/users" class="btn btn-ghost">Réinitialiser</a>
        <?php endif; ?>
    </form>
</div>

<!-- Liste des utilisateurs -->
<?php if (empty($users)): ?>
<div class="empty-state large">
    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
    <h3>Aucun utilisateur trouvé</h3>
    <p>Ajoutez votre premier utilisateur</p>
    <a href="/users/create" class="btn btn-primary">Ajouter un utilisateur</a>
</div>
<?php else: ?>

<div class="card">
    <div class="users-table">
        <table>
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Équipe</th>
                    <th>Rôle</th>
                    <th>Dernière connexion</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="avatar-sm">
                                <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                            </div>
                            <div class="user-cell-info">
                                <span class="user-cell-name"><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></span>
                                <span class="user-cell-username">@<?= sanitize($user['username']) ?></span>
                            </div>
                        </div>
                    </td>
                    <td><?= sanitize($user['email']) ?></td>
                    <td>
                        <?php if ($user['team_name']): ?>
                        <span class="team-badge" style="--team-color: <?= $user['team_color'] ?>">
                            <?= sanitize($user['team_name']) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($user['role'] === 'admin'): ?>
                        <span class="badge badge-admin">Administrateur</span>
                        <?php else: ?>
                        <span class="badge">Membre</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($user['last_login_at'])): ?>
                        <?= formatDate($user['last_login_at']) ?>
                        <?php else: ?>
                        <span class="text-muted">Jamais</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="/users/<?= $user['id'] ?>/edit" class="btn btn-ghost btn-xs" title="Modifier">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </a>
                            <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                            <form action="/users/<?= $user['id'] ?>/delete" method="POST" class="inline-form" data-confirm="Supprimer cet utilisateur ?">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-ghost btn-xs btn-danger-hover" title="Supprimer">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>

<style>
.users-table {
    overflow-x: auto;
}
.users-table table {
    width: 100%;
    border-collapse: collapse;
}
.users-table th,
.users-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}
.users-table th {
    font-weight: 600;
    font-size: 0.8125rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
    background: var(--bg-secondary);
}
.users-table tbody tr:hover {
    background: var(--bg-secondary);
}
.user-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.user-cell-info {
    display: flex;
    flex-direction: column;
}
.user-cell-name {
    font-weight: 500;
}
.user-cell-username {
    font-size: 0.8125rem;
    color: var(--text-muted);
}
.badge-admin {
    background: rgba(139, 92, 246, 0.1);
    color: var(--purple);
}
.table-actions {
    display: flex;
    gap: 0.25rem;
}
.inline-form {
    display: inline;
}
.btn-danger-hover:hover {
    color: var(--danger) !important;
}
.text-muted {
    color: var(--text-muted);
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
