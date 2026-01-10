<?php
/**
 * DocuFlow - Fichier Helpers
 * Fonctions utilitaires globales
 * 
 * Emplacement: htdocs/src/Config/helpers.php
 */

// ============================================================
// FONCTION DE TRADUCTION __()
// ============================================================

if (!function_exists('__')) {
    function __(string $key, array $params = []): string {
        static $translations = null;
        
        if ($translations === null) {
            $translationsFile = __DIR__ . '/translations.php';
            if (file_exists($translationsFile)) {
                $translations = require $translationsFile;
            } else {
                $translations = [];
            }
        }
        
        $lang = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'fr';
        $text = $translations[$lang][$key] ?? $translations['fr'][$key] ?? $key;
        
        // Remplacer les paramètres :param
        foreach ($params as $param => $value) {
            $text = str_replace(':' . $param, $value, $text);
        }
        
        return $text;
    }
}

// ============================================================
// FONCTION SANITIZE
// ============================================================

if (!function_exists('sanitize')) {
    function sanitize(?string $string): string {
        if ($string === null) {
            return '';
        }
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

// ============================================================
// FONCTION FORMAT DATE
// ============================================================

if (!function_exists('formatDate')) {
    function formatDate(?string $date, string $format = 'd/m/Y H:i'): string {
        if (empty($date)) {
            return '';
        }
        try {
            $dt = new DateTime($date);
            return $dt->format($format);
        } catch (Exception $e) {
            return $date;
        }
    }
}

// ============================================================
// FONCTION FORMAT FILE SIZE
// ============================================================

if (!function_exists('formatFileSize')) {
    function formatFileSize(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

// ============================================================
// FONCTION CURRENT USER ID
// ============================================================

if (!function_exists('currentUserId')) {
    function currentUserId(): ?int {
        return $_SESSION['user']['id'] ?? null;
    }
}

// ============================================================
// FONCTION CURRENT USER
// ============================================================

if (!function_exists('currentUser')) {
    function currentUser(): ?array {
        return $_SESSION['user'] ?? null;
    }
}

// ============================================================
// FONCTION IS LOGGED IN
// ============================================================

if (!function_exists('isLoggedIn')) {
    function isLoggedIn(): bool {
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }
}

// ============================================================
// TRADUCTION DES DESCRIPTIONS D'ACTIVITÉ
// ============================================================

/**
 * Traduit une description d'activité française vers la langue active
 * 
 * Les descriptions sont stockées en français dans la BDD.
 * Cette fonction les traduit dynamiquement vers la langue de l'utilisateur.
 * 
 * @param string|null $description La description depuis la BDD
 * @return string La description traduite et échappée HTML
 */
if (!function_exists('translateActivityDescription')) {
    function translateActivityDescription(?string $description): string {
        if (empty($description)) {
            return '';
        }
        
        // Si la langue est le français, retourner tel quel
        // Utilise currentLang() si disponible, sinon détecte manuellement
        if (function_exists('currentLang')) {
            $lang = currentLang();
        } else {
            $lang = $_SESSION['lang'] ?? $_COOKIE['docuflow_lang'] ?? 'fr';
        }
        if ($lang === 'fr') {
            return htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
        }
        
        // Mapping des descriptions françaises vers anglais
        $translations = [
            // === ZONES ===
            'Suppression de zone' => 'Zone deletion',
            'Création de zone' => 'Zone creation',
            'Modification de zone' => 'Zone update',
            
            // === AUTHENTIFICATION ===
            'Connexion réussie' => 'Successful login',
            'Connexion' => 'Login',
            'Déconnexion' => 'Logout',
            
            // === ANNOTATIONS ===
            'Ajout d\'annotation' => 'Annotation added',
            'Résolution d\'annotation' => 'Annotation resolved',
            'Suppression d\'annotation' => 'Annotation deleted',
            'Modification d\'annotation' => 'Annotation updated',
            
            // === LIAISONS ===
            'Création de liaison' => 'Link creation',
            'Suppression de liaison' => 'Link deletion',
            'Modification de liaison' => 'Link update',
            
            // === DOCUMENTS ===
            'Import de document' => 'Document import',
            'Upload de document' => 'Document upload',
            'Suppression de document' => 'Document deleted',
            'Modification de document' => 'Document updated',
            'Consultation de document' => 'Document viewed',
            'Téléchargement de document' => 'Document downloaded',
            'Partage de document' => 'Document shared',
            
            // === UTILISATEURS ===
            'Création d\'utilisateur' => 'User created',
            'Modification d\'utilisateur' => 'User updated',
            'Suppression d\'utilisateur' => 'User deleted',
            'Activation d\'utilisateur' => 'User activated',
            'Désactivation d\'utilisateur' => 'User deactivated',
            
            // === ÉQUIPES ===
            'Création d\'équipe' => 'Team created',
            'Modification d\'équipe' => 'Team updated',
            'Suppression d\'équipe' => 'Team deleted',
            
            // === RÔLES ===
            'Création de rôle' => 'Role created',
            'Modification de rôle' => 'Role updated',
            'Suppression de rôle' => 'Role deleted',
            
            // === PROFIL ===
            'Modification du profil' => 'Profile updated',
            'Changement de mot de passe' => 'Password changed',
            
            // === MENTIONS ===
            'Mention d\'utilisateur' => 'User mentioned',
        ];
        
        // Chercher une correspondance exacte
        if (isset($translations[$description])) {
            return htmlspecialchars($translations[$description], ENT_QUOTES, 'UTF-8');
        }
        
        // Patterns avec numéros dynamiques (ex: "Création de zone sur le document #8")
        $patterns = [
            // Zones
            '/^Création de zone sur le document #(\d+)$/' => 'Zone creation on document #$1',
            '/^Suppression de zone sur le document #(\d+)$/' => 'Zone deletion on document #$1',
            '/^Modification de zone sur le document #(\d+)$/' => 'Zone update on document #$1',
            
            // Annotations
            '/^Ajout d\'annotation sur le document #(\d+)$/' => 'Annotation added on document #$1',
            '/^Suppression d\'annotation sur le document #(\d+)$/' => 'Annotation deleted on document #$1',
            '/^Résolution d\'annotation sur le document #(\d+)$/' => 'Annotation resolved on document #$1',
            
            // Liaisons
            '/^Liaison vers le document #(\d+)$/' => 'Link to document #$1',
            '/^Liaison depuis le document #(\d+)$/' => 'Link from document #$1',
            '/^Création de liaison vers le document #(\d+)$/' => 'Link creation to document #$1',
            '/^Création de liaison sur le document #(\d+)$/' => 'Link creation on document #$1',
            
            // Documents avec noms
            '/^Import du document "(.+)"$/' => 'Import of document "$1"',
            '/^Upload du document "(.+)"$/' => 'Upload of document "$1"',
            '/^Consultation du document "(.+)"$/' => 'Viewed document "$1"',
            '/^Téléchargement du document "(.+)"$/' => 'Downloaded document "$1"',
            '/^Suppression du document "(.+)"$/' => 'Deleted document "$1"',
        ];
        
        foreach ($patterns as $pattern => $replacement) {
            if (preg_match($pattern, $description)) {
                $translated = preg_replace($pattern, $replacement, $description);
                return htmlspecialchars($translated, ENT_QUOTES, 'UTF-8');
            }
        }
        
        // Si aucune correspondance, retourner l'original échappé
        return htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    }
}

// ============================================================
// TRADUCTION DU TYPE D'ENTITÉ
// ============================================================

if (!function_exists('translateEntityType')) {
    function translateEntityType(?string $entityType): string {
        if (empty($entityType)) {
            return '';
        }
        
        // Utilise currentLang() si disponible, sinon détecte manuellement
        if (function_exists('currentLang')) {
            $lang = currentLang();
        } else {
            $lang = $_SESSION['lang'] ?? $_COOKIE['docuflow_lang'] ?? 'fr';
        }
        
        if ($lang === 'fr') {
            return ucfirst($entityType);
        }
        
        $translations = [
            'document' => 'Document',
            'zone' => 'Zone',
            'annotation' => 'Annotation',
            'liaison' => 'Link',
            'link' => 'Link',
            'utilisateur' => 'User',
            'user' => 'User',
            'équipe' => 'Team',
            'team' => 'Team',
            'rôle' => 'Role',
            'role' => 'Role',
        ];
        
        $key = strtolower($entityType);
        return $translations[$key] ?? ucfirst($entityType);
    }
}
