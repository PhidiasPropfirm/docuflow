<?php
/**
 * Modal de réinitialisation complète
 * Fichier: htdocs/src/Views/components/reset-modal.php
 * 
 * Accessible à tous les utilisateurs connectés
 */
?>

<!-- Modal de confirmation de réinitialisation -->
<div class="modal-overlay" id="resetModal">
    <div class="modal reset-modal">
        <div class="modal-header danger">
            <div class="modal-icon danger">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <h2><?= __('reset_all_title') ?></h2>
            <button type="button" class="modal-close" onclick="closeResetModal()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="warning-box critical">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div>
                    <strong><?= __('reset_warning_title') ?></strong>
                    <p><?= __('reset_warning_irreversible') ?></p>
                </div>
            </div>
            
            <div class="reset-info">
                <h3><?= __('reset_what_deleted') ?></h3>
                <ul class="reset-list">
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                        <span><strong><?= __('documents') ?></strong> - <?= __('reset_all_documents') ?></span>
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <rect x="7" y="7" width="3" height="3"/>
                        </svg>
                        <span><strong><?= __('zones') ?></strong> - <?= __('reset_all_zones') ?></span>
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <span><strong><?= __('annotations') ?></strong> - <?= __('reset_all_annotations') ?></span>
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                        </svg>
                        <span><strong><?= __('links') ?></strong> - <?= __('reset_all_links') ?></span>
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                        <span><strong><?= __('activity') ?></strong> - <?= __('reset_all_activity') ?></span>
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                        </svg>
                        <span><strong><?= __('chat') ?></strong> - <?= __('reset_all_chat') ?></span>
                    </li>
                </ul>
            </div>
            
            <div class="info-box">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="16" x2="12" y2="12"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <div>
                    <strong><?= __('reset_when_use') ?></strong>
                    <p><?= __('reset_when_use_desc') ?></p>
                </div>
            </div>
            
            <div class="warning-box">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <div>
                    <strong><?= __('reset_all_users_warning') ?></strong>
                    <p><?= __('reset_all_users_desc') ?></p>
                </div>
            </div>
            
            <div class="confirm-input">
                <label for="confirmCode">
                    <?= __('reset_confirm_label') ?>
                    <code>ERASE-ALL</code>
                </label>
                <input type="text" 
                       id="confirmCode" 
                       placeholder="<?= __('reset_confirm_placeholder') ?>"
                       autocomplete="off"
                       spellcheck="false">
            </div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeResetModal()">
                <?= __('cancel') ?>
            </button>
            <form action="/admin/reset-all" method="POST" id="resetForm">
                <?= csrf_field() ?>
                <input type="hidden" name="confirm_code" id="confirmCodeHidden">
                <button type="submit" class="btn btn-danger" id="resetSubmitBtn" disabled>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                    <?= __('reset_confirm_btn') ?>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
/* Bouton Tout supprimer */
.reset-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: transparent;
    border: 1px solid #DC2626;
    color: #DC2626;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.reset-all-btn:hover {
    background: #DC2626;
    color: white;
}

/* Modal overlay */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
}

.modal-overlay.active {
    opacity: 1;
    visibility: visible;
}

