<?php
/**
 * DocuFlow - Helper de traduction pour les activités
 * 
 * Traduit dynamiquement les descriptions d'activité stockées en base de données
 */

/**
 * Traduit une description d'activité en fonction de la langue active
 * 
 * @param string $action Le code action (login, upload, create_link, etc.)
 * @param string|null $description La description originale en français
 * @param array $metadata Données additionnelles (document, user, etc.)
 * @return string La description traduite
 */
function translateActivity(string $action, ?string $description = null, array $metadata = []): string {
    // Mapping des actions vers les clés de traduction
    $actionKeys = [
        'login' => 'activity_login',
        'logout' => 'activity_logout',
        'upload' => 'activity_upload',
        'delete' => 'activity_delete',
        'create_link' => 'activity_create_link',
        'delete_link' => 'activity_delete_link',
        'create_zone' => 'activity_create_zone',
        'delete_zone' => 'activity_delete_zone',
        'create_annotation' => 'activity_create_annotation',
        'delete_annotation' => 'activity_delete_annotation',
        'resolve_annotation' => 'activity_resolve_annotation',
        'update_profile' => 'activity_update_profile',
        'create_user' => 'activity_create_user',
        'update_user' => 'activity_update_user',
        'delete_user' => 'activity_delete_user',
        'create_team' => 'activity_create_team',
        'update_team' => 'activity_update_team',
        'delete_team' => 'activity_delete_team',
        'create_role' => 'activity_create_role',
        'update_role' => 'activity_update_role',
        'delete_role' => 'activity_delete_role',
        'view_document' => 'activity_view_document',
        'download_document' => 'activity_download_document',
        'share_document' => 'activity_share_document',
        'mention_user' => 'activity_mention_user',
    ];
    
    // Si l'action est connue, utiliser la clé de traduction
    if (isset($actionKeys[$action])) {
        return __($actionKeys[$action]);
    }
    
    // Sinon, essayer de traduire la description française
    return translateFrenchDescription($description ?? $action);
}

/**
 * Traduit une description française stockée en base
 * vers la langue active
 * 
 * @param string $frenchDescription Description en français
 * @return string Description traduite
 */
function translateFrenchDescription(string $frenchDescription): string {
    $lang = getCurrentLang();
    
    // Si la langue est le français, retourner tel quel
    if ($lang === 'fr') {
        return $frenchDescription;
    }
    
    // Mapping des descriptions françaises vers les clés de traduction
    $frenchToKeys = [
        // Connexion / Déconnexion
        'Connexion' => 'activity_login',
        'Déconnexion' => 'activity_logout',
        
        // Documents
        'Upload de document' => 'activity_upload',
        'Import de document' => 'activity_upload',
        'Suppression' => 'activity_delete',
        'Suppression de document' => 'activity_delete',
        'Consultation de document' => 'activity_view_document',
        'Téléchargement de document' => 'activity_download_document',
        'Partage de document' => 'activity_share_document',
        
        // Zones
        'Création de zone' => 'activity_create_zone',
        'Suppression de zone' => 'activity_delete_zone',
        
        // Annotations
        'Ajout d\'annotation' => 'activity_create_annotation',
        'Suppression d\'annotation' => 'activity_delete_annotation',
        'Résolution d\'annotation' => 'activity_resolve_annotation',
        
        // Liaisons
        'Création de liaison' => 'activity_create_link',
        'Suppression de liaison' => 'activity_delete_link',
        
        // Profil
        'Modification du profil' => 'activity_update_profile',
        
        // Utilisateurs
        'Création d\'utilisateur' => 'activity_create_user',
        'Modification d\'utilisateur' => 'activity_update_user',
        'Suppression d\'utilisateur' => 'activity_delete_user',
        
        // Équipes
        'Création d\'équipe' => 'activity_create_team',
        'Modification d\'équipe' => 'activity_update_team',
        'Suppression d\'équipe' => 'activity_delete_team',
        
        // Rôles
        'Création de rôle' => 'activity_create_role',
        'Modification de rôle' => 'activity_update_role',
        'Suppression de rôle' => 'activity_delete_role',
        
        // Mentions
        'Mention d\'utilisateur' => 'activity_mention_user',
    ];
    
    // Chercher une correspondance exacte
    if (isset($frenchToKeys[$frenchDescription])) {
        return __($frenchToKeys[$frenchDescription]);
    }
    
    // Chercher une correspondance partielle avec regex
    $patterns = [
        '/^Connexion/i' => 'activity_login',
        '/^Déconnexion/i' => 'activity_logout',
        '/^Upload|^Import/i' => 'activity_upload',
        '/^Suppression de document/i' => 'activity_delete',
        '/^Suppression de zone/i' => 'activity_delete_zone',
        '/^Suppression de liaison/i' => 'activity_delete_link',
        '/^Suppression d\'annotation/i' => 'activity_delete_annotation',
        '/^Suppression d\'utilisateur/i' => 'activity_delete_user',
        '/^Suppression d\'équipe/i' => 'activity_delete_team',
        '/^Suppression de rôle/i' => 'activity_delete_role',
        '/^Suppression/i' => 'activity_delete',
        '/^Création de zone/i' => 'activity_create_zone',
        '/^Création de liaison/i' => 'activity_create_link',
        '/^Création d\'équipe/i' => 'activity_create_team',
        '/^Création d\'utilisateur/i' => 'activity_create_user',
        '/^Création de rôle/i' => 'activity_create_role',
        '/^Ajout d\'annotation/i' => 'activity_create_annotation',
        '/^Résolution d\'annotation/i' => 'activity_resolve_annotation',
        '/^Modification du profil/i' => 'activity_update_profile',
        '/^Modification d\'utilisateur/i' => 'activity_update_user',
        '/^Modification d\'équipe/i' => 'activity_update_team',
        '/^Modification de rôle/i' => 'activity_update_role',
        '/^Consultation/i' => 'activity_view_document',
        '/^Téléchargement/i' => 'activity_download_document',
        '/^Partage/i' => 'activity_share_document',
        '/^Mention/i' => 'activity_mention_user',
    ];
    
    foreach ($patterns as $pattern => $translationKey) {
        if (preg_match($pattern, $frenchDescription)) {
            return __($translationKey);
        }
    }
    
    // Si aucune correspondance, retourner la description originale
    return $frenchDescription;
}

