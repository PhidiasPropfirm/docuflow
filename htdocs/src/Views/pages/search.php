<?php
/**
 * DocuFlow - Page de recherche
 * Fichier: htdocs/src/Views/pages/search.php
 */

// Helper pour surligner les termes recherchés (doit être défini AVANT utilisation)
if (!function_exists('highlightSearch')) {
    function highlightSearch($text, $query) {
        if (empty($text) || empty($query)) return sanitize($text ?? '');
        $text = sanitize($text);
        $words = explode(' ', $query);
        foreach ($words as $word) {
            if (strlen($word) > 2) {
                $text = preg_replace('/(' . preg_quote($word, '/') . ')/i', '<mark>$1</mark>', $text);
            }
        }
        return $text;
    }
}

// Initialiser les variables avec des valeurs par défaut
$query = $query ?? '';
$searchContent = isset($_GET['content']) && $_GET['content'] == '1';

// Normaliser la structure des résultats
if (!isset($results) || !is_array($results)) {
    $results = ['data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1];
}

// Si les résultats viennent de l'ancienne structure du contrôleur
if (isset($results['documents']) && !isset($results['data'])) {
    $allResults = $results['documents'] ?? [];
    // Fusionner avec les résultats de contenu OCR si disponibles
    if (!empty($results['content'])) {
        $contentIds = array_column($results['content'], 'id');
        foreach ($results['content'] as $contentDoc) {
            $found = false;
            foreach ($allResults as &$doc) {
                if ($doc['id'] == $contentDoc['id']) {
                    $doc['content_match'] = $contentDoc['content_match'] ?? '';
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $allResults[] = $contentDoc;
            }
        }
    }
    $results = [
        'data' => $allResults,
        'total' => count($allResults),
        'current_page' => 1,
        'last_page' => 1
    ];
}

// S'assurer que 'total' existe
if (!isset($results['total'])) {
    $results['total'] = count($results['data'] ?? []);
}

$pageTitle = __('search_results');
ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= __('search_results') ?></h1>
        <p><?= $results['total'] ?> <?= __('results_count') ?> "<?= sanitize($query) ?>"</p>
    </div>
</div>

<!-- Filtres de recherche -->
<div class="search-filters">
    <form action="/search" method="GET" class="search-filters-form">
        <div class="search-input-large">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="q" value="<?= sanitize($query) ?>" placeholder="<?= __('search_placeholder') ?>" autofocus>
            <button type="submit" class="btn btn-primary"><?= __('search_button') ?></button>
        </div>
        
        <div class="search-options">
            <label class="checkbox-label">
                <input type="checkbox" name="content" value="1" <?= $searchContent ? 'checked' : '' ?>>
                <span><?= __('search_in_ocr') ?></span>
            </label>
        </div>
    </form>
</div>

<!-- Résultats -->
<?php if (empty($results['data'])): ?>
<div class="empty-state large">
    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
        <circle cx="11" cy="11" r="8"/>
        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    <h3><?= __('no_results') ?></h3>
    <p><?= __('no_results_for') ?> "<?= sanitize($query) ?>"</p>
    <div class="search-tips">
        <h4><?= __('search_tips_title') ?></h4>
        <ul>
            <li><?= __('search_tip_1') ?></li>
            <li><?= __('search_tip_2') ?></li>
            <li><?= __('search_tip_3') ?></li>
        </ul>
    </div>
</div>
<?php else: ?>

<div class="search-results">
    <?php foreach ($results['data'] as $doc): ?>
    <div class="search-result-item">
        <a href="/documents/<?= $doc['id'] ?>" class="result-link">
            <div class="result-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            
            <div class="result-content">
                <h3 class="result-title"><?= highlightSearch($doc['title'], $query) ?></h3>
                
                <div class="result-meta">
                    <span class="badge badge-<?= $doc['document_type'] ?? 'other' ?>"><?= \App\Models\Document::TYPES[$doc['document_type']] ?? __('other') ?></span>
                    <?php if (!empty($doc['team_name'])): ?>
                    <span class="team-badge" style="--team-color: <?= $doc['team_color'] ?? '#3B82F6' ?>">
                        <?= sanitize($doc['team_name']) ?>
                    </span>
                    <?php endif; ?>
                    <?php if (!empty($doc['reference_number'])): ?>
                    <span class="ref"><?= __('ref') ?>: <?= highlightSearch($doc['reference_number'], $query) ?></span>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($doc['description'])): ?>
                <p class="result-description"><?= highlightSearch(substr($doc['description'], 0, 200), $query) ?>...</p>
                <?php endif; ?>
                
                <?php if (!empty($doc['content_match'])): ?>
                <div class="result-content-match">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="4 7 4 4 20 4 20 7"/>
                        <line x1="9" y1="20" x2="15" y2="20"/>
                        <line x1="12" y1="4" x2="12" y2="20"/>
                    </svg>
                    <span>...<?= highlightSearch($doc['content_match'], $query) ?>...</span>
                </div>
                <?php endif; ?>
                
                <div class="result-footer">
                    <span class="result-author">
                        <div class="avatar-xs"><?= strtoupper(substr($doc['first_name'] ?? '', 0, 1) . substr($doc['last_name'] ?? '', 0, 1)) ?></div>
                        <?= sanitize(($doc['first_name'] ?? '') . ' ' . ($doc['last_name'] ?? '')) ?>
                    </span>
                    <span class="result-date"><?= formatDate($doc['created_at'] ?? '') ?></span>
                    <span class="result-size"><?= formatFileSize($doc['file_size'] ?? 0) ?></span>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if (($results['last_page'] ?? 1) > 1): ?>
<div class="pagination">
    <?php if (($results['current_page'] ?? 1) > 1): ?>
    <a href="?q=<?= urlencode($query) ?>&page=<?= ($results['current_page'] ?? 1) - 1 ?><?= $searchContent ? '&content=1' : '' ?>" class="pagination-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </a>
    <?php endif; ?>
    
    <span class="pagination-info">
        <?= __('page') ?> <?= $results['current_page'] ?? 1 ?> <?= __('of') ?> <?= $results['last_page'] ?? 1 ?>
    </span>
    
    <?php if (($results['current_page'] ?? 1) < ($results['last_page'] ?? 1)): ?>
    <a href="?q=<?= urlencode($query) ?>&page=<?= ($results['current_page'] ?? 1) + 1 ?><?= $searchContent ? '&content=1' : '' ?>" class="pagination-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php endif; ?>

<style>
.search-filters {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.search-input-large {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-lg);
    padding: 0.5rem 0.5rem 0.5rem 1rem;
    margin-bottom: 1rem;
}

.search-input-large:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-input-large svg {
    color: var(--gray-400);
    flex-shrink: 0;
}

.search-input-large input {
    flex: 1;
    border: none;
    background: none;
    padding: 0.75rem 0;
    font-size: 1rem;
    outline: none;
}

.search-options {
    display: flex;
    gap: 1.5rem;
}

.search-options .checkbox-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.search-results {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.search-result-item {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-lg);
    transition: all var(--transition-fast);
}

.search-result-item:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow-md);
}

