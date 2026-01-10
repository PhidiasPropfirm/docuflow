<?php
$pageTitle = 'Mon profil';
ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Mon profil</h1>
        <p>Gérez vos informations personnelles et vos paramètres</p>
    </div>
</div>

<div class="profile-layout">
    <!-- Sidebar profil -->
    <div class="profile-sidebar">
        <div class="profile-card">
            <div class="profile-avatar">
                <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
            </div>
            <h2><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></h2>
            <p class="username">@<?= sanitize($user['username']) ?></p>
            
            <div class="profile-badges">
                <?php if ($user['role'] === 'admin'): ?>
                <span class="badge badge-admin">Administrateur</span>
                <?php else: ?>
                <span class="badge">Membre</span>
                <?php endif; ?>
                
                <?php if ($user['team_name']): ?>
                <span class="team-badge" style="--team-color: <?= $user['team_color'] ?>">
                    <?= sanitize($user['team_name']) ?>
                </span>
                <?php endif; ?>
            </div>
            
            <div class="profile-stats">
                <div class="profile-stat">
                    <span class="stat-number"><?= $stats['documents'] ?></span>
                    <span class="stat-label">Documents</span>
                </div>
                <div class="profile-stat">
                    <span class="stat-number"><?= $stats['annotations'] ?></span>
                    <span class="stat-label">Annotations</span>
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
                    Membre depuis <?= formatDate($user['created_at'], 'd/m/Y') ?>
                </p>
                <?php if ($user['last_login_at']): ?>
                <p>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    Dernière connexion : <?= formatDate($user['last_login_at']) ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Contenu principal -->
    <div class="profile-main">
        <!-- Informations personnelles -->
        <form action="/profile/update" method="POST" class="profile-form">
            <?= csrf_field() ?>
            
            <div class="form-section">
                <h2>Informations personnelles</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">Prénom *</label>
                        <input type="text" id="first_name" name="first_name" required 
                               value="<?= sanitize($user['first_name']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Nom *</label>
                        <input type="text" id="last_name" name="last_name" required 
                               value="<?= sanitize($user['last_name']) ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur *</label>
                        <input type="text" id="username" name="username" required 
                               pattern="[a-z0-9_]+"
                               value="<?= sanitize($user['username']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required 
                               value="<?= sanitize($user['email']) ?>">
                    </div>
                </div>
                
                <div class="form-actions-inline">
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Enregistrer
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Changement de mot de passe -->
        <form action="/profile/password" method="POST" class="profile-form">
            <?= csrf_field() ?>
            
            <div class="form-section">
                <h2>Changer le mot de passe</h2>
                
                <div class="form-group">
                    <label for="current_password">Mot de passe actuel *</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="new_password">Nouveau mot de passe *</label>
                        <input type="password" id="new_password" name="new_password" required minlength="8">
                        <span class="form-hint">Minimum 8 caractères</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password_confirmation">Confirmer *</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>
                </div>
                
                <div class="form-actions-inline">
                    <button type="submit" class="btn btn-secondary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        Changer le mot de passe
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Préférences de notification -->
        <div class="form-section">
            <h2>Notifications</h2>
            
            <div class="notification-prefs">
                <label class="toggle-label">
                    <input type="checkbox" checked>
                    <span class="toggle-switch"></span>
                    <span class="toggle-text">
                        <strong>Nouveaux documents</strong>
                        <small>Recevoir une notification quand un document est ajouté</small>
                    </span>
                </label>
                
                <label class="toggle-label">
                    <input type="checkbox" checked>
                    <span class="toggle-switch"></span>
                    <span class="toggle-text">
                        <strong>Annotations</strong>
                        <small>Recevoir une notification pour les nouvelles annotations</small>
                    </span>
                </label>
                
                <label class="toggle-label">
                    <input type="checkbox" checked>
                    <span class="toggle-switch"></span>
                    <span class="toggle-text">
                        <strong>Liaisons</strong>
                        <small>Recevoir une notification quand une liaison est créée vers vos documents</small>
                    </span>
                </label>
            </div>
        </div>
    </div>
</div>

<style>
.profile-layout {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 1.5rem;
    max-width: 1200px;
}

.profile-sidebar {
    position: sticky;
    top: calc(var(--header-height) + 1.5rem);
    height: fit-content;
}

.profile-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-lg);
    padding: 2rem;
    text-align: center;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 700;
    margin: 0 auto 1rem;
}

.profile-card h2 {
    font-size: 1.25rem;
    margin-bottom: 0.25rem;
}

.profile-card .username {
    color: var(--text-muted);
    margin-bottom: 1rem;
}

.profile-badges {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}

.profile-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    padding: 1rem 0;
    border-top: 1px solid var(--border-color);
    border-bottom: 1px solid var(--border-color);
    margin-bottom: 1rem;
}

.profile-stat {
    text-align: center;
}

.profile-stat .stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    display: block;
}

.profile-stat .stat-label {
    font-size: 0.75rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.profile-meta {
    text-align: left;
}

.profile-meta p {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
}

.profile-main {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.profile-form {
    margin: 0;
}

.form-actions-inline {
    padding-top: 1rem;
}

.badge-admin {
    background: rgba(139, 92, 246, 0.1);
    color: var(--purple);
}

.notification-prefs {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.toggle-label {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    cursor: pointer;
}

.toggle-label input {
    display: none;
}

.toggle-switch {
    width: 44px;
    height: 24px;
    background: var(--gray-300);
    border-radius: 12px;
    position: relative;
    transition: background var(--transition-fast);
    flex-shrink: 0;
}

.toggle-switch::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    top: 2px;
    left: 2px;
    transition: transform var(--transition-fast);
    box-shadow: var(--shadow-sm);
}

.toggle-label input:checked + .toggle-switch {
    background: var(--primary);
}

.toggle-label input:checked + .toggle-switch::after {
    transform: translateX(20px);
}

.toggle-text {
    display: flex;
    flex-direction: column;
}

.toggle-text strong {
    font-weight: 500;
    margin-bottom: 0.125rem;
}

.toggle-text small {
    font-size: 0.8125rem;
    color: var(--text-muted);
}

@media (max-width: 900px) {
    .profile-layout {
        grid-template-columns: 1fr;
    }
    
    .profile-sidebar {
        position: static;
    }
}
</style>

<script>
// Validation du changement de mot de passe
document.querySelectorAll('form')[1].addEventListener('submit', function(e) {
    const newPass = document.getElementById('new_password').value;
    const confirm = document.getElementById('new_password_confirmation').value;
    
    if (newPass !== confirm) {
        e.preventDefault();
        alert('Les nouveaux mots de passe ne correspondent pas');
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
