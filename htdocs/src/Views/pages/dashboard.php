<?php
/**
 * DocuFlow - Dashboard avec traduction des activités et bouton reset
 * Fichier: htdocs/src/Views/pages/dashboard.php
 */

$pageTitle = __('dashboard');
ob_start();
?>

<div class="dashboard">
    <!-- Header avec bouton Reset -->
    <div class="dashboard-action-bar">
        <div class="action-bar-info">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            <span><?= __('reset_session_info') ?></span>
        </div>
        <button type="button" onclick="openResetModal()" class="btn btn-danger-outline reset-all-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                <line x1="10" y1="11" x2="10" y2="17"/>
                <line x1="14" y1="11" x2="14" y2="17"/>
            </svg>
            <?= __('reset_all_btn') ?>
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?= number_format($stats['documents']['total'] ?? 0) ?></span>
                <span class="stat-label"><?= __('documents') ?></span>
            </div>
            <div class="stat-trend positive">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                </svg>
                +<?= $stats['documents']['this_month'] ?? 0 ?> <?= __('this_month') ?>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10B981, #059669);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?= number_format($stats['documents']['total_links'] ?? 0) ?></span>
                <span class="stat-label"><?= __('links') ?></span>
            </div>
            <div class="stat-trend">
                <?= __('mapping_active') ?>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #F59E0B, #D97706);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?= number_format($stats['documents']['total_annotations'] ?? 0) ?></span>
                <span class="stat-label"><?= __('annotations') ?></span>
            </div>
            <div class="stat-trend <?= ($stats['unresolved_annotations'] ?? 0) > 0 ? 'warning' : '' ?>">
                <?= $stats['unresolved_annotations'] ?? 0 ?> <?= __('unresolved') ?>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #8B5CF6, #7C3AED);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-value"><?= number_format($stats['users'] ?? 0) ?></span>
                <span class="stat-label"><?= __('users') ?></span>
            </div>
            <div class="stat-trend">
                <?= __('active') ?>
            </div>
        </div>
    </div>
    
    <!-- Main Content Grid -->
    <div class="dashboard-grid">
        <!-- Documents récents -->
        <div class="card recent-documents">
            <div class="card-header">
                <h2>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <?= __('recent_documents') ?>
                </h2>
                <a href="/documents" class="btn btn-ghost btn-sm"><?= __('view_all') ?></a>
            </div>
            <div class="card-body">
                <?php if (empty($recentDocuments)): ?>
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <p><?= __('no_documents') ?></p>
                    <a href="/documents/create" class="btn btn-primary btn-sm"><?= __('add_document') ?></a>
                </div>
                <?php else: ?>
                <div class="document-list">
                    <?php foreach ($recentDocuments as $doc): ?>
                    <a href="/documents/<?= $doc['id'] ?>" class="document-item">
                        <div class="document-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div class="document-info">
                            <span class="document-title"><?= sanitize($doc['title']) ?></span>
                            <span class="document-meta">
                                <span class="badge badge-<?= $doc['document_type'] ?? 'other' ?>"><?= \App\Models\Document::TYPES[$doc['document_type']] ?? __('other') ?></span>
                                <?= formatDate($doc['created_at']) ?>
                            </span>
                        </div>
                        <div class="document-author">
                            <div class="avatar-sm"><?= strtoupper(substr($doc['first_name'] ?? '', 0, 1) . substr($doc['last_name'] ?? '', 0, 1)) ?></div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Activité récente avec traduction -->
        <div class="card recent-activity">
            <div class="card-header">
                <h2>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                    <?= __('recent_activity') ?>
                </h2>
                <a href="/activity" class="btn btn-ghost btn-sm"><?= __('view_all') ?></a>
            </div>
            <div class="card-body">
                <?php if (empty($recentActivity)): ?>
                <div class="empty-state small">
                    <p><?= __('no_recent_activity') ?></p>
                </div>
                <?php else: ?>
                <div class="activity-list">
                    <?php foreach ($recentActivity as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-avatar">
                            <?= strtoupper(substr($activity['first_name'] ?? '', 0, 1) . substr($activity['last_name'] ?? '', 0, 1)) ?>
                        </div>
                        <div class="activity-content">
                            <span class="activity-text">
                                <strong><?= !empty($activity['first_name']) ? sanitize($activity['first_name'] . ' ' . ($activity['last_name'] ?? '')) : __('system') ?></strong>
                                <?= translateActivityDescription($activity['description'] ?? $activity['action'] ?? '') ?>
                            </span>
                            <span class="activity-time"><?= formatDate($activity['created_at'] ?? '', 'd/m H:i') ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Liaisons récentes -->
        <div class="card recent-links">
            <div class="card-header">
                <h2>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                    </svg>
                    <?= __('recent_links') ?>
                </h2>
            </div>
            <div class="card-body">
                <?php if (empty($recentLinks)): ?>
                <div class="empty-state small">
                    <p><?= __('no_links') ?></p>
                </div>
                <?php else: ?>
                <div class="links-list">
                    <?php foreach ($recentLinks as $link): ?>
                    <div class="link-item">
                        <div class="link-docs">
                            <span class="link-doc"><?= sanitize($link['source_title'] ?? 'Document') ?></span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                            <span class="link-doc"><?= sanitize($link['target_title'] ?? 'Document') ?></span>
                        </div>
                        <span class="link-date"><?= formatDate($link['created_at'] ?? '', 'd/m') ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Équipes (admin uniquement) -->
        <?php if (isAdmin()): ?>
        <div class="card teams-overview">
            <div class="card-header">
                <h2>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <?= __('teams') ?>
                </h2>
            </div>
            <div class="card-body">
                <?php if (empty($teams)): ?>
                <div class="empty-state small">
                    <p><?= __('no_team') ?></p>
                </div>
                <?php else: ?>
                <div class="teams-list">
                    <?php foreach ($teams as $team): ?>
                    <div class="team-item">
                        <div class="team-color" style="background-color: <?= $team['color'] ?? '#6B7280' ?>"></div>
                        <div class="team-info">
                            <span class="team-name"><?= sanitize($team['name']) ?></span>
                            <span class="team-members"><?= $team['member_count'] ?? 0 ?> <?= __('members') ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Barre d'action reset */
.dashboard-action-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(135deg, #F3F4F6, #E5E7EB);
    border: 1px solid #D1D5DB;
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 20px;
}

.action-bar-info {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #4B5563;
    font-size: 0.875rem;
    font-weight: 500;
}

.action-bar-info svg {
    color: #6B7280;
}

/* Bouton Tout supprimer */
.reset-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    background: transparent;
    border: 1px solid #DC2626;
    color: #DC2626;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.reset-all-btn:hover {
    background: #DC2626;
    color: white;
}

@media (max-width: 600px) {
    .dashboard-action-bar {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';

// ============================================================
// Modal de réinitialisation complète (tous les utilisateurs)
// ============================================================
include __DIR__ . '/../components/reset-modal.php';
?>
