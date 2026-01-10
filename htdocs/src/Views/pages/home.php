<?php
/**
 * DocuFlow - Page d'accueil / Dashboard
 * 
 * Emplacement: htdocs/src/Views/pages/home.php
 */

$pageTitle = __('dashboard');
ob_start();
?>

<div class="dashboard-page">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3B82F6, #2563EB);">
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
                    <a href="/documents/upload" class="btn btn-primary btn-sm"><?= __('upload_document') ?></a>
                </div>
                <?php else: ?>
                <div class="documents-list">
                    <?php foreach ($recentDocuments as $doc): ?>
                    <a href="/documents/<?= $doc['id'] ?>" class="document-item">
                        <div class="document-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div class="document-info">
                            <span class="document-title"><?= sanitize($doc['title']) ?></span>
                            <span class="document-meta">
                                <span class="document-type"><?= sanitize($doc['document_type'] ?? __('type_other')) ?></span>
                                <span class="document-date"><?= formatDate($doc['created_at'], 'd/m/Y H:i') ?></span>
                            </span>
                        </div>
                        <div class="document-avatar" title="<?= sanitize($doc['first_name'] . ' ' . $doc['last_name']) ?>">
                            <?= strtoupper(substr($doc['first_name'] ?? '', 0, 1) . substr($doc['last_name'] ?? '', 0, 1)) ?>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Activité récente -->
        <div class="card recent-activity">
            <div class="card-header">
                <h2>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <?= __('recent_activity') ?>
                </h2>
                <a href="/activity" class="btn btn-ghost btn-sm"><?= __('view_all') ?></a>
            </div>
            <div class="card-body">
                <?php if (empty($recentActivities)): ?>
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <p><?= __('no_recent_activity') ?></p>
                </div>
                <?php else: ?>
                <div class="activity-list">
                    <?php foreach ($recentActivities as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-avatar" title="<?= sanitize(($activity['first_name'] ?? '') . ' ' . ($activity['last_name'] ?? '')) ?>">
                            <?= strtoupper(substr($activity['first_name'] ?? '', 0, 1) . substr($activity['last_name'] ?? '', 0, 1)) ?>
                        </div>
                        <div class="activity-content">
                            <span class="activity-text">
                                <strong><?= sanitize(($activity['first_name'] ?? '') . ' ' . ($activity['last_name'] ?? '')) ?></strong>
                                <?= translateActivityDescription($activity['description'] ?? '') ?>
                            </span>
                            <span class="activity-time"><?= formatDate($activity['created_at'], 'd/m H:i') ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Équipes -->
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
                        <div class="team-color" style="background-color: <?= sanitize($team['color'] ?? '#6B7280') ?>"></div>
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
        
        <!-- Dernières liaisons -->
        <div class="card recent-links">
            <div class="card-header">
                <h2>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                    </svg>
                    <?= __('links') ?>
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
                        <div class="link-visual">
                            <span class="link-source" title="<?= sanitize($link['source_title'] ?? '') ?>">
                                <?= sanitize(substr($link['source_title'] ?? '', 0, 20)) ?><?= strlen($link['source_title'] ?? '') > 20 ? '...' : '' ?>
                            </span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                            <span class="link-target" title="<?= sanitize($link['target_title'] ?? '') ?>">
                                <?= sanitize(substr($link['target_title'] ?? '', 0, 20)) ?><?= strlen($link['target_title'] ?? '') > 20 ? '...' : '' ?>
                            </span>
                        </div>
                        <span class="link-meta">
                            <?= sanitize($link['first_name'] ?? '') ?> • <?= formatDate($link['created_at'] ?? '', 'd/m H:i') ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Stockage -->
    <div class="storage-info">
        <div class="storage-header">
            <span><?= __('storage_used') ?></span>
            <span><?= formatFileSize($stats['documents']['storage_used'] ?? 0) ?></span>
        </div>
        <div class="storage-bar">
            <div class="storage-used" style="width: <?= min((($stats['documents']['storage_used'] ?? 0) / (1024 * 1024 * 1024)) * 100, 100) ?>%"></div>
        </div>
    </div>
</div>

<style>
/* Dashboard Page Styles */
.dashboard-page {
    padding: 24px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.stat-card {
    background: var(--bg-primary, white);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.stat-content {
    flex: 1;
}

.stat-value {
    display: block;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary, #111827);
}

.stat-label {
    font-size: 0.875rem;
    color: var(--text-secondary, #6B7280);
}

.stat-trend {
    font-size: 0.75rem;
    color: var(--text-secondary, #6B7280);
    display: flex;
    align-items: center;
    gap: 4px;
}

.stat-trend.positive {
    color: #10B981;
}

.stat-trend.warning {
    color: #F59E0B;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
    margin-bottom: 24px;
}

@media (max-width: 1024px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}

.card {
    background: var(--bg-primary, white);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color, #e5e7eb);
}

.card-header h2 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text-primary, #111827);
}

.card-body {
    padding: 16px 20px;
    max-height: 400px;
    overflow-y: auto;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 32px;
    color: var(--text-secondary, #6B7280);
    text-align: center;
    gap: 12px;
}

.empty-state.small {
    padding: 16px;
}

/* Documents list */
.documents-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.document-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border-radius: 8px;
    text-decoration: none;
    color: inherit;
    transition: background 0.15s;
}

.document-item:hover {
    background: var(--bg-secondary, #f9fafb);
}

.document-icon {
    color: var(--primary, #3B82F6);
}

.document-info {
    flex: 1;
    min-width: 0;
}

.document-title {
    display: block;
    font-weight: 500;
    color: var(--text-primary, #111827);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.document-meta {
    display: flex;
    gap: 8px;
    font-size: 0.75rem;
    color: var(--text-secondary, #6B7280);
}

.document-type {
    background: var(--bg-secondary, #f3f4f6);
    padding: 2px 6px;
    border-radius: 4px;
}

.document-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--primary, #3B82F6);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Activity list */
.activity-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.activity-item {
    display: flex;
    gap: 12px;
    padding: 8px 0;
    border-bottom: 1px solid var(--border-color, #f3f4f6);
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--bg-secondary, #e5e7eb);
    color: var(--text-secondary, #6B7280);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
    min-width: 0;
}

.activity-text {
    display: block;
    font-size: 0.875rem;
    color: var(--text-primary, #111827);
    margin-bottom: 4px;
}

.activity-text strong {
    font-weight: 600;
}

.activity-time {
    font-size: 0.75rem;
    color: var(--text-muted, #9CA3AF);
}

/* Teams list */
.teams-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.team-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px;
    border-radius: 8px;
    transition: background 0.15s;
}

.team-item:hover {
    background: var(--bg-secondary, #f9fafb);
}

.team-color {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.team-info {
    flex: 1;
}

.team-name {
    display: block;
    font-weight: 500;
    color: var(--text-primary, #111827);
}

.team-members {
    font-size: 0.75rem;
    color: var(--text-secondary, #6B7280);
}

/* Links list */
.links-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.link-item {
    padding: 10px;
    border-radius: 8px;
    background: var(--bg-secondary, #f9fafb);
}

.link-visual {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}

.link-source,
.link-target {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary, #111827);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 120px;
}

.link-visual svg {
    color: var(--primary, #3B82F6);
    flex-shrink: 0;
}

.link-meta {
    font-size: 0.75rem;
    color: var(--text-secondary, #6B7280);
}

/* Storage */
.storage-info {
    background: var(--bg-primary, white);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 12px;
    padding: 16px 20px;
}

.storage-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.875rem;
    color: var(--text-secondary, #6B7280);
}

.storage-bar {
    height: 8px;
    background: var(--bg-secondary, #e5e7eb);
    border-radius: 4px;
    overflow: hidden;
}

.storage-used {
    height: 100%;
    background: linear-gradient(90deg, #3B82F6, #2563EB);
    border-radius: 4px;
    transition: width 0.3s ease;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: all 0.15s;
}

.btn-primary {
    background: var(--primary, #3B82F6);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-hover, #2563EB);
}

.btn-ghost {
    background: transparent;
    color: var(--primary, #3B82F6);
}

.btn-ghost:hover {
    background: var(--bg-secondary, #f3f4f6);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.8125rem;
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
