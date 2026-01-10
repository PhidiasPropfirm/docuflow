<!DOCTYPE html>
<html lang="<?= currentLang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>DocuFlow</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📄</text></svg>">
    <link rel="stylesheet" href="/css/style.css">
    
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    
    <!-- Fabric.js pour les zones -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    
    <!-- Tesseract.js pour l'OCR -->
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <span class="logo-icon">📄</span>
                <span class="logo-text">DocuFlow</span>
            </div>
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <a href="/dashboard" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/dashboard') === 0 || $_SERVER['REQUEST_URI'] === '/' ? 'active' : '' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                </svg>
                <span><?= __('dashboard') ?></span>
            </a>
            
            <a href="/documents" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/documents') === 0 ? 'active' : '' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <span><?= __('nav_documents') ?></span>
            </a>
            
            <a href="/activity" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/activity') === 0 ? 'active' : '' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <span><?= __('activity') ?></span>
            </a>
            
            <?php if (isAdmin()): ?>
            <div class="nav-divider"></div>
            <div class="nav-section-title"><?= __('nav_administration') ?></div>
            
            <a href="/users" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/users') === 0 ? 'active' : '' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span><?= __('nav_users') ?></span>
            </a>
            
            <a href="/teams" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/teams') === 0 ? 'active' : '' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
                <span><?= __('nav_teams') ?></span>
            </a>
            
            <a href="/roles" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/roles') === 0 ? 'active' : '' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                <span><?= __('nav_roles') ?></span>
            </a>
            <?php endif; ?>
        </nav>
        
        <div class="sidebar-footer">
            <a href="/profile" class="user-menu">
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['user']['first_name'], 0, 1) . substr($_SESSION['user']['last_name'], 0, 1)) ?>
                </div>
                <div class="user-info">
                    <span class="user-name"><?= sanitize($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']) ?></span>
                    <span class="user-role"><?= ucfirst($_SESSION['user']['role']) ?></span>
                </div>
            </a>
            <a href="/logout" class="logout-btn" title="<?= __('nav_logout') ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </a>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <button class="mobile-menu-btn" onclick="toggleSidebarMobile()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            
            <div class="header-search">
                <form action="/search" method="GET" class="search-form">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="q" placeholder="<?= __('search_documents') ?>" autocomplete="off">
                </form>
            </div>
            
            <div class="header-actions">
                <!-- Sélecteur de langue -->
                <div class="lang-dropdown">
                    <button class="lang-btn" onclick="toggleLangMenu()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="2" y1="12" x2="22" y2="12"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        </svg>
                        <span><?= currentLang() === 'fr' ? 'FR' : 'EN' ?></span>
                    </button>
                    <div class="lang-menu" id="langMenu">
                        <a href="/lang?lang=fr" class="lang-option <?= currentLang() === 'fr' ? 'active' : '' ?>">
                            🇫🇷 <?= __('french') ?>
                        </a>
                        <a href="/lang?lang=en" class="lang-option <?= currentLang() === 'en' ? 'active' : '' ?>">
                            🇬🇧 <?= __('english') ?>
                        </a>
                    </div>
                </div>
                
                <!-- Notifications -->
                <div class="notification-dropdown" id="notificationDropdown">
                    <button class="notification-btn" onclick="toggleNotifications()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                    </button>
                    
                    <div class="notification-menu" id="notificationMenu">
                        <div class="notification-header">
                            <span><?= __('notifications') ?></span>
                            <button onclick="markAllRead()"><?= __('mark_all_read') ?></button>
                        </div>
                        <div class="notification-list" id="notificationList">
                            <div class="notification-empty"><?= __('loading') ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Upload rapide -->
                <a href="/documents/create" class="btn btn-primary btn-sm">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <span><?= __('upload') ?></span>
                </a>
            </div>
        </header>
        
        <!-- Flash Messages -->
        <?php if (hasFlash('success')): ?>
        <div class="alert alert-success">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <span><?= getFlash('success') ?></span>
            <button onclick="this.parentElement.remove()">&times;</button>
        </div>
        <?php endif; ?>
        
        <?php if (hasFlash('error')): ?>
        <div class="alert alert-error">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
            <span><?= getFlash('error') ?></span>
            <button onclick="this.parentElement.remove()">&times;</button>
        </div>
        <?php endif; ?>
        
        <?php if (hasFlash('warning')): ?>
        <div class="alert alert-warning">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            <span><?= getFlash('warning') ?></span>
            <button onclick="this.parentElement.remove()">&times;</button>
        </div>
        <?php endif; ?>
        
        <!-- Page Content -->
        <div class="page-content">
            <?= $content ?? '' ?>
        </div>
    </main>
    
    <?php if (isset($_SESSION['user'])): ?>
    <?php include __DIR__ . '/../components/chat-widget.php'; ?>
    <?php endif; ?>
    
    <script src="/js/app.js"></script>
    
    <?php // Script de sync temps réel pour les pages documents ?>
    <?php if (preg_match('/\/documents\/\d+/', $_SERVER['REQUEST_URI'])): ?>
    <script src="/js/document-sync.js"></script>
    <?php endif; ?>
    
    <script>
    // Toggle menu langue
    function toggleLangMenu() {
        const menu = document.getElementById('langMenu');
        menu.classList.toggle('show');
    }
    
    // Fermer le menu si on clique ailleurs
    document.addEventListener('click', function(e) {
        const dropdown = document.querySelector('.lang-dropdown');
        const menu = document.getElementById('langMenu');
        if (dropdown && menu && !dropdown.contains(e.target)) {
            menu.classList.remove('show');
        }
    });
    </script>
</body>
</html>
