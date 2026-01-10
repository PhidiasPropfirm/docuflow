<?php
/**
 * Widget de chat en temps réel avec support multilingue
 * Inclure ce fichier dans le layout main.php
 */

// Récupérer les traductions pour JavaScript
$chatTranslations = [
    'chat_title' => __('chat_title'),
    'chat_general' => __('chat_general'),
    'chat_placeholder' => __('chat_placeholder'),
    'chat_send' => __('chat_send'),
    'chat_online' => __('chat_online'),
    'chat_no_messages' => __('chat_no_messages'),
    'chat_loading' => __('chat_loading'),
    'chat_reply_to' => __('chat_reply_to'),
    'chat_delete_confirm' => __('chat_delete_confirm'),
    'chat_open' => __('chat_open'),
    'chat_close' => __('chat_close'),
    'chat_reply' => __('chat_reply'),
    'chat_delete' => __('chat_delete'),
    'chat_cancel_reply' => __('chat_cancel_reply'),
    'chat_message_deleted' => __('chat_message_deleted'),
    'chat_error_send' => __('chat_error_send'),
    'chat_error_load' => __('chat_error_load'),
    'chat_error_delete' => __('chat_error_delete'),
    'chat_channel_general' => __('chat_channel_general'),
    'chat_channel_team' => __('chat_channel_team'),
    'chat_channel_document' => __('chat_channel_document'),
    'chat_you' => __('chat_you'),
    'time_now' => __('time_now'),
    'time_minutes' => __('time_minutes'),
    'time_hours' => __('time_hours'),
    'time_days' => __('time_days'),
    'today' => __('today'),
    'yesterday' => __('yesterday'),
];
?>
<!-- Chat Widget -->
<div id="chatWidget" class="chat-widget">
    <!-- Bouton flottant -->
    <button id="chatToggle" class="chat-toggle" title="<?= __('chat_open') ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <span id="chatUnreadBadge" class="chat-unread-badge" style="display: none;">0</span>
    </button>
    
    <!-- Fenêtre de chat -->
    <div id="chatWindow" class="chat-window">
        <div class="chat-header">
            <div class="chat-header-info">
                <h3><?= __('chat_title') ?></h3>
                <span id="chatOnlineCount" class="chat-online-indicator">
                    <span class="online-dot"></span>
                    <span class="online-count">0 <?= __('chat_online') ?></span>
                </span>
            </div>
            <div class="chat-header-actions">
                <select id="chatChannelSelect" class="chat-channel-select">
                    <option value="general"><?= __('chat_channel_general') ?></option>
                </select>
                <button id="chatClose" class="chat-close" title="<?= __('chat_close') ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <div id="chatMessages" class="chat-messages">
            <div class="chat-loading">
                <div class="spinner"></div>
                <?= __('chat_loading') ?>
            </div>
        </div>
        
        <!-- Zone de réponse -->
        <div id="chatReplyBar" class="chat-reply-bar" style="display: none;">
            <div class="reply-content">
                <span class="reply-label"><?= __('chat_reply_to') ?></span>
                <span id="chatReplyTo" class="reply-text"></span>
            </div>
            <button id="chatCancelReply" class="reply-cancel" title="<?= __('chat_cancel_reply') ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        
        <div class="chat-input-area">
            <input type="text" id="chatInput" class="chat-input" 
                   placeholder="<?= __('chat_placeholder') ?>" 
                   maxlength="1000"
                   autocomplete="off">
            <button id="chatSend" class="chat-send" title="<?= __('chat_send') ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<style>
/* Chat Widget Styles */
.chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: inherit;
}

.chat-toggle {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: var(--primary, #3B82F6);
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    transition: transform 0.2s, box-shadow 0.2s;
    position: relative;
}

.chat-toggle:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.5);
}

.chat-toggle.active {
    background: var(--text-secondary, #6B7280);
}

.chat-unread-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #EF4444;
    color: white;
    font-size: 11px;
    font-weight: 600;
    min-width: 20px;
    height: 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 5px;
}

