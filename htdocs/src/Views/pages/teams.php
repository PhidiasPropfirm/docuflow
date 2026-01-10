<?php
$pageTitle = __('teams_title');
ob_start();

// Fonction helper pour obtenir le nom traduit
function getTeamName($team) {
    $lang = currentLang();
    if ($lang === 'en' && !empty($team['name_en'])) {
        return $team['name_en'];
    }
    return $team['name'];
}

function getTeamDescription($team) {
    $lang = currentLang();
    if ($lang === 'en' && !empty($team['description_en'])) {
        return $team['description_en'];
    }
    return $team['description'] ?? '';
}
?>

<style>
/* Teams Page Styles */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
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

.teams-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
}

.team-card {
    background: var(--bg-primary, white);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 12px;
    padding: 20px;
    transition: box-shadow 0.2s, transform 0.2s;
}

.team-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.team-card-header {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 16px;
}

.team-color-indicator {
    width: 4px;
    height: 40px;
    border-radius: 2px;
    flex-shrink: 0;
}

.team-card-title {
    flex: 1;
    min-width: 0;
}

.team-card-title h3 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 4px 0;
    color: var(--text-primary, #111827);
}

.team-card-title p {
    font-size: 0.8rem;
    color: var(--text-secondary, #6B7280);
    margin: 0;
    line-height: 1.4;
}

.team-card-actions {
    flex-shrink: 0;
}

.team-card-stats {
    display: flex;
    gap: 24px;
    padding: 16px 0;
    border-bottom: 1px solid var(--border-color, #e5e7eb);
    margin-bottom: 16px;
}

.team-stat {
    text-align: center;
}

.team-stat .stat-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary, #111827);
}

.team-stat .stat-label {
    font-size: 0.7rem;
    color: var(--text-secondary, #6B7280);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.team-card-members h4 {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-secondary, #6B7280);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 0 0 12px 0;
}

.no-members {
    font-size: 0.85rem;
    color: var(--text-tertiary, #9CA3AF);
    font-style: italic;
    margin: 0;
}

.members-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.member-item {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--bg-secondary, #f3f4f6);
    padding: 4px 10px 4px 4px;
    border-radius: 20px;
    font-size: 0.8rem;
}

.avatar-sm {
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

.member-more {
    background: var(--bg-tertiary, #e5e7eb);
    color: var(--text-secondary, #6B7280);
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    grid-column: 1 / -1;
}

.empty-state.large svg {
    color: var(--text-tertiary, #9CA3AF);
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 8px 0;
    color: var(--text-primary, #111827);
}

.empty-state p {
    color: var(--text-secondary, #6B7280);
    margin: 0 0 20px 0;
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal.open {
    display: flex;
}

.modal-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
}

.modal-content {
    position: relative;
    background: var(--bg-primary, white);
    border-radius: 16px;
    width: 100%;
    max-width: 500px;
    max-height: 90vh;
    overflow: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color, #e5e7eb);
}

.modal-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
}

.modal-close {
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-secondary, #6B7280);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    background: var(--bg-secondary, #f3f4f6);
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 16px 24px;
    border-top: 1px solid var(--border-color, #e5e7eb);
}

/* Form styles */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 8px;
    color: var(--text-primary, #111827);
}

.form-group input[type="text"],
.form-group textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 8px;
    font-size: 0.9rem;
    transition: border-color 0.15s;
    box-sizing: border-box;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary, #3B82F6);
}

.color-picker-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
}

.color-picker-wrapper input[type="color"] {
    width: 50px;
    height: 40px;
    padding: 0;
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 8px;
    cursor: pointer;
}

.color-value {
    font-family: monospace;
    font-size: 0.9rem;
    color: var(--text-secondary, #6B7280);
}

.form-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.form-hint {
    font-size: 0.75rem;
    color: var(--text-tertiary, #9CA3AF);
    margin-top: 4px;
}

/* Buttons */
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

.btn-primary {
    background: var(--primary, #3B82F6);
    color: white;
}

.btn-primary:hover {
    background: #2563EB;
}

.btn-secondary {
    background: var(--bg-secondary, #f3f4f6);
    color: var(--text-primary, #111827);
}

.btn-secondary:hover {
    background: var(--bg-tertiary, #e5e7eb);
}

.btn-ghost {
    background: transparent;
    color: var(--text-secondary, #6B7280);
    padding: 8px;
}

.btn-ghost:hover {
    background: var(--bg-secondary, #f3f4f6);
    color: var(--text-primary, #111827);
}

.btn-xs {
    padding: 6px 10px;
    font-size: 0.8rem;
}

@media (max-width: 768px) {
    .teams-grid {
        grid-template-columns: 1fr;
    }
    
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    
    .form-row-2 {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= __('teams_title') ?></h1>
        <p><?= __('teams_count', ['count' => count($teams)]) ?></p>
    </div>
    <button onclick="openTeamModal()" class="btn btn-primary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <line x1="19" y1="8" x2="19" y2="14"/>
            <line x1="22" y1="11" x2="16" y2="11"/>
        </svg>
        <?= __('new_team') ?>
    </button>
</div>

<!-- Liste des équipes -->
<div class="teams-grid">
    <?php foreach ($teams as $team): ?>
    <div class="team-card">
        <div class="team-card-header">
            <div class="team-color-indicator" style="background-color: <?= $team['color'] ?? '#3B82F6' ?>"></div>
            <div class="team-card-title">
                <h3><?= sanitize(getTeamName($team)) ?></h3>
                <?php $desc = getTeamDescription($team); if (!empty($desc)): ?>
                <p><?= sanitize($desc) ?></p>
                <?php endif; ?>
            </div>
            <div class="team-card-actions">
                <button onclick="editTeam(<?= htmlspecialchars(json_encode($team), ENT_QUOTES) ?>)" class="btn btn-ghost btn-xs" title="<?= __('edit') ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="team-card-stats">
            <div class="team-stat">
                <span class="stat-value"><?= $team['member_count'] ?? 0 ?></span>
                <span class="stat-label"><?= __('team_members_label') ?></span>
            </div>
            <div class="team-stat">
                <span class="stat-value"><?= $team['document_count'] ?? 0 ?></span>
                <span class="stat-label"><?= __('team_documents_label') ?></span>
            </div>
        </div>
        
        <div class="team-card-members">
            <h4><?= __('members_label') ?></h4>
            <?php if (empty($team['members'])): ?>
            <p class="no-members"><?= __('no_members_in_team') ?></p>
            <?php else: ?>
            <div class="members-list">
                <?php foreach (array_slice($team['members'], 0, 5) as $member): ?>
                <div class="member-item" title="<?= sanitize($member['first_name'] . ' ' . $member['last_name']) ?>">
                    <div class="avatar-sm">
                        <?= strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)) ?>
                    </div>
                    <span><?= sanitize($member['first_name']) ?></span>
                </div>
                <?php endforeach; ?>
                <?php if (count($team['members']) > 5): ?>
                <div class="member-more">+<?= count($team['members']) - 5 ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if (empty($teams)): ?>
    <div class="empty-state large">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        <h3><?= __('no_team') ?></h3>
        <p><?= __('create_first_team') ?></p>
        <button onclick="openTeamModal()" class="btn btn-primary"><?= __('new_team') ?></button>
    </div>
    <?php endif; ?>
</div>

<!-- Modal équipe -->
<div class="modal" id="teamModal">
    <div class="modal-overlay" onclick="closeTeamModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="teamModalTitle"><?= __('new_team') ?></h3>
            <button onclick="closeTeamModal()" class="modal-close">&times;</button>
        </div>
        <form action="/teams" method="POST" id="teamForm">
            <?= csrf_field() ?>
            <input type="hidden" name="team_id" id="teamId">
            
            <div class="modal-body">
                <!-- Nom FR / EN -->
                <div class="form-row-2">
                    <div class="form-group">
                        <label for="teamName"><?= __('team_name') ?> (FR)</label>
                        <input type="text" id="teamName" name="name" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="teamNameEn"><?= __('team_name') ?> (EN)</label>
                        <input type="text" id="teamNameEn" name="name_en" maxlength="100">
                    </div>
                </div>
                
                <!-- Description FR -->
                <div class="form-group">
                    <label for="teamDescription"><?= __('team_description') ?> (FR)</label>
                    <textarea id="teamDescription" name="description" rows="2" maxlength="500"></textarea>
                </div>
                
                <!-- Description EN -->
                <div class="form-group">
                    <label for="teamDescriptionEn"><?= __('team_description') ?> (EN)</label>
                    <textarea id="teamDescriptionEn" name="description_en" rows="2" maxlength="500"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="teamColor"><?= __('team_color') ?></label>
                    <div class="color-picker-wrapper">
                        <input type="color" id="teamColor" name="color" value="#3B82F6">
                        <span class="color-value">#3B82F6</span>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="closeTeamModal()" class="btn btn-secondary"><?= __('cancel') ?></button>
                <button type="submit" class="btn btn-primary"><?= __('save') ?></button>
            </div>
        </form>
    </div>
</div>

<script>
const TRANSLATIONS = {
    new_team: '<?= __('new_team') ?>',
    edit_team: '<?= __('edit_team') ?>',
    delete_team_confirm: '<?= __('delete_team_confirm') ?>'
};

function openTeamModal() {
    document.getElementById('teamModal').classList.add('open');
    document.getElementById('teamModalTitle').textContent = TRANSLATIONS.new_team;
    document.getElementById('teamForm').reset();
    document.getElementById('teamId').value = '';
    document.querySelector('.color-value').textContent = '#3B82F6';
}

function closeTeamModal() {
    document.getElementById('teamModal').classList.remove('open');
}

function editTeam(team) {
    document.getElementById('teamModal').classList.add('open');
    document.getElementById('teamModalTitle').textContent = TRANSLATIONS.edit_team;
    document.getElementById('teamId').value = team.id;
    document.getElementById('teamName').value = team.name || '';
    document.getElementById('teamNameEn').value = team.name_en || '';
    document.getElementById('teamDescription').value = team.description || '';
    document.getElementById('teamDescriptionEn').value = team.description_en || '';
    document.getElementById('teamColor').value = team.color || '#3B82F6';
    document.querySelector('.color-value').textContent = team.color || '#3B82F6';
}

// Color picker update
document.getElementById('teamColor').addEventListener('input', function() {
    document.querySelector('.color-value').textContent = this.value;
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
