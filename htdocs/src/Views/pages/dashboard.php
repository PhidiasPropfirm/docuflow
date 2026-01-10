<?php
$pageTitle = __('dashboard');
ob_start();
?>

<div class="dashboard">
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
                <span class="stat-value"><?= number_format($stats['documents']['total']) ?></span>
                <span class="stat-label"><?= __('documents') ?></span>
            </div>
            <div class="stat-trend positive">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                </svg>
                +<?= $stats['documents']['this_month'] ?> <?= __('this_month') ?>
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
                <span class="stat-value"><?= number_format($stats['documents']['total_links']) ?></span>
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
                <span class="stat-value"><?= number_format($stats['documents']['total_annotations']) ?></span>
                <span class="stat-label"><?= __('annotations') ?></span>
            </div>
            <div class="stat-trend <?= $stats['unresolved_annotations'] > 0 ? 'warning' : '' ?>">
                <?= $stats['unresolved_annotations'] ?> <?= __('unresolved') ?>
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
                <span class="stat-value"><?= number_format($stats['users']) ?></span>
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
                                <span class="badge badge-<?= $doc['document_type'] ?>"><?= \App\Models\Document::TYPES[$doc['document_type']] ?? __('other') ?></span>
                                <?= formatDate($doc['created_at']) ?>
                            </span>
                        </div>
                        <div class="document-author">
                            <div class="avatar-sm"><?= strtoupper(substr($doc['first_name'], 0, 1) . substr($doc['last_name'], 0, 1)) ?></div>
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
                <?php if (empty($recentActivity)): ?>
                <div class="empty-state">
                    <p><?= __('no_recent_activity') ?></p>
                </div>
                <?php else: ?>
                <div class="activity-list">
                    <?php foreach ($recentActivity as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-avatar">
                            <?php if ($activity['first_name']): ?>
                            <?= strtoupper(substr($activity['first_name'], 0, 1) . substr($activity['last_name'], 0, 1)) ?>
                            <?php else: ?>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                            </svg>
                            <?php endif; ?>
                        </div>
                        <div class="activity-content">
                            <span class="activity-text">
                                <strong><?= $activity['first_name'] ? sanitize($activity['first_name'] . ' ' . $activity['last_name']) : __('system') ?></strong>
                                <?= sanitize($activity['description'] ?? \App\Models\ActivityLog::ACTIONS[$activity['action']] ?? $activity['action']) ?>
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
                <div class="teams-list">
                    <?php foreach ($teams as $team): ?>
                    <div class="team-item">
                        <div class="team-color" style="background-color: <?= $team['color'] ?>"></div>
                        <div class="team-info">
                            <span class="team-name"><?= sanitize($team['name']) ?></span>
                            <span class="team-members"><?= $team['member_count'] ?> <?= __('members') ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
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
                        <div class="link-visual">
                            <span class="link-source" title="<?= sanitize($link['source_title']) ?>">
                                <?= sanitize(substr($link['source_title'], 0, 20)) ?><?= strlen($link['source_title']) > 20 ? '...' : '' ?>
                            </span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                            <span class="link-target" title="<?= sanitize($link['target_title']) ?>">
                                <?= sanitize(substr($link['target_title'], 0, 20)) ?><?= strlen($link['target_title']) > 20 ? '...' : '' ?>
                            </span>
                        </div>
                        <span class="link-meta">
                            <?= sanitize($link['first_name']) ?> • <?= formatDate($link['created_at'], 'd/m H:i') ?>
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
            <span><?= formatFileSize($stats['documents']['storage_used']) ?></span>
        </div>
        <div class="storage-bar">
            <div class="storage-used" style="width: <?= min(($stats['documents']['storage_used'] / (1024 * 1024 * 1024)) * 100, 100) ?>%"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
