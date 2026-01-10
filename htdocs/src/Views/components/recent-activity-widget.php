<?php
/**
 * DocuFlow - Widget "Recent Activity" avec traductions
 * 
 * Ce composant affiche les activités récentes avec traduction automatique
 * 
 * Utilisation:
 * <?php include __DIR__ . '/components/recent-activity-widget.php'; ?>
 */

// Inclure le helper si pas déjà fait
if (!function_exists('translateActivity')) {
    require_once __DIR__ . '/../Config/activity-helpers.php';
}

// Récupérer les activités récentes (mock data pour la démo)
// En production, utiliser: $activities = (new ActivityLog())->getRecent(10);
$activities = $activities ?? [
    [
        'id' => 1,
        'user_id' => 1,
        'first_name' => 'Marie',
        'last_name' => 'Dupont',
        'avatar' => null,
        'action' => 'upload',
        'description' => 'Upload de document',
        'entity_type' => 'document',
        'entity_id' => 15,
        'metadata' => json_encode(['document_name' => 'Facture_2024_001.pdf']),
        'created_at' => date('Y-m-d H:i:s', strtotime('-5 minutes')),
    ],
    [
        'id' => 2,
        'user_id' => 2,
        'first_name' => 'Jean',
        'last_name' => 'Martin',
        'avatar' => null,
        'action' => 'create_link',
        'description' => 'Création de liaison',
        'entity_type' => 'link',
        'entity_id' => 8,
        'metadata' => json_encode(['target_document' => 'Rapport_Q4.pdf']),
        'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
    ],
    [
        'id' => 3,
        'user_id' => 3,
        'first_name' => 'Sophie',
        'last_name' => 'Bernard',
        'avatar' => null,
        'action' => 'create_annotation',
        'description' => 'Ajout d\'annotation',
        'entity_type' => 'annotation',
        'entity_id' => 42,
        'metadata' => json_encode(['document_name' => 'Contrat_Client.pdf']),
        'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours')),
    ],
    [
        'id' => 4,
        'user_id' => 1,
        'first_name' => 'Marie',
        'last_name' => 'Dupont',
        'avatar' => null,
        'action' => 'resolve_annotation',
        'description' => 'Résolution d\'annotation',
        'entity_type' => 'annotation',
        'entity_id' => 38,
        'metadata' => null,
        'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
    ],
    [
        'id' => 5,
        'user_id' => 4,
        'first_name' => 'Pierre',
        'last_name' => 'Leroy',
        'avatar' => null,
        'action' => 'login',
        'description' => 'Connexion',
        'entity_type' => 'user',
        'entity_id' => 4,
        'metadata' => null,
        'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
    ],
];

// Formater les activités
$formattedActivities = array_map('formatActivityForDisplay', $activities);
?>

<!-- Widget Recent Activity -->
<div class="card activity-widget">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
            <?= __('recent_activity') ?>
        </h3>
        <a href="/activity" class="btn btn-sm btn-link"><?= __('view_all') ?></a>
    </div>
    
    <div class="card-body">
        <?php if (empty($formattedActivities)): ?>
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.5">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>
                <p><?= __('no_recent_activity') ?></p>
            </div>
        <?php else: ?>
            <div class="activity-list">
                <?php foreach ($formattedActivities as $activity): ?>
                    <div class="activity-item" data-activity-id="<?= $activity['id'] ?>">
                        <div class="activity-avatar">
                            <?php if ($activity['avatar']): ?>
                                <img src="<?= htmlspecialchars($activity['avatar']) ?>" alt="<?= htmlspecialchars($activity['user_name']) ?>">
                            <?php else: ?>
                                <span class="avatar-initials"><?= strtoupper(substr($activity['user_name'], 0, 1)) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="activity-content">
                            <div class="activity-header">
                                <span class="activity-user"><?= htmlspecialchars($activity['user_name']) ?></span>
                                <span class="activity-time"><?= $activity['time_ago'] ?></span>
                            </div>
                            <div class="activity-description">
                                <span class="activity-icon activity-<?= $activity['color'] ?>"><?= $activity['icon'] ?></span>
                                <span class="activity-text"><?= htmlspecialchars($activity['description']) ?></span>
                            </div>
                            <?php 
                            // Afficher le nom du document si disponible
                            $docName = $activity['metadata']['document_name'] ?? $activity['metadata']['target_document'] ?? null;
                            if ($docName): 
                            ?>
                                <div class="activity-meta">
                                    <span class="activity-document">📄 <?= htmlspecialchars($docName) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Widget Recent Activity Styles */