.chat-window {
    position: absolute;
    bottom: 70px;
    right: 0;
    width: 380px;
    height: 500px;
    background: var(--bg-primary, white);
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: none;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid var(--border-color, #e5e7eb);
}

.chat-window.open {
    display: flex;
    animation: chatSlideUp 0.3s ease;
}

@keyframes chatSlideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background: var(--bg-secondary, #f9fafb);
    border-bottom: 1px solid var(--border-color, #e5e7eb);
}

.chat-header-info h3 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 4px 0;
}

.chat-online-indicator {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    color: var(--text-secondary, #6B7280);
}

.online-dot {
    width: 8px;
    height: 8px;
    background: #10B981;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.chat-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.chat-channel-select {
    padding: 6px 10px;
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 8px;
    font-size: 0.8rem;
    background: var(--bg-primary, white);
    cursor: pointer;
}

.chat-close {
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    cursor: pointer;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary, #6B7280);
    transition: background 0.15s;
}

.chat-close:hover {
    background: var(--bg-tertiary, #f3f4f6);
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.chat-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: var(--text-secondary, #6B7280);
    font-size: 0.875rem;
    gap: 12px;
}

.chat-loading .spinner {
    width: 24px;
    height: 24px;
    border: 2px solid var(--border-color, #e5e7eb);
    border-top-color: var(--primary, #3B82F6);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.chat-empty {
    text-align: center;
    color: var(--text-secondary, #6B7280);
    font-size: 0.875rem;
    padding: 40px 20px;
}

.chat-message {
    display: flex;
    gap: 10px;
    max-width: 85%;
}

.chat-message.mine {
    flex-direction: row-reverse;
    margin-left: auto;
}

.chat-avatar {
    width: 32px;
    height: 32px;
    min-width: 32px;
    background: var(--primary, #3B82F6);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 600;
}

.chat-message.mine .chat-avatar {
    background: #10B981;
}

.chat-bubble {
    background: var(--bg-secondary, #f3f4f6);
    padding: 10px 14px;
    border-radius: 16px;
    border-top-left-radius: 4px;
    position: relative;
}

.chat-message.mine .chat-bubble {
    background: var(--primary, #3B82F6);
    color: white;
    border-radius: 16px;
    border-top-right-radius: 4px;
}

.chat-bubble-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
}

.chat-bubble-name {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-primary, #111827);
}

.chat-message.mine .chat-bubble-name {
    color: rgba(255, 255, 255, 0.9);
}

.chat-bubble-time {
    font-size: 0.65rem;
    color: var(--text-secondary, #6B7280);
}

.chat-message.mine .chat-bubble-time {
    color: rgba(255, 255, 255, 0.7);
}

.chat-bubble-text {
    font-size: 0.875rem;
    line-height: 1.4;
    word-wrap: break-word;
}

.chat-bubble-reply {
    background: rgba(0, 0, 0, 0.05);
    padding: 6px 10px;
    border-radius: 8px;
    margin-bottom: 6px;
    font-size: 0.75rem;
    border-left: 2px solid var(--primary, #3B82F6);
}

.chat-message.mine .chat-bubble-reply {
    background: rgba(255, 255, 255, 0.15);
    border-left-color: rgba(255, 255, 255, 0.5);
}

.chat-bubble-actions {
    position: absolute;
    top: 4px;
    right: 4px;
    display: none;
}

.chat-message:hover .chat-bubble-actions {
    display: flex;
    gap: 4px;
}

.chat-bubble-action {
    width: 24px;
    height: 24px;
    border: none;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary, #6B7280);
    font-size: 0.7rem;
}

.chat-bubble-action:hover {
    background: rgba(0, 0, 0, 0.2);
}

.chat-reply-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 16px;
    background: var(--bg-secondary, #f9fafb);
    border-top: 1px solid var(--border-color, #e5e7eb);
}

.reply-content {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    overflow: hidden;
}

.reply-label {
    color: var(--primary, #3B82F6);
    font-weight: 500;
}

.reply-text {
    color: var(--text-secondary, #6B7280);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.reply-cancel {
    width: 24px;
    height: 24px;
    border: none;
    background: transparent;
    cursor: pointer;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary, #6B7280);
}

.reply-cancel:hover {
    background: var(--bg-tertiary, #f3f4f6);
}

.chat-input-area {
    display: flex;
    gap: 10px;
    padding: 12px 16px;
    border-top: 1px solid var(--border-color, #e5e7eb);
    background: var(--bg-primary, white);
}

.chat-input {
    flex: 1;
    padding: 10px 14px;
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 20px;
    font-size: 0.875rem;
    outline: none;
    transition: border-color 0.15s;
}

.chat-input:focus {
    border-color: var(--primary, #3B82F6);
}

.chat-send {
    width: 40px;
    height: 40px;
    border: none;
    background: var(--primary, #3B82F6);
    color: white;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.15s;
}

.chat-send:hover {
    background: #2563EB;
}

.chat-send:disabled {
    background: var(--text-secondary, #6B7280);
    cursor: not-allowed;
}

/* Date separator */
.chat-date-separator {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 8px 0;
}

.chat-date-separator::before,
.chat-date-separator::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border-color, #e5e7eb);
}

.chat-date-separator span {
    font-size: 0.7rem;
    color: var(--text-secondary, #6B7280);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Responsive */
@media (max-width: 480px) {
    .chat-window {
        width: calc(100vw - 40px);
        height: calc(100vh - 140px);
        bottom: 70px;
        right: 0;
        border-radius: 16px 16px 0 0;
    }
}
</style>

<script>
// Chat Widget JavaScript avec traductions
(function() {
    // Traductions injectées depuis PHP
    const TRANSLATIONS = <?= json_encode($chatTranslations, JSON_UNESCAPED_UNICODE) ?>;
    
    // Fonction de traduction
    function t(key, params = {}) {
        let text = TRANSLATIONS[key] || key;
        for (const [param, value] of Object.entries(params)) {
            text = text.replace(':' + param, value);
        }
        return text;
    }
    
    const POLL_INTERVAL = 3000; // 3 secondes
    const ONLINE_POLL_INTERVAL = 30000; // 30 secondes
    
    let chatOpen = false;
    let currentChannel = 'general';
    let lastMessageId = 0;
    let replyToId = null;
    let pollTimer = null;
    let onlinePollTimer = null;
    
    // Éléments DOM
    const widget = document.getElementById('chatWidget');
    const toggleBtn = document.getElementById('chatToggle');
    const window_ = document.getElementById('chatWindow');
    const closeBtn = document.getElementById('chatClose');
    const messagesContainer = document.getElementById('chatMessages');
    const input = document.getElementById('chatInput');
    const sendBtn = document.getElementById('chatSend');
    const channelSelect = document.getElementById('chatChannelSelect');
    const unreadBadge = document.getElementById('chatUnreadBadge');
    const onlineCount = document.getElementById('chatOnlineCount');
    const replyBar = document.getElementById('chatReplyBar');
    const replyToSpan = document.getElementById('chatReplyTo');
    const cancelReplyBtn = document.getElementById('chatCancelReply');
    
    // Ouvrir/Fermer le chat
    toggleBtn.addEventListener('click', () => {
        chatOpen = !chatOpen;
        window_.classList.toggle('open', chatOpen);
        toggleBtn.classList.toggle('active', chatOpen);
        toggleBtn.title = chatOpen ? t('chat_close') : t('chat_open');
        
        if (chatOpen) {
            loadMessages();
            startPolling();
            input.focus();
        } else {
            stopPolling();
        }
    });
    
    closeBtn.addEventListener('click', () => {
        chatOpen = false;
        window_.classList.remove('open');
        toggleBtn.classList.remove('active');
        toggleBtn.title = t('chat_open');
        stopPolling();
    });
    
    // Envoyer un message
    async function sendMessage() {
        const message = input.value.trim();
        if (!message) return;
        
        sendBtn.disabled = true;
        
        try {
            const response = await fetch('/api/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    channel: currentChannel,
                    message: message,
                    reply_to: replyToId
                })
            });
            
            if (response.ok) {
                input.value = '';
                cancelReply();
                await loadMessages();
                scrollToBottom();
            } else {
                console.error(t('chat_error_send'));
            }
        } catch (error) {
            console.error(t('chat_error_send'), error);
        } finally {
            sendBtn.disabled = false;
        }
    }
    
    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    // Charger les messages
    async function loadMessages() {
        try {
            const response = await fetch(`/api/chat/messages?channel=${currentChannel}&after=${lastMessageId}`);
            const data = await response.json();
            
            if (data.success && data.messages) {
                if (lastMessageId === 0) {
                    messagesContainer.innerHTML = '';
                    if (data.messages.length === 0) {
                        messagesContainer.innerHTML = `<div class="chat-empty">${t('chat_no_messages')}</div>`;
                    }
                }
                
                data.messages.forEach(msg => {
                    if (msg.id > lastMessageId) {
                        appendMessage(msg);
                        lastMessageId = msg.id;
                    }
                });
                
                if (data.online_count !== undefined) {
                    updateOnlineCount(data.online_count);
                }
            }
        } catch (error) {
            console.error(t('chat_error_load'), error);
        }
    }
    
    // Ajouter un message au DOM
    function appendMessage(msg) {
        const div = document.createElement('div');
        div.className = `chat-message ${msg.is_mine ? 'mine' : ''}`;
        div.dataset.id = msg.id;
        
        let replyHtml = '';
        if (msg.reply_to) {
            replyHtml = `
                <div class="chat-bubble-reply">
                    <strong>${escapeHtml(msg.reply_to.user_name)}:</strong>
                    ${escapeHtml(msg.reply_to.message)}...
                </div>
            `;
        }
        
        const displayName = msg.is_mine ? t('chat_you') : escapeHtml(msg.user_name);
        
        div.innerHTML = `
            <div class="chat-avatar">${msg.user_initials}</div>
            <div class="chat-bubble">
                <div class="chat-bubble-header">
                    <span class="chat-bubble-name">${displayName}</span>
                    <span class="chat-bubble-time">${msg.time_ago}</span>
                </div>
                ${replyHtml}
                <div class="chat-bubble-text">${escapeHtml(msg.message)}</div>
                <div class="chat-bubble-actions">
                    <button class="chat-bubble-action" onclick="chatReply(${msg.id}, '${escapeHtml(msg.message).substring(0, 30)}')" title="${t('chat_reply')}">↩</button>
                    ${msg.is_mine ? `<button class="chat-bubble-action" onclick="chatDelete(${msg.id})" title="${t('chat_delete')}">🗑</button>` : ''}
                </div>
            </div>
        `;
        
        messagesContainer.appendChild(div);
    }
    
    // Répondre à un message
    window.chatReply = function(id, preview) {
        replyToId = id;
        replyToSpan.textContent = preview + (preview.length >= 30 ? '...' : '');
        replyBar.style.display = 'flex';
        input.focus();
    };
    
    function cancelReply() {
        replyToId = null;
        replyBar.style.display = 'none';
    }
    
    cancelReplyBtn.addEventListener('click', cancelReply);
    
    // Supprimer un message
    window.chatDelete = async function(id) {
        if (!confirm(t('chat_delete_confirm'))) return;
        
        try {
            const response = await fetch(`/api/chat/delete/${id}`, {
                method: 'DELETE'
            });
            
            if (response.ok) {
                const msgEl = document.querySelector(`.chat-message[data-id="${id}"]`);
                if (msgEl) {
                    msgEl.remove();
                }
            } else {
                console.error(t('chat_error_delete'));
            }
        } catch (error) {
            console.error(t('chat_error_delete'), error);
        }
    };
    
    // Mettre à jour le compteur en ligne
    function updateOnlineCount(count) {
        const countSpan = onlineCount.querySelector('.online-count');
        if (countSpan) {
            countSpan.textContent = t('chat_online', { count: count });
        }
    }
    
    // Polling
    function startPolling() {
        stopPolling();
        pollTimer = setInterval(loadMessages, POLL_INTERVAL);
        onlinePollTimer = setInterval(() => {
            fetch('/api/chat/ping', { method: 'POST' });
        }, ONLINE_POLL_INTERVAL);
    }
    
    function stopPolling() {
        if (pollTimer) clearInterval(pollTimer);
        if (onlinePollTimer) clearInterval(onlinePollTimer);
    }
    
    // Scroll en bas
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // Échapper le HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Changement de canal
    channelSelect.addEventListener('change', () => {
        currentChannel = channelSelect.value;
        lastMessageId = 0;
        messagesContainer.innerHTML = `<div class="chat-loading"><div class="spinner"></div>${t('chat_loading')}</div>`;
        loadMessages();
    });
})();
</script>
