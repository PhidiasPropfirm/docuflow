/**
 * DocuFlow - JavaScript Principal
 * Portail collaboratif de gestion documentaire
 */

// ==========================================
// Fonctions utilitaires
// ==========================================

/**
 * Requête API avec gestion d'erreurs
 */
async function apiRequest(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.error || 'Erreur serveur');
        }
        
        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

/**
 * Formate une taille de fichier
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 o';
    const k = 1024;
    const sizes = ['o', 'Ko', 'Mo', 'Go'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Formate une date
 */
function formatDate(dateStr, options = {}) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        ...options
    });
}

/**
 * Débounce une fonction
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ==========================================
// Sidebar
// ==========================================

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('collapsed');
    
    // Sauvegarde l'état
    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
}

// Restaure l'état de la sidebar
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    if (sidebar && localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
    }
});

// Mobile sidebar
document.addEventListener('click', (e) => {
    const sidebar = document.getElementById('sidebar');
    const menuBtn = document.querySelector('.mobile-menu-btn');
    
    if (window.innerWidth <= 768 && sidebar && !sidebar.contains(e.target) && !menuBtn?.contains(e.target)) {
        sidebar.classList.remove('open');
    }
});

function toggleSidebarMobile() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('open');
}

// ==========================================
// Notifications
// ==========================================

let notificationPollInterval = null;
let lastNotificationCheck = new Date().toISOString();

function toggleNotifications() {
    const menu = document.getElementById('notificationMenu');
    menu.classList.toggle('show');
    
    if (menu.classList.contains('show')) {
        loadNotifications();
    }
}

async function loadNotifications() {
    try {
        const data = await apiRequest('/api/notifications');
        
        if (data.success) {
            renderNotifications(data.notifications);
            updateNotificationBadge(data.unread_count);
        }
    } catch (error) {
        console.error('Erreur chargement notifications:', error);
    }
}

function renderNotifications(notifications) {
    const list = document.getElementById('notificationList');
    
    if (notifications.length === 0) {
        list.innerHTML = '<div class="notification-empty">Aucune notification</div>';
        return;
    }
    
    list.innerHTML = notifications.map(notif => `
        <div class="notification-item ${notif.is_read ? '' : 'unread'}" onclick="handleNotificationClick(${notif.id}, '${notif.link || ''}')">
            <div class="notification-title">${escapeHtml(notif.title)}</div>
            <div class="notification-message">${escapeHtml(notif.message)}</div>
            <div class="notification-time">${formatDate(notif.created_at)}</div>
        </div>
    `).join('');
}

async function handleNotificationClick(id, link) {
    await markNotificationRead(id);
    
    if (link) {
        window.location.href = link;
    }
}

async function markNotificationRead(id) {
    try {
        await apiRequest('/api/notifications/read', {
            method: 'POST',
            body: JSON.stringify({ id })
        });
        loadNotifications();
    } catch (error) {
        console.error('Erreur:', error);
    }
}

async function markAllRead() {
    try {
        await apiRequest('/api/notifications/read-all', {
            method: 'POST'
        });
        loadNotifications();
    } catch (error) {
        console.error('Erreur:', error);
    }
}

function updateNotificationBadge(count) {
    const badge = document.getElementById('notificationBadge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    }
}

async function pollNotifications() {
    try {
        const data = await apiRequest(`/api/notifications/poll?since=${lastNotificationCheck}`);
        
        if (data.success) {
            lastNotificationCheck = data.timestamp;
            
            if (data.notifications.length > 0) {
                // Nouvelle notification - affiche un toast
                data.notifications.forEach(notif => {
                    showToast(notif.title, 'info');
                });
            }
            
            updateNotificationBadge(data.unread_count);
        }
    } catch (error) {
        console.error('Erreur polling:', error);
    }
}

// Démarre le polling des notifications
document.addEventListener('DOMContentLoaded', () => {
    // Charge les notifications initiales
    loadNotifications();
    
    // Poll toutes les 30 secondes
    notificationPollInterval = setInterval(pollNotifications, 30000);
});

// Ferme le menu des notifications en cliquant ailleurs
document.addEventListener('click', (e) => {
    const dropdown = document.getElementById('notificationDropdown');
    if (dropdown && !dropdown.contains(e.target)) {
        document.getElementById('notificationMenu')?.classList.remove('show');
    }
});

// ==========================================
// Utilitaires de sécurité
// ==========================================

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ==========================================
// Confirmation de suppression
// ==========================================

document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', (e) => {
        const message = form.dataset.confirm || 'Êtes-vous sûr de vouloir effectuer cette action ?';
        if (!confirm(message)) {
            e.preventDefault();
        }
    });
});

// ==========================================
// Auto-dismiss des alertes
// ==========================================

document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
});

// ==========================================
// Recherche en temps réel
// ==========================================

const searchInput = document.querySelector('.search-form input');

if (searchInput) {
    const searchDebounced = debounce(async (query) => {
        if (query.length < 2) return;
        
        // Optionnel: recherche en temps réel avec suggestion
        // const results = await apiRequest(`/api/search/content?q=${encodeURIComponent(query)}`);
        // Afficher les résultats...
    }, 300);
    
    searchInput.addEventListener('input', (e) => {
        searchDebounced(e.target.value);
    });
}

// ==========================================
// Gestion des raccourcis clavier
// ==========================================

document.addEventListener('keydown', (e) => {
    // Ctrl/Cmd + K : Focus sur la recherche
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.querySelector('.search-form input')?.focus();
    }
    
    // Escape : Ferme les modals
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal.open').forEach(modal => {
            modal.classList.remove('open');
        });
    }
});

// ==========================================
// Styles pour les toasts
// ==========================================

const toastStyles = document.createElement('style');
toastStyles.textContent = `
    .toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: var(--gray-800);
        color: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg);
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s ease;
        z-index: 10000;
    }
    .toast.show {
        transform: translateY(0);
        opacity: 1;
    }
    .toast-success { background: var(--success); }
    .toast-error { background: var(--danger); }
    .toast-warning { background: var(--warning); }
    .toast-info { background: var(--primary); }
`;
document.head.appendChild(toastStyles);

// ==========================================
// Initialisation
// ==========================================

console.log('DocuFlow initialisé');
