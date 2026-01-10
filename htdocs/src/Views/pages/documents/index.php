<?php
$pageTitle = __('documents');
ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= __('documents') ?></h1>
        <p><?= __('documents_count', ['count' => $documents['total']]) ?></p>
    </div>
    <a href="/documents/create" class="btn btn-primary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <?= __('new_document') ?>
    </a>
</div>

<!-- Filtres -->
<div class="filters-bar">
    <form action="/documents" method="GET" class="filters-form">
        <div class="filter-group">
            <input type="text" name="search" placeholder="<?= __('search') ?>" value="<?= sanitize($filters['search'] ?? '') ?>">
        </div>
        
        <div class="filter-group">
            <select name="type">
                <option value=""><?= __('all') ?> <?= strtolower(__('document_type')) ?>s</option>
                <?php foreach ($documentTypes as $key => $label): ?>
                <option value="<?= $key ?>" <?= ($filters['type'] ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="filter-group">
            <select name="team_id">
                <option value=""><?= __('all') ?> <?= strtolower(__('teams')) ?></option>
                <?php foreach ($teams as $team): ?>
                <option value="<?= $team['id'] ?>" <?= ($filters['team_id'] ?? '') == $team['id'] ? 'selected' : '' ?>><?= sanitize($team['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="filter-group">
            <input type="date" name="date_from" placeholder="<?= __('from') ?>" value="<?= $filters['date_from'] ?? '' ?>">
        </div>
        
        <div class="filter-group">
            <input type="date" name="date_to" placeholder="<?= __('to') ?>" value="<?= $filters['date_to'] ?? '' ?>">
        </div>
        
        <button type="submit" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            <?= __('filter') ?>
        </button>
        
        <?php if (!empty(array_filter($filters ?? []))): ?>
        <a href="/documents" class="btn btn-ghost"><?= __('reset') ?></a>
        <?php endif; ?>
    </form>
</div>

<!-- Liste des documents -->
<?php if (empty($documents['data'])): ?>
<div class="empty-state large">
    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
    </svg>
    <h3><?= __('no_documents') ?></h3>
    <p><?= __('no_documents_desc') ?></p>
    <a href="/documents/create" class="btn btn-primary"><?= __('add_document') ?></a>
</div>
<?php else: ?>

<div class="documents-grid">
    <?php foreach ($documents['data'] as $doc): ?>
    <div class="document-card">
        <a href="/documents/<?= $doc['id'] ?>" class="document-card-link">
            <div class="document-card-preview">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
            </div>
            
            <div class="document-card-body">
                <h3 class="document-card-title"><?= sanitize($doc['title']) ?></h3>
                
                <div class="document-card-meta">
                    <span class="badge badge-<?= $doc['document_type'] ?>"><?= $documentTypes[$doc['document_type']] ?? __('other') ?></span>
                    
                    <?php if ($doc['team_name']): ?>
                    <span class="team-badge" style="--team-color: <?= $doc['team_color'] ?>">
                        <?= sanitize($doc['team_name']) ?>
                    </span>
                    <?php endif; ?>
                </div>
                
                <?php if ($doc['reference_number']): ?>
                <div class="document-card-ref"><?= __('document_reference') ?>: <?= sanitize($doc['reference_number']) ?></div>
                <?php endif; ?>
                
                <div class="document-card-stats">
                    <span title="<?= __('zones') ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        </svg>
                        <?= $doc['zone_count'] ?>
                    </span>
                    <span title="<?= __('links') ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                        </svg>
                        <?= $doc['link_count'] ?>
                    </span>
                    <span title="<?= __('document_size') ?>">
                        <?= formatFileSize($doc['file_size']) ?>
                    </span>
                </div>
            </div>
            
            <div class="document-card-footer">
                <div class="document-card-author">
                    <div class="avatar-xs"><?= strtoupper(substr($doc['first_name'], 0, 1) . substr($doc['last_name'], 0, 1)) ?></div>
                    <span><?= sanitize($doc['first_name'] . ' ' . $doc['last_name']) ?></span>
                </div>
                <span class="document-card-date"><?= formatDate($doc['created_at'], 'd/m/Y') ?></span>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if ($documents['last_page'] > 1): ?>
<div class="pagination">
    <?php if ($documents['current_page'] > 1): ?>
    <a href="?page=<?= $documents['current_page'] - 1 ?><?= http_build_query(array_filter($filters ?? [])) ? '&' . http_build_query(array_filter($filters ?? [])) : '' ?>" class="pagination-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </a>
    <?php endif; ?>
    
    <span class="pagination-info">
        Page <?= $documents['current_page'] ?> / <?= $documents['last_page'] ?>
    </span>
    
    <?php if ($documents['current_page'] < $documents['last_page']): ?>
    <a href="?page=<?= $documents['current_page'] + 1 ?><?= http_build_query(array_filter($filters ?? [])) ? '&' . http_build_query(array_filter($filters ?? [])) : '' ?>" class="pagination-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
