<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - DocuFlow</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📄</text></svg>">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <span class="logo-icon">📄</span>
                    <h1>DocuFlow</h1>
                </div>
                <p>Portail collaboratif de gestion documentaire</p>
            </div>
            
            <?php if (hasFlash('error')): ?>
            <div class="alert alert-error">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <span><?= getFlash('error') ?></span>
            </div>
            <?php endif; ?>
            
            <?php if (hasFlash('success')): ?>
            <div class="alert alert-success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <span><?= getFlash('success') ?></span>
            </div>
            <?php endif; ?>
            
            <form action="/login" method="POST" class="login-form">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label for="login">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        Identifiant ou Email
                    </label>
                    <input type="text" id="login" name="login" required autofocus 
                           placeholder="Votre nom d'utilisateur ou email">
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        Mot de passe
                    </label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" required 
                               placeholder="Votre mot de passe">
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="eyeIcon">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember">
                        <span class="checkmark"></span>
                        Se souvenir de moi
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    Se connecter
                </button>
            </form>
            
            <div class="login-footer">
                <p>DocuFlow v<?= APP_VERSION ?></p>
                <p class="text-muted">© <?= date('Y') ?> - Tous droits réservés</p>
            </div>
        </div>
        
        <div class="login-features">
            <div class="feature">
                <div class="feature-icon">📁</div>
                <h3>Gestion centralisée</h3>
                <p>Stockez et organisez tous vos documents PDF en un seul endroit</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🔗</div>
                <h3>Mapping intelligent</h3>
                <p>Créez des liaisons entre vos documents pour un suivi simplifié</p>
            </div>
            <div class="feature">
                <div class="feature-icon">👥</div>
                <h3>Collaboration</h3>
                <p>Travaillez en équipe avec annotations et notifications</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🔍</div>
                <h3>Recherche avancée</h3>
                <p>Retrouvez instantanément n'importe quel document</p>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }
    </script>
</body>
</html>
