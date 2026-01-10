<?php
/**
 * DocuFlow - Helpers pour la traduction des activités
 * Fichier: htdocs/src/Config/activity-helpers.php
 * 
 * Traduit dynamiquement les descriptions d'activité stockées en français
 * vers la langue courante de l'utilisateur
 */

/**
 * Traduit une description d'activité
 * 
 * @param string $description La description en français depuis la BDD
 * @return string La description traduite
 */
function translateActivityDescription(string $description): string {
    // Patterns de traduction FR -> clé
    $patterns = [
        // Upload de document
        '/^Upload du document:\s*(.+)$/i' => 'activity_upload_document',
        '/^Upload de document:\s*(.+)$/i' => 'activity_upload_document',
        
        // Suppression de document
        '/^Suppression du document:\s*(.+)$/i' => 'activity_delete_document',
        '/^Suppression de document:\s*(.+)$/i' => 'activity_delete_document',
        
        // Modification de document
        '/^Modification du document$/i' => 'activity_update_document',
        '/^Modification du document:\s*(.+)$/i' => 'activity_update_document_name',
        
        // Zones
        '/^Création de zone sur le document #(\d+)$/i' => 'activity_create_zone',
        '/^Modification de zone$/i' => 'activity_update_zone',
        '/^Suppression de zone$/i' => 'activity_delete_zone',
        
        // Liaisons
        '/^Création de liaison$/i' => 'activity_create_link',
        '/^Suppression de liaison$/i' => 'activity_delete_link',
        
        // Annotations
        '/^Ajout d\'annotation$/i' => 'activity_create_annotation',
        '/^Résolution d\'annotation$/i' => 'activity_resolve_annotation',
        '/^Suppression d\'annotation$/i' => 'activity_delete_annotation',
        
        // Authentification
        '/^Connexion réussie$/i' => 'activity_login_success',
        '/^Déconnexion$/i' => 'activity_logout',
        
        // Utilisateurs
        '/^Création d\'utilisateur:\s*(.+)$/i' => 'activity_create_user',
        '/^Modification d\'utilisateur$/i' => 'activity_update_user',
        '/^Désactivation d\'utilisateur$/i' => 'activity_deactivate_user',
        
        // Équipes
        '/^Création d\'équipe:\s*(.+)$/i' => 'activity_create_team',
        '/^Modification d\'équipe$/i' => 'activity_update_team',
        '/^Suppression d\'équipe$/i' => 'activity_delete_team',
        
        // Mentions
        '/^Mention envoyée à l\'utilisateur #(\d+)$/i' => 'activity_mention_sent',
    ];
    
    foreach ($patterns as $pattern => $translationKey) {
        if (preg_match($pattern, $description, $matches)) {
            // Récupérer la traduction
            $translated = __($translationKey);
            
            // Si la traduction existe et contient un placeholder
            if ($translated !== $translationKey) {
                // Remplacer :name ou :id par la valeur capturée
                if (isset($matches[1])) {
                    $translated = str_replace([':name', ':id'], $matches[1], $translated);
                }
                return $translated;
            }
        }
    }
    
    // Aucun pattern ne correspond, retourner la description originale
    return $description;
}

/**
 * Traduit une action d'activité
 * 
 * @param string $action Le code de l'action
 * @return string L'action traduite
 */
function translateActivityAction(string $action): string {
    $key = 'activity_' . $action;
    $translated = __($key);
    
    // Si pas de traduction, essayer avec action_
    if ($translated === $key) {
        $key = 'action_' . $action;
        $translated = __($key);
    }
    
    // Si toujours pas de traduction, retourner l'action formatée
    if ($translated === $key) {
        return ucfirst(str_replace('_', ' ', $action));
    }
    
    return $translated;
}

/**
 * Traduit un type d'entité
 * 
 * @param string $entityType Le type d'entité
 * @return string Le type traduit
 */
function translateEntityType(string $entityType): string {
    $key = 'entity_' . $entityType;
    $translated = __($key);
    
    if ($translated === $key) {
        return ucfirst($entityType);
    }
    
    return $translated;
}
