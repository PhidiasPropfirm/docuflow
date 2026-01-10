<?php
$isEdit = isset($user);
$pageTitle = $isEdit ? 'Modifier : ' . $user['first_name'] . ' ' . $user['last_name'] : 'Nouvel utilisateur';
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
        <h1><?= $isEdit ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' ?></h1>
    </div>
</div>

<div class="form-container">
    <form action="<?= $isEdit ? '/users/' . $user['id'] : '/users' ?>" method="POST">
        <?= csrf_field() ?>
        
        <!-- Informations personnelles -->
        <div class="form-section">
            <h2>Informations personnelles</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">Prénom *</label>
                    <input type="text" id="first_name" name="first_name" required 
                           value="<?= sanitize($user['first_name'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="last_name">Nom *</label>
                    <input type="text" id="last_name" name="last_name" required 
                           value="<?= sanitize($user['last_name'] ?? '') ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur *</label>
                    <input type="text" id="username" name="username" required 
                           pattern="[a-z0-9_]+"
                           value="<?= sanitize($user['username'] ?? '') ?>">
                    <span class="form-hint">Lettres minuscules, chiffres et underscores uniquement</span>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required 
                           value="<?= sanitize($user['email'] ?? '') ?>">
                </div>
            </div>
        </div>
        
        <!-- Sécurité -->
        <div class="form-section">
            <h2>Sécurité</h2>
            
            <?php if ($isEdit): ?>
            <div class="info-box">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="16" x2="12" y2="12"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <span>Laissez le mot de passe vide pour ne pas le modifier</span>
            </div>
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Mot de passe <?= $isEdit ? '' : '*' ?></label>
                    <input type="password" id="password" name="password" 
                           <?= $isEdit ? '' : 'required' ?>
                           minlength="8">
                    <span class="form-hint">Minimum 8 caractères</span>
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">Confirmer le mot de passe <?= $isEdit ? '' : '*' ?></label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           <?= $isEdit ? '' : 'required' ?>>
                </div>
            </div>
        </div>
        
        <!-- Rôle et équipe -->
        <div class="form-section">
            <h2>Rôle et équipe</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="role">Rôle *</label>
                    <select id="role" name="role" required>
                        <option value="member" <?= ($user['role'] ?? 'member') === 'member' ? 'selected' : '' ?>>Membre</option>
                        <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                    </select>
                    <span class="form-hint">Les administrateurs ont accès à la gestion des utilisateurs et équipes</span>
                </div>
                
                <div class="form-group">
                    <label for="team_id">Équipe</label>
                    <select id="team_id" name="team_id">
                        <option value="">Aucune équipe</option>
                        <?php foreach ($teams as $team): ?>
                        <option value="<?= $team['id'] ?>" <?= ($user['team_id'] ?? '') == $team['id'] ? 'selected' : '' ?>>
                            <?= sanitize($team['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="form-actions">
            <a href="/users" class="btn btn-ghost">Annuler</a>
            <button type="submit" class="btn btn-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <?= $isEdit ? 'Enregistrer' : 'Créer l\'utilisateur' ?>
            </button>
        </div>
    </form>
</div>

<style>
.info-box {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    background: rgba(59, 130, 246, 0.1);
    color: var(--primary);
    border-radius: var(--border-radius);
    margin-bottom: 1.25rem;
    font-size: 0.875rem;
}
</style>

<script>
// Validation du mot de passe
const password = document.getElementById('password');
const confirmation = document.getElementById('password_confirmation');

document.querySelector('form').addEventListener('submit', function(e) {
    if (password.value && password.value !== confirmation.value) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas');
        confirmation.focus();
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
