<?php
$pageTitle = 'Recherche : ' . $query;
ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Résultats de recherche</h1>
        <p><?= $results['total'] ?> résultat(s) pour "<?= sanitize($query) ?>"</p>
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
            <input type="text" name="q" value="<?= sanitize($query) ?>" placeholder="Rechercher des documents, du contenu..." autofocus>
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </div>
        
        <div class="search-options">
            <label class="checkbox-label">
                <input type="checkbox" name="content" <?= $searchContent ? 'checked' : '' ?>>
                <span>Rechercher dans le contenu OCR</span>
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
    <h3>Aucun résultat</h3>
    <p>Aucun document ne correspond à votre recherche "<?= sanitize($query) ?>"</p>
    <div class="search-tips">
        <h4>Conseils de recherche :</h4>
        <ul>
            <li>Vérifiez l'orthographe des termes</li>
            <li>Essayez des mots-clés plus généraux</li>
            <li>Activez la recherche dans le contenu OCR pour rechercher dans le texte des documents</li>
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
                    <span class="badge badge-<?= $doc['document_type'] ?>"><?= \App\Models\Document::TYPES[$doc['document_type']] ?? 'Autre' ?></span>
                    <?php if ($doc['team_name']): ?>
                    <span class="team-badge" style="--team-color: <?= $doc['team_color'] ?>">
                        <?= sanitize($doc['team_name']) ?>
                    </span>
                    <?php endif; ?>
                    <?php if ($doc['reference_number']): ?>
                    <span class="ref">Réf: <?= highlightSearch($doc['reference_number'], $query) ?></span>
                    <?php endif; ?>
                </div>
                
                <?php if ($doc['description']): ?>
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
                        <div class="avatar-xs"><?= strtoupper(substr($doc['first_name'], 0, 1) . substr($doc['last_name'], 0, 1)) ?></div>
                        <?= sanitize($doc['first_name'] . ' ' . $doc['last_name']) ?>
                    </span>
                    <span class="result-date"><?= formatDate($doc['created_at']) ?></span>
                    <span class="result-size"><?= formatFileSize($doc['file_size']) ?></span>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if ($results['last_page'] > 1): ?>
<div class="pagination">
    <?php if ($results['current_page'] > 1): ?>
    <a href="?q=<?= urlencode($query) ?>&page=<?= $results['current_page'] - 1 ?><?= $searchContent ? '&content=1' : '' ?>" class="pagination-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </a>
    <?php endif; ?>
    
    <span class="pagination-info">
        Page <?= $results['current_page'] ?> sur <?= $results['last_page'] ?>
    </span>
    
    <?php if ($results['current_page'] < $results['last_page']): ?>
    <a href="?q=<?= urlencode($query) ?>&page=<?= $results['current_page'] + 1 ?><?= $searchContent ? '&content=1' : '' ?>" class="pagination-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php endif; ?>

<?php
// Helper pour surligner les termes recherchés
function highlightSearch($text, $query) {
    $text = sanitize($text);
    $words = explode(' ', $query);
    foreach ($words as $word) {
        if (strlen($word) > 2) {
            $text = preg_replace('/(' . preg_quote($word, '/') . ')/i', '<mark>$1</mark>', $text);
        }
    }
    return $text;
}
?>

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
    gap: 1rem;
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
}

.result-icon svg {
    color: var(--primary);
}

.result-content {
    flex: 1;
    min-width: 0;
}

.result-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.result-title mark {
    background: rgba(245, 158, 11, 0.3);
    color: inherit;
    padding: 0 2px;
    border-radius: 2px;
}

.result-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
    margin-bottom: 0.5rem;
}

.result-meta .ref {
    font-size: 0.8125rem;
    color: var(--text-muted);
}

.result-description {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

.result-description mark {
    background: rgba(245, 158, 11, 0.3);
    color: inherit;
}

.result-content-match {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: var(--bg-secondary);
    border-radius: var(--border-radius);
    font-size: 0.8125rem;
    color: var(--text-secondary);
    margin-bottom: 0.75rem;
}

.result-content-match svg {
    flex-shrink: 0;
    margin-top: 2px;
    color: var(--text-muted);
}

.result-content-match mark {
    background: rgba(245, 158, 11, 0.3);
    color: inherit;
}

.result-footer {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.8125rem;
    color: var(--text-muted);
}

.result-author {
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.search-tips {
    text-align: left;
    max-width: 400px;
    margin: 2rem auto 0;
    padding: 1.5rem;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-lg);
}

.search-tips h4 {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.search-tips ul {
    list-style: none;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.search-tips li {
    padding: 0.375rem 0;
    padding-left: 1.25rem;
    position: relative;
}

.search-tips li::before {
    content: '→';
    position: absolute;
    left: 0;
    color: var(--primary);
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