/**
 * Obtient l'icône correspondant à une action
 * 
 * @param string $action Code de l'action
 * @return string Emoji ou icône SVG
 */
function getActivityIcon(string $action): string {
    $icons = [
        'login' => '🔐',
        'logout' => '🚪',
        'upload' => '📤',
        'delete' => '🗑️',
        'create_link' => '🔗',
        'delete_link' => '🔗',
        'create_zone' => '📍',
        'delete_zone' => '📍',
        'create_annotation' => '💬',
        'delete_annotation' => '💬',
        'resolve_annotation' => '✅',
        'update_profile' => '👤',
        'create_user' => '👥',
        'update_user' => '👤',
        'delete_user' => '👤',
        'create_team' => '🏢',
        'update_team' => '🏢',
        'delete_team' => '🏢',
        'create_role' => '🛡️',
        'update_role' => '🛡️',
        'delete_role' => '🛡️',
        'view_document' => '👁️',
        'download_document' => '📥',
        'share_document' => '📤',
        'mention_user' => '@',
    ];
    
    return $icons[$action] ?? '📋';
}

/**
 * Obtient la couleur correspondant à une action
 * 
 * @param string $action Code de l'action
 * @return string Classe CSS ou code couleur
 */
function getActivityColor(string $action): string {
    $colors = [
        'login' => 'success',
        'logout' => 'info',
        'upload' => 'primary',
        'delete' => 'danger',
        'create_link' => 'info',
        'delete_link' => 'danger',
        'create_zone' => 'success',
        'delete_zone' => 'danger',
        'create_annotation' => 'warning',
        'delete_annotation' => 'danger',
        'resolve_annotation' => 'success',
        'update_profile' => 'info',
        'create_user' => 'success',
        'update_user' => 'info',
        'delete_user' => 'danger',
        'create_team' => 'success',
        'update_team' => 'info',
        'delete_team' => 'danger',
        'create_role' => 'success',
        'update_role' => 'info',
        'delete_role' => 'danger',
        'view_document' => 'secondary',
        'download_document' => 'primary',
        'share_document' => 'info',
        'mention_user' => 'warning',
    ];
    
    return $colors[$action] ?? 'secondary';
}

/**
 * Formate une activité complète pour l'affichage
 * 
 * @param array $activity L'entrée d'activité de la base de données
 * @return array Données formatées pour l'affichage
 */
function formatActivityForDisplay(array $activity): array {
    $action = $activity['action'] ?? '';
    $description = $activity['description'] ?? '';
    $metadata = !empty($activity['metadata']) ? json_decode($activity['metadata'], true) : [];
    
    return [
        'id' => $activity['id'] ?? 0,
        'user_id' => $activity['user_id'] ?? 0,
        'user_name' => trim(($activity['first_name'] ?? '') . ' ' . ($activity['last_name'] ?? '')),
        'avatar' => $activity['avatar'] ?? null,
        'action' => $action,
        'description' => translateActivity($action, $description, $metadata),
        'original_description' => $description,
        'icon' => getActivityIcon($action),
        'color' => getActivityColor($action),
        'entity_type' => $activity['entity_type'] ?? null,
        'entity_id' => $activity['entity_id'] ?? null,
        'metadata' => $metadata,
        'created_at' => $activity['created_at'] ?? null,
        'time_ago' => isset($activity['created_at']) ? timeAgo($activity['created_at']) : '',
    ];
}

/**
 * Calcule le temps écoulé en format lisible
 * 
 * @param string $datetime Date/heure au format SQL
 * @return string Temps écoulé (ex: "Il y a 5 min")
 */
function timeAgo(string $datetime): string {
    $now = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);
    
    if ($diff->y > 0) {
        return __('time_years', ['count' => $diff->y]);
    }
    if ($diff->m > 0) {
        return __('time_months', ['count' => $diff->m]);
    }
    if ($diff->d > 0) {
        if ($diff->d === 1) {
            return __('yesterday');
        }
        return __('time_days', ['count' => $diff->d]);
    }
    if ($diff->h > 0) {
        return __('time_hours', ['count' => $diff->h]);
    }
    if ($diff->i > 0) {
        return __('time_minutes', ['count' => $diff->i]);
    }
    
    return __('time_now');
}

/**
 * Obtenir la langue courante
 */
if (!function_exists('getCurrentLang')) {
    function getCurrentLang(): string {
        return $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'fr';
    }
}