.result-link {
    display: flex;
    gap: 1.25rem;
    padding: 1.25rem;
    text-decoration: none;
    color: inherit;
}

.result-icon {
    width: 48px;
    height: 48px;
    background: var(--bg-secondary);
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: var(--primary);
}

.result-content {
    flex: 1;
    min-width: 0;
}

.result-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.result-title mark {
    background: rgba(59, 130, 246, 0.2);
    color: var(--primary);
    padding: 0.1em 0.2em;
    border-radius: 2px;
}

.result-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.result-meta .ref {
    font-size: 0.8125rem;
    color: var(--text-muted);
}

.result-description {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.75rem;
    line-height: 1.5;
}

.result-description mark {
    background: rgba(59, 130, 246, 0.15);
    color: var(--text-primary);
}

.result-content-match {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 0.75rem;
    background: var(--bg-secondary);
    border-radius: var(--border-radius);
    font-size: 0.8125rem;
    color: var(--text-secondary);
    margin-bottom: 0.75rem;
}

.result-content-match svg {
    color: var(--primary);
    flex-shrink: 0;
    margin-top: 2px;
}

.result-footer {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    font-size: 0.8125rem;
    color: var(--text-muted);
}

.result-author {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.avatar-xs {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.625rem;
    font-weight: 600;
}

.empty-state.large {
    padding: 4rem 2rem;
    text-align: center;
}

.empty-state.large svg {
    color: var(--gray-400);
    margin-bottom: 1.5rem;
}

.empty-state.large h3 {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

.search-tips {
    margin-top: 2rem;
    text-align: left;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.search-tips h4 {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.75rem;
}

.search-tips ul {
    list-style: disc;
    padding-left: 1.25rem;
    color: var(--text-muted);
    font-size: 0.875rem;
}

.search-tips li {
    margin-bottom: 0.5rem;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 2rem;
}

.pagination-btn {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    color: var(--text-secondary);
    transition: all var(--transition-fast);
}

.pagination-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
}

.pagination-info {
    font-size: 0.875rem;
    color: var(--text-muted);
}

@media (max-width: 640px) {
    .result-link {
        flex-direction: column;
    }
    
    .result-icon {
        width: 40px;
        height: 40px;
    }
    
    .result-footer {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