/* Modal Reset */
.reset-modal {
    background: var(--bg-primary, white);
    border-radius: 16px;
    width: 100%;
    max-width: 560px;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transform: scale(0.9);
    transition: transform 0.3s;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.modal-overlay.active .reset-modal {
    transform: scale(1);
}

/* Header */
.modal-header {
    padding: 24px;
    border-bottom: 1px solid var(--border-color, #e5e7eb);
    display: flex;
    align-items: center;
    gap: 16px;
    position: relative;
}

.modal-header.danger {
    background: linear-gradient(135deg, #FEF2F2, #FEE2E2);
}

.modal-icon {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.modal-icon.danger {
    background: #DC2626;
    color: white;
}

.modal-header h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #991B1B;
    margin: 0;
}

.modal-close {
    position: absolute;
    top: 16px;
    right: 16px;
    background: transparent;
    border: none;
    cursor: pointer;
    color: var(--text-secondary, #6B7280);
    padding: 4px;
    border-radius: 6px;
    transition: all 0.2s;
}

.modal-close:hover {
    background: var(--bg-secondary, #f3f4f6);
    color: var(--text-primary, #111827);
}

/* Body */
.modal-body {
    padding: 24px;
    overflow-y: auto;
    flex: 1;
}

/* Warning boxes */
.warning-box {
    display: flex;
    gap: 12px;
    padding: 16px;
    background: #FFFBEB;
    border: 1px solid #FCD34D;
    border-radius: 10px;
    margin-bottom: 20px;
}

.warning-box.critical {
    background: #FEF2F2;
    border-color: #FECACA;
}

.warning-box svg {
    flex-shrink: 0;
    color: #D97706;
}

.warning-box.critical svg {
    color: #DC2626;
}

.warning-box strong {
    display: block;
    color: #92400E;
    font-size: 0.9rem;
    margin-bottom: 4px;
}

.warning-box.critical strong {
    color: #991B1B;
}

.warning-box p {
    font-size: 0.85rem;
    color: #78350F;
    margin: 0;
    line-height: 1.5;
}

.warning-box.critical p {
    color: #7F1D1D;
}

/* Info box */
.info-box {
    display: flex;
    gap: 12px;
    padding: 16px;
    background: #EFF6FF;
    border: 1px solid #BFDBFE;
    border-radius: 10px;
    margin-bottom: 20px;
}

.info-box svg {
    flex-shrink: 0;
    color: #2563EB;
}

.info-box strong {
    display: block;
    color: #1E40AF;
    font-size: 0.9rem;
    margin-bottom: 4px;
}

.info-box p {
    font-size: 0.85rem;
    color: #1E3A8A;
    margin: 0;
    line-height: 1.5;
}

/* Reset info */
.reset-info {
    margin-bottom: 20px;
}

.reset-info h3 {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary, #111827);
    margin: 0 0 12px;
}

.reset-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    gap: 10px;
}

.reset-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    background: var(--bg-secondary, #f9fafb);
    border-radius: 8px;
    font-size: 0.875rem;
    color: var(--text-secondary, #6B7280);
}

.reset-list li svg {
    color: #DC2626;
    flex-shrink: 0;
}

.reset-list li strong {
    color: var(--text-primary, #111827);
}

/* Confirm input */
.confirm-input {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color, #e5e7eb);
}

.confirm-input label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary, #111827);
    margin-bottom: 8px;
}

.confirm-input code {
    background: #FEE2E2;
    color: #DC2626;
    padding: 2px 8px;
    border-radius: 4px;
    font-family: monospace;
    font-weight: 600;
}

.confirm-input input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border-color, #e5e7eb);
    border-radius: 8px;
    font-size: 1rem;
    font-family: monospace;
    text-transform: uppercase;
    letter-spacing: 2px;
    text-align: center;
    transition: all 0.2s;
}

.confirm-input input:focus {
    outline: none;
    border-color: #DC2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.confirm-input input.valid {
    border-color: #16A34A;
    background: #F0FDF4;
}

/* Footer */
.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--border-color, #e5e7eb);
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    background: var(--bg-secondary, #f9fafb);
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
}

.btn-secondary {
    background: var(--bg-primary, white);
    color: var(--text-primary, #111827);
    border: 1px solid var(--border-color, #e5e7eb);
}

.btn-secondary:hover {
    background: var(--bg-secondary, #f3f4f6);
}

.btn-danger {
    background: #DC2626;
    color: white;
}

.btn-danger:hover:not(:disabled) {
    background: #B91C1C;
}

.btn-danger:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

@media (max-width: 600px) {
    .reset-modal {
        margin: 16px;
        max-height: calc(100vh - 32px);
    }
    
    .modal-header {
        padding: 16px;
    }
    
    .modal-body {
        padding: 16px;
    }
    
    .modal-footer {
        flex-direction: column;
    }
    
    .modal-footer .btn {
        width: 100%;
    }
}
</style>

<script>
// Ouvrir le modal
function openResetModal() {
    document.getElementById('resetModal').classList.add('active');
    document.getElementById('confirmCode').value = '';
    document.getElementById('confirmCode').classList.remove('valid');
    document.getElementById('resetSubmitBtn').disabled = true;
    document.body.style.overflow = 'hidden';
}

// Fermer le modal
function closeResetModal() {
    document.getElementById('resetModal').classList.remove('active');
    document.body.style.overflow = '';
}

// Vérifier le code de confirmation
document.getElementById('confirmCode').addEventListener('input', function(e) {
    const value = e.target.value.toUpperCase();
    const isValid = value === 'ERASE-ALL';
    
    e.target.classList.toggle('valid', isValid);
    document.getElementById('resetSubmitBtn').disabled = !isValid;
    document.getElementById('confirmCodeHidden').value = value;
});

// Soumettre le formulaire
document.getElementById('resetForm').addEventListener('submit', function(e) {
    const confirmCode = document.getElementById('confirmCode').value.toUpperCase();
    
    if (confirmCode !== 'ERASE-ALL') {
        e.preventDefault();
        alert('<?= __('reset_invalid_code') ?>');
        return false;
    }
    
    // Dernière confirmation
    if (!confirm('<?= __('reset_final_confirm') ?>')) {
        e.preventDefault();
        return false;
    }
});

// Fermer avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeResetModal();
    }
});

// Fermer en cliquant sur l'overlay
document.getElementById('resetModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeResetModal();
    }
});
</script>