.activity-widget {
    background: var(--card-bg, #fff);
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.activity-widget .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color, #e5e7eb);
    background: var(--card-header-bg, #f9fafb);
}

.activity-widget .card-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary, #111827);
}

.activity-widget .card-title svg {
    color: var(--primary-color, #3b82f6);
}

.activity-widget .card-body {
    padding: 0;
    max-height: 400px;
    overflow-y: auto;
}

/* Empty state */
.activity-widget .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    color: var(--text-secondary, #6b7280);
    gap: 12px;
}

/* Activity list */
.activity-list {
    display: flex;
    flex-direction: column;
}

.activity-item {
    display: flex;
    gap: 12px;
    padding: 14px 20px;
    border-bottom: 1px solid var(--border-color, #f3f4f6);
    transition: background 0.2s;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-item:hover {
    background: var(--hover-bg, #f9fafb);
}

/* Avatar */
.activity-avatar {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    overflow: hidden;
    background: var(--avatar-bg, #e5e7eb);
    display: flex;
    align-items: center;
    justify-content: center;
}

.activity-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-initials {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-secondary, #6b7280);
}

/* Content */
.activity-content {
    flex: 1;
    min-width: 0;
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 4px;
}

.activity-user {
    font-weight: 600;
    font-size: 14px;
    color: var(--text-primary, #111827);
}

.activity-time {
    font-size: 12px;
    color: var(--text-muted, #9ca3af);
    white-space: nowrap;
}

.activity-description {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--text-secondary, #6b7280);
}

.activity-icon {
    flex-shrink: 0;
    font-size: 14px;
}

.activity-text {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Meta (document name) */
.activity-meta {
    margin-top: 4px;
    font-size: 12px;
}

.activity-document {
    color: var(--primary-color, #3b82f6);
    cursor: pointer;
}

.activity-document:hover {
    text-decoration: underline;
}

/* Color variants */
.activity-success { color: #10b981; }
.activity-danger { color: #ef4444; }
.activity-warning { color: #f59e0b; }
.activity-info { color: #3b82f6; }
.activity-primary { color: #6366f1; }
.activity-secondary { color: #6b7280; }

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .activity-widget {
        --card-bg: #1f2937;
        --card-header-bg: #111827;
        --border-color: #374151;
        --text-primary: #f9fafb;
        --text-secondary: #d1d5db;
        --text-muted: #9ca3af;
        --hover-bg: #374151;
        --avatar-bg: #4b5563;
    }
}

/* Responsive */
@media (max-width: 640px) {
    .activity-widget .card-header {
        padding: 12px 16px;
    }
    
    .activity-item {
        padding: 12px 16px;
    }
    
    .activity-avatar {
        width: 32px;
        height: 32px;
    }
}

/* Scrollbar styling */
.activity-widget .card-body::-webkit-scrollbar {
    width: 6px;
}

.activity-widget .card-body::-webkit-scrollbar-track {
    background: transparent;
}

.activity-widget .card-body::-webkit-scrollbar-thumb {
    background: var(--border-color, #e5e7eb);
    border-radius: 3px;
}

.activity-widget .card-body::-webkit-scrollbar-thumb:hover {
    background: var(--text-muted, #9ca3af);
}
</style>
