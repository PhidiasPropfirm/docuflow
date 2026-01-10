<?php
/**
 * Page Activity - Journal d'activité
 * Version avec connexion directe
 */

// S'assurer que sanitize() existe
if (!function_exists('sanitize')) {
    function sanitize($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// S'assurer que __() existe (fonction de traduction)
if (!function_exists('__')) {
    function __($key, $params = []) {
        return $key; // Retourne la clé si pas de traduction
    }
}

$pageTitle = __('activity_log');

// ============================================
// RÉCUPÉRATION DES DONNÉES - CONNEXION DIRECTE
// ============================================

$activities = [];
$users = [];
$pagination = ['current_page' => 1, 'total_pages' => 1, 'total_items' => 0, 'per_page' => 30];
$debugInfo = '';

// Mode debug - mettre à false en production
$debugMode = isset($_GET['debug']);

try {
    // Connexion directe à la base de données
    $dbHost = 'sql100.infinityfree.com';
    $dbName = 'if0_40864379_privateaccountig';
    $dbUser = 'if0_40864379';
    
    // Lire le mot de passe depuis db.key
    $dbPass = '';
    $foundPath = '';
    $possiblePaths = [
        __DIR__ . '/../../Config/db.key',
        dirname(__DIR__, 2) . '/Config/db.key',
        $_SERVER['DOCUMENT_ROOT'] . '/../src/Config/db.key',
        '/home/vol1_7/infinityfree.com/if0_40864379/htdocs/src/Config/db.key',
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $dbPass = trim(file_get_contents($path));
            $foundPath = $path;
            break;
        }
    }
    
    if ($debugMode) {
        $debugInfo .= "DB Key trouvé: " . ($foundPath ? "Oui ($foundPath)" : "Non") . " | ";
    }
    
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    // Paramètres de filtrage
    $filterAction = $_GET['action'] ?? null;
    $filterUser = $_GET['user'] ?? null;
    $dateFrom = $_GET['from'] ?? null;
    $dateTo = $_GET['to'] ?? null;
    $page = max(1, intval($_GET['page'] ?? 1));
    $perPage = 30;
    $offset = ($page - 1) * $perPage;
    
    // Construction de la requête
    $where = [];
    $params = [];
    
    if ($filterAction) {
        $where[] = "a.action = ?";
        $params[] = $filterAction;
    }
    
    if ($filterUser) {
        $where[] = "a.user_id = ?";
        $params[] = $filterUser;
    }
    
    if ($dateFrom) {
        $where[] = "DATE(a.created_at) >= ?";
        $params[] = $dateFrom;
    }
    
    if ($dateTo) {
        $where[] = "DATE(a.created_at) <= ?";
        $params[] = $dateTo;
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Compter le total
    $countSql = "SELECT COUNT(*) FROM activity_log a $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalItems = (int) $countStmt->fetchColumn();
    $totalPages = max(1, (int) ceil($totalItems / $perPage));
    
    // Récupérer les activités
    $sql = "
        SELECT 
            a.id,
            a.user_id,
            a.action,
            a.entity_type,
            a.entity_id,
            a.description,
            a.metadata,
            a.ip_address,
            a.created_at,
            u.first_name,
            u.last_name,
            u.email
        FROM activity_log a
        LEFT JOIN users u ON a.user_id = u.id
        $whereClause
        ORDER BY a.created_at DESC
        LIMIT $perPage OFFSET $offset
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($debugMode) {
        $debugInfo .= "Activités trouvées: " . count($activities) . " | ";
    }
    
    // Récupérer la liste des utilisateurs pour le filtre
    $usersStmt = $pdo->query("
        SELECT DISTINCT u.id, u.first_name, u.last_name 
        FROM users u 
        INNER JOIN activity_log a ON a.user_id = u.id
        WHERE u.first_name IS NOT NULL AND u.first_name != ''
        ORDER BY u.first_name, u.last_name
    ");
    $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Pagination
    $pagination = [
        'current_page' => $page,
        'total_pages' => $totalPages,
        'total_items' => $totalItems,
        'per_page' => $perPage
    ];
    
} catch (Exception $e) {
    // Toujours afficher l'erreur
    $debugInfo .= "Erreur DB: " . $e->getMessage();
}

ob_start();
?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
}

.page-header-content h1 {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0 0 4px 0;
}

.page-header-content p {
    color: var(--text-secondary, #6B7280);
    font-size: 0.875rem;
    margin: 0;
}

.filters-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    padding: 16px;
    background: var(--bg-primary, white);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 12px;
    margin-bottom: 24px;
    align-items: center;
}

.filter-group {
    flex: 1;
    min-width: 150px;
}

.filter-select,
.filter-input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 8px;
    font-size: 0.875rem;
    background: var(--bg-primary, white);
}

.filter-select:focus,
.filter-input:focus {
    outline: none;
    border-color: var(--primary, #3B82F6);
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: all 0.15s;
}

.btn-secondary {
    background: var(--bg-secondary, #f3f4f6);
    color: var(--text-primary, #111827);
}

.btn-secondary:hover {
    background: var(--bg-tertiary, #e5e7eb);
}

.activity-timeline {
    position: relative;
}

.activity-date-group {
    margin-bottom: 24px;
}

.activity-date-header {
    display: inline-block;
    padding: 6px 14px;
    background: var(--bg-secondary, #f3f4f6);
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--text-secondary, #6B7280);
    margin-bottom: 16px;
}

.activity-item {
    display: flex;
    gap: 16px;
    padding: 16px;
    background: var(--bg-primary, white);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 12px;
    margin-bottom: 12px;
    transition: box-shadow 0.2s;
}

.activity-item:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.activity-icon.create { background: #D1FAE5; color: #059669; }
.activity-icon.update { background: #DBEAFE; color: #2563EB; }
.activity-icon.delete { background: #FEE2E2; color: #DC2626; }
.activity-icon.view { background: #E0E7FF; color: #4F46E5; }
.activity-icon.upload { background: #FEF3C7; color: #D97706; }
.activity-icon.download { background: #CFFAFE; color: #0891B2; }
.activity-icon.login { background: #D1FAE5; color: #059669; }
.activity-icon.logout { background: #F3F4F6; color: #6B7280; }

.activity-body {
    flex: 1;
    min-width: 0;
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 4px;
}

.activity-user {
    display: flex;
    align-items: center;
    gap: 8px;
}

.activity-avatar {
    width: 24px;
    height: 24px;
    background: var(--primary, #3B82F6);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    font-weight: 600;
}

.activity-user-name {
    font-weight: 600;
    color: var(--text-primary, #111827);
}

.activity-time {
    font-size: 0.8rem;
    color: var(--text-tertiary, #9CA3AF);
}

.activity-action {
    font-size: 0.9rem;
    color: var(--text-secondary, #6B7280);
    margin-bottom: 8px;
}

.activity-action strong {
    color: var(--text-primary, #111827);
}

.activity-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.activity-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    background: var(--bg-secondary, #f3f4f6);
    border-radius: 6px;
    font-size: 0.75rem;
    color: var(--text-secondary, #6B7280);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: var(--bg-primary, white);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 12px;
}

.empty-state svg {
    color: var(--text-tertiary, #9CA3AF);
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 8px 0;
}

.empty-state p {
    color: var(--text-secondary, #6B7280);
    margin: 0;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
}

.pagination a,
.pagination span {
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 0.875rem;
    text-decoration: none;
    color: var(--text-secondary, #6B7280);
    background: var(--bg-primary, white);
    border: 1px solid var(--border-color, #e5e7eb);
}

.pagination a:hover {
    background: var(--bg-secondary, #f3f4f6);
}

.pagination .active {
    background: var(--primary, #3B82F6);
    color: white;
    border-color: var(--primary, #3B82F6);
}

.activity-count {
    font-size: 0.85rem;
    color: var(--text-tertiary, #9CA3AF);
    margin-bottom: 16px;
}

.debug-box {
    background: #FEF3C7;
    border: 1px solid #F59E0B;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.8rem;
    color: #92400E;
}

@media (max-width: 768px) {
    .filters-bar {
        flex-direction: column;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .page-header {
        flex-direction: column;
        gap: 8px;
    }
}
</style>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= __('activity_log') ?></h1>
        <p><?= __('activity_log_subtitle') ?></p>
    </div>
</div>

<?php if ($debugInfo): ?>
<div class="debug-box">
    <strong>Debug:</strong> <?= htmlspecialchars($debugInfo) ?>
</div>
<?php endif; ?>

<!-- Filtres -->
<div class="filters-bar">
    <div class="filter-group">
        <select class="filter-select" id="actionFilter">
            <option value=""><?= __('all_actions') ?></option>
            <option value="create" <?= ($_GET['action'] ?? '') === 'create' ? 'selected' : '' ?>><?= __('action_create') ?></option>
            <option value="update" <?= ($_GET['action'] ?? '') === 'update' ? 'selected' : '' ?>><?= __('action_update') ?></option>
            <option value="delete" <?= ($_GET['action'] ?? '') === 'delete' ? 'selected' : '' ?>><?= __('action_delete') ?></option>
            <option value="view" <?= ($_GET['action'] ?? '') === 'view' ? 'selected' : '' ?>><?= __('action_view') ?></option>
            <option value="upload" <?= ($_GET['action'] ?? '') === 'upload' ? 'selected' : '' ?>><?= __('action_upload') ?></option>
            <option value="download" <?= ($_GET['action'] ?? '') === 'download' ? 'selected' : '' ?>><?= __('action_download') ?></option>
        </select>
    </div>
    
    <div class="filter-group">
        <select class="filter-select" id="userFilter">
            <option value=""><?= __('all_users') ?></option>
            <?php foreach ($users as $user): ?>
            <option value="<?= (int)$user['id'] ?>" <?= ($_GET['user'] ?? '') == $user['id'] ? 'selected' : '' ?>>
                <?= sanitize(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="filter-group">
        <input type="date" class="filter-input" id="dateFrom" value="<?= sanitize($_GET['from'] ?? '') ?>">
    </div>
    
    <div class="filter-group">
        <input type="date" class="filter-input" id="dateTo" value="<?= sanitize($_GET['to'] ?? '') ?>">
    </div>
    
    <button type="button" onclick="applyFilters()" class="btn btn-secondary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
        </svg>
        <?= __('filter') ?>
    </button>
</div>

<?php if ($pagination['total_items'] > 0): ?>
<p class="activity-count"><?= $pagination['total_items'] ?> activité(s)</p>
<?php endif; ?>

<!-- Timeline d'activités -->
<div class="activity-timeline">
    <?php 
    $actionIcons = [
        'create' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
        'update' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
        'delete' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>',
        'view' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
        'upload' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>',
        'download' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>',
        'login' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>',
        'logout' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
    ];
    
    $currentDate = '';
    
    if (!empty($activities)):
        foreach ($activities as $activity): 
            if (!is_array($activity)) continue;
            
            $createdAt = $activity['created_at'] ?? null;
            if (empty($createdAt)) continue;
            
            $activityDate = date('Y-m-d', strtotime($createdAt));
            
            if ($activityDate !== $currentDate):
                if ($currentDate !== '') echo '</div>';
                $currentDate = $activityDate;
                $displayDate = date('d F Y', strtotime($activityDate));
    ?>
    <div class="activity-date-group">
        <div class="activity-date-header"><?= $displayDate ?></div>
    <?php endif; ?>
        
        <?php
        $action = $activity['action'] ?? 'view';
        $firstName = $activity['first_name'] ?? '';
        $lastName = $activity['last_name'] ?? '';
        $description = $activity['description'] ?? '';
        $metadataJson = $activity['metadata'] ?? null;
        ?>
        
        <div class="activity-item">
            <div class="activity-icon <?= htmlspecialchars($action) ?>">
                <?= $actionIcons[$action] ?? $actionIcons['view'] ?>
            </div>
            
            <div class="activity-body">
                <div class="activity-header">
                    <div class="activity-user">
                        <?php if (!empty($firstName)): ?>
                        <div class="activity-avatar">
                            <?= strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1)) ?>
                        </div>
                        <span class="activity-user-name"><?= sanitize($firstName . ' ' . $lastName) ?></span>
                        <?php else: ?>
                        <span class="activity-user-name"><?= __('system') ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="activity-time"><?= date('H:i', strtotime($createdAt)) ?></span>
                </div>
                
                <div class="activity-action">
                    <strong><?= __('action_' . $action) ?></strong>
                    <?php if (!empty($description)): ?>
                    - <?= sanitize($description) ?>
                    <?php endif; ?>
                </div>
                
                <?php 
                $metadata = null;
                if (!empty($metadataJson) && is_string($metadataJson)) {
                    $metadata = @json_decode($metadataJson, true);
                }
                
                if (!empty($metadata) && is_array($metadata)): 
                ?>
                <div class="activity-meta">
                    <?php if (!empty($metadata['entity_type'])): ?>
                    <span class="activity-tag"><?= sanitize(ucfirst($metadata['entity_type'])) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($metadata['entity_name'])): ?>
                    <span class="activity-tag"><?= sanitize($metadata['entity_name']) ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
    <?php 
        endforeach;
        echo '</div>';
    else: 
    ?>
    <div class="empty-state">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
        </svg>
        <h3><?= __('no_recent_activity') ?></h3>
        <p><?= __('activity_log_subtitle') ?></p>
    </div>
    <?php endif; ?>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
<div class="pagination">
    <?php $cp = $pagination['current_page']; $tp = $pagination['total_pages']; ?>
    
    <?php if ($cp > 1): ?>
    <a href="?page=<?= $cp - 1 ?><?= isset($_GET['action']) ? '&action=' . urlencode($_GET['action']) : '' ?><?= isset($_GET['user']) ? '&user=' . urlencode($_GET['user']) : '' ?>">&laquo;</a>
    <?php endif; ?>
    
    <?php for ($i = max(1, $cp - 2); $i <= min($tp, $cp + 2); $i++): ?>
    <a href="?page=<?= $i ?><?= isset($_GET['action']) ? '&action=' . urlencode($_GET['action']) : '' ?><?= isset($_GET['user']) ? '&user=' . urlencode($_GET['user']) : '' ?>" class="<?= $i === $cp ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    
    <?php if ($cp < $tp): ?>
    <a href="?page=<?= $cp + 1 ?><?= isset($_GET['action']) ? '&action=' . urlencode($_GET['action']) : '' ?><?= isset($_GET['user']) ? '&user=' . urlencode($_GET['user']) : '' ?>">&raquo;</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<script>
function applyFilters() {
    const params = new URLSearchParams();
    
    const action = document.getElementById('actionFilter').value;
    const user = document.getElementById('userFilter').value;
    const from = document.getElementById('dateFrom').value;
    const to = document.getElementById('dateTo').value;
    
    if (action) params.set('action', action);
    if (user) params.set('user', user);
    if (from) params.set('from', from);
    if (to) params.set('to', to);
    
    window.location.href = '/activity?' + params.toString();
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
