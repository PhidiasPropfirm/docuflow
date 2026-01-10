/**
 * DocuFlow - Synchronisation temps réel des documents
 * Polling pour mettre à jour les zones, annotations et liaisons
 */
(function() {
    // Configuration
    const SYNC_INTERVAL = 5000; // 5 secondes
    const VIEWERS_INTERVAL = 10000; // 10 secondes
    
    let documentId = null;
    let lastSync = null;
    let syncTimer = null;
    let viewersTimer = null;
    let isActive = true;
    
    // Initialisation
    function init() {
        // Récupérer l'ID du document depuis l'URL
        const match = window.location.pathname.match(/\/documents\/(\d+)/);
        if (!match) return;
        
        documentId = parseInt(match[1]);
        lastSync = new Date().toISOString().slice(0, 19).replace('T', ' ');
        
        // Créer l'indicateur de viewers
        createViewersIndicator();
        
        // Démarrer le polling
        startPolling();
        
        // Arrêter le polling quand l'utilisateur quitte la page
        window.addEventListener('beforeunload', cleanup);
        
        // Pause quand l'onglet n'est pas visible
        document.addEventListener('visibilitychange', handleVisibilityChange);
        
        console.log('📡 Sync temps réel activé pour document #' + documentId);
    }
    
    // Créer l'indicateur des utilisateurs en ligne
    function createViewersIndicator() {
        const header = document.querySelector('.page-header-actions') || document.querySelector('.page-header');
        if (!header) return;
        
        const indicator = document.createElement('div');
        indicator.id = 'documentViewers';
        indicator.className = 'document-viewers';
        indicator.innerHTML = `
            <div class="viewers-avatars"></div>
            <span class="viewers-count"></span>
        `;
        
        // Insérer avant les actions
        const actions = header.querySelector('.page-header-actions');
        if (actions) {
            header.insertBefore(indicator, actions);
        } else {
            header.appendChild(indicator);
        }
        
        // Ajouter les styles
        addStyles();
    }
    
    // Ajouter les styles CSS
    function addStyles() {
        if (document.getElementById('syncStyles')) return;
        
        const style = document.createElement('style');
        style.id = 'syncStyles';
        style.textContent = `
            .document-viewers {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-right: 1rem;
            }
            
            .viewers-avatars {
                display: flex;
                flex-direction: row-reverse;
            }
            
            .viewer-avatar {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: var(--primary, #3B82F6);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.7rem;
                font-weight: 600;
                border: 2px solid white;
                margin-left: -8px;
                position: relative;
                cursor: default;
            }
            
            .viewer-avatar:last-child {
                margin-left: 0;
            }
            
            .viewer-avatar.is-me {
                background: #10B981;
            }
            
            .viewer-avatar[title]:hover::after {
                content: attr(title);
                position: absolute;
                bottom: -28px;
                left: 50%;
                transform: translateX(-50%);
                background: #1F2937;
                color: white;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 0.7rem;
                white-space: nowrap;
                z-index: 100;
            }
            
            .viewers-count {
                font-size: 0.8rem;
                color: var(--text-secondary, #6B7280);
            }
            
            .sync-notification {
                position: fixed;
                bottom: 100px;
                right: 20px;
                background: #1F2937;
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                font-size: 0.875rem;
                display: flex;
                align-items: center;
                gap: 10px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 9998;
                animation: slideIn 0.3s ease;
            }
            
            @keyframes slideIn {
                from { transform: translateX(100px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            
            .sync-notification.hide {
                animation: slideOut 0.3s ease forwards;
            }
            
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100px); opacity: 0; }
            }
            
            .sync-dot {
                width: 8px;
                height: 8px;
                background: #10B981;
                border-radius: 50%;
                animation: pulse 1s infinite;
            }
            
            .zone-highlight-new {
                animation: zoneHighlight 2s ease;
            }
            
            @keyframes zoneHighlight {
                0%, 100% { box-shadow: none; }
                50% { box-shadow: 0 0 20px rgba(16, 185, 129, 0.8); }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Démarrer le polling
    function startPolling() {
        // Sync immédiat puis périodique
        pollUpdates();
        pollViewers();
        
        syncTimer = setInterval(pollUpdates, SYNC_INTERVAL);
        viewersTimer = setInterval(pollViewers, VIEWERS_INTERVAL);
    }
    
    // Arrêter le polling
    function stopPolling() {
        if (syncTimer) clearInterval(syncTimer);
        if (viewersTimer) clearInterval(viewersTimer);
    }
    
    // Polling des mises à jour
    async function pollUpdates() {
        if (!isActive || !documentId) return;
        
        try {
            const response = await fetch(`/api/documents/sync?document_id=${documentId}&since=${encodeURIComponent(lastSync)}`);
            const data = await response.json();
            
            if (data.success && data.has_updates) {
                processUpdates(data.updates);
                lastSync = data.timestamp;
            }
        } catch (error) {
            console.error('Sync error:', error);
        }
    }
    
    // Polling des viewers
    async function pollViewers() {
        if (!isActive || !documentId) return;
        
        try {
            const response = await fetch(`/api/documents/viewers?document_id=${documentId}`);
            const data = await response.json();
            
            if (data.success) {
                updateViewersDisplay(data.viewers);
            }
        } catch (error) {
            console.error('Viewers error:', error);
        }
    }
    
    // Traiter les mises à jour
    function processUpdates(updates) {
        let hasNewContent = false;
        
        // Nouvelles zones
        if (updates.zones && updates.zones.length > 0) {
            updates.zones.forEach(zone => {
                if (!document.querySelector(`.zone-marker[data-id="${zone.id}"]`)) {
                    addZoneToPage(zone);
                    hasNewContent = true;
                }
            });
        }
        
        // Nouvelles annotations
        if (updates.annotations && updates.annotations.length > 0) {
            updates.annotations.forEach(annotation => {
                // Ajouter à la liste des annotations si elle est ouverte
                const annotationsList = document.querySelector(`#annotationsZone${annotation.zone_id}`);
                if (annotationsList && !annotationsList.querySelector(`[data-annotation-id="${annotation.id}"]`)) {
                    addAnnotationToList(annotation, annotationsList);
                    hasNewContent = true;
                }
            });
        }
        
        // Nouvelles liaisons
        if (updates.links && updates.links.length > 0) {
            hasNewContent = true;
        }
        
        // Afficher une notification si des mises à jour
        if (hasNewContent) {
            showNotification('Document mis à jour par un collaborateur');
        }
    }
    
    // Ajouter une zone à la page
    function addZoneToPage(zone) {
        const pageContainer = document.querySelector(`.pdf-page[data-page="${zone.page}"] .zones-layer`);
        if (!pageContainer) return;
        
        const zoneEl = document.createElement('div');
        zoneEl.className = 'zone-marker zone-highlight-new';
        zoneEl.dataset.id = zone.id;
        zoneEl.dataset.zoneId = zone.id;
        zoneEl.style.cssText = `
            left: ${zone.x}%;
            top: ${zone.y}%;
            width: ${zone.width}%;
            height: ${zone.height}%;
            border-color: ${zone.color};
            background: ${zone.color}20;
        `;
        zoneEl.innerHTML = `<span class="zone-label">${escapeHtml(zone.name)}</span>`;
        
        if (zone.tooltip) {
            zoneEl.title = zone.tooltip;
        }
        
        pageContainer.appendChild(zoneEl);
        
        // Retirer l'animation après 2s
        setTimeout(() => zoneEl.classList.remove('zone-highlight-new'), 2000);
    }
    
    // Ajouter une annotation à la liste
    function addAnnotationToList(annotation, container) {
        const annotationEl = document.createElement('div');
        annotationEl.className = 'annotation-item';
        annotationEl.dataset.annotationId = annotation.id;
        annotationEl.innerHTML = `
            <div class="annotation-header">
                <span class="annotation-author">${escapeHtml(annotation.user_name)}</span>
                <span class="annotation-date">À l'instant</span>
            </div>
            <div class="annotation-content">${escapeHtml(annotation.content)}</div>
        `;
        
        container.insertBefore(annotationEl, container.firstChild);
    }
    
    // Mettre à jour l'affichage des viewers
    function updateViewersDisplay(viewers) {
        const container = document.getElementById('documentViewers');
        if (!container) return;
        
        const avatarsContainer = container.querySelector('.viewers-avatars');
        const countSpan = container.querySelector('.viewers-count');
        
        // Générer les avatars (max 5)
        const displayViewers = viewers.slice(0, 5);
        avatarsContainer.innerHTML = displayViewers.map(v => `
            <div class="viewer-avatar ${v.is_me ? 'is-me' : ''}" title="${escapeHtml(v.name)}">
                ${v.initials}
            </div>
        `).join('');
        
        // Compteur
        if (viewers.length > 1) {
            countSpan.textContent = `${viewers.length} personnes sur ce document`;
        } else {
            countSpan.textContent = '';
        }
    }
    
    // Afficher une notification
    function showNotification(message) {
        // Supprimer l'ancienne notification
        const old = document.querySelector('.sync-notification');
        if (old) old.remove();
        
        const notification = document.createElement('div');
        notification.className = 'sync-notification';
        notification.innerHTML = `
            <div class="sync-dot"></div>
            <span>${escapeHtml(message)}</span>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-hide après 4s
        setTimeout(() => {
            notification.classList.add('hide');
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }
    
    // Gestion de la visibilité de l'onglet
    function handleVisibilityChange() {
        if (document.hidden) {
            isActive = false;
            stopPolling();
        } else {
            isActive = true;
            startPolling();
        }
    }
    
    // Cleanup avant de quitter
    function cleanup() {
        stopPolling();
        
        // Notifier le serveur qu'on quitte
        if (documentId) {
            navigator.sendBeacon('/api/documents/leave', JSON.stringify({
                document_id: documentId
            }));
        }
    }
    
    // Utilitaire
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Lancer quand le DOM est prêt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
