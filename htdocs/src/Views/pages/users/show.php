<?php
$pageTitle = $user['first_name'] . ' ' . $user['last_name'];
ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/users" class="back-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
            Retour aux utilisateurs
        </a>
        <h1><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></h1>
        <p>@<?= sanitize($user['username']) ?></p>
    </div>
    <div class="page-header-actions">
        <a href="/users/<?= $user['id'] ?>/edit" class="btn btn-secondary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Modifier
        </a>
    </div>
</div>

<div class="user-profile-grid">
    <!-- Informations principales -->
    <div class="card">
        <div class="card-header">
            <h2>Informations</h2>
        </div>
        <div class="card-body">
            <div class="user-profile-header">
                <div class="avatar-lg">
                    <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                </div>
                <div class="user-profile-info">
                    <h3><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></h3>
                    <p class="text-muted">@<?= sanitize($user['username']) ?></p>
                    <?php if ($user['role'] === 'admin'): ?>
                    <span class="badge badge-admin">Administrateur</span>
                    <?php else: ?>
                    <span class="badge">Membre</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="info-list" style="margin-top: 1.5rem;">
                <dt>Email</dt>
                <dd><a href="mailto:<?= sanitize($user['email']) ?>"><?= sanitize($user['email']) ?></a></dd>
                
                <dt>Équipe</dt>
                <dd>
                    <?php if ($team): ?>
                    <span class="team-badge" style="--team-color: <?= $team['color'] ?? '#6B7280' ?>">
                        <?= sanitize($team['name']) ?>
                    </span>
                    <?php else: ?>
                    <span class="text-muted">Aucune équipe</span>
                    <?php endif; ?>
                </dd>
                
                <dt>Inscrit le</dt>
                <dd><?= formatDate($user['created_at']) ?></dd>
                
                <dt>Dernière connexion</dt>
                <dd>
                    <?php if (!empty($user['last_login_at'])): ?>
                    <?= formatDate($user['last_login_at']) ?>
                    <?php else: ?>
                    <span class="text-muted">Jamais</span>
                    <?php endif; ?>
                </dd>
            </div>
        </div>
    </div>
    
    <!-- Activité récente -->
    <div class="card">
        <div class="card-header">
            <h2>Activité récente</h2>
        </div>
        <div class="card-body">
            <?php if (!empty($activity)): ?>
            <div class="activity-list">
                <?php foreach ($activity as $act): ?>
                <div class="activity-item">
                    <div class="activity-content">
                        <span class="activity-text"><?= sanitize($act['description']) ?></span>
                        <span class="activity-time"><?= formatDate($act['created_at']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state small">
                <p>Aucune activité récente</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
</div>

<style>
.user-profile-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
}

.user-profile-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.avatar-lg {
    width: 80px;
    height: 80px;
    background: var(--primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 600;
}

.user-profile-info h3 {
    font-size: 1.25rem;
    margin-bottom: 0.25rem;
}

.badge-admin {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

@media (max-width: 768px) {
    .user-profile-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
