<?php
/**
 * DocuFlow - Configuration Principale
 * Portail collaboratif de gestion documentaire
 */

// Mode debug (mettre à false en production)
define('DEBUG_MODE', true);

// Configuration de la base de données
define('DB_HOST', 'sql100.infinityfree.com');
define('DB_NAME', 'if0_40864379_privateaccountig');
define('DB_USER', 'if0_40864379');
define('DB_PASS', trim(file_get_contents(__DIR__ . '/db.key')));
define('DB_CHARSET', 'utf8mb4');

// Configuration de l'application
define('APP_NAME', 'DocuFlow');
define('APP_URL', 'https://privateaccounting.fwh.is');
define('APP_VERSION', '1.0.0');

// Configuration des uploads
define('UPLOAD_DIR', __DIR__ . '/../../public/uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10 Mo
define('ALLOWED_EXTENSIONS', ['pdf']);

// Configuration de sécurité
define('SESSION_LIFETIME', 3600 * 8); // 8 heures
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_MIN_LENGTH', 8);

// Configuration des notifications
define('NOTIFICATION_POLL_INTERVAL', 30000); // 30 secondes en millisecondes

// Timezone
date_default_timezone_set('Europe/Paris');

// Gestion des erreurs
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Démarrage de la session sécurisée
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict');
    session_start();
}

// ============================================
// Chargement des helpers (traduction activités)
// ============================================
require_once __DIR__ . '/helpers.php';

// Connexion à la base de données (Singleton)
class Database {
    private static ?PDO $instance = null;
    
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                if (DEBUG_MODE) {
                    die("Erreur de connexion : " . $e->getMessage());
                }
                die("Erreur de connexion à la base de données.");
            }
        }
        return self::$instance;
    }
    
    // Empêcher le clonage
    private function __clone() {}
}

// Fonctions utilitaires globales
function db(): PDO {
    return Database::getInstance();
}

function redirect(string $url): void {
    header("Location: " . APP_URL . $url);
    exit;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function currentUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    static $user = null;
    if ($user === null) {
        $stmt = db()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([currentUserId()]);
        $user = $stmt->fetch();
    }
    return $user;
}

function csrf_token(): string {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function csrf_field(): string {
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . csrf_token() . '">';
}

function verify_csrf(string $token): bool {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function formatDate(string $date, string $format = 'd/m/Y H:i'): string {
    return (new DateTime($date))->format($format);
}

function formatFileSize($bytes) {
    if ($bytes == 0) return '0 o';
    $units = ['o', 'Ko', 'Mo', 'Go'];
    $power = floor(log($bytes, 1024));
    return round($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
}

function flash(string $type, string $message): void {
    $_SESSION['flash'][$type] = $message;
}

function getFlash(string $type): ?string {
    $message = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $message;
}

function hasFlash(string $type): bool {
    return isset($_SESSION['flash'][$type]);
}

// ============================================
// Système de traduction FR/EN
// ============================================

/**
 * Récupère la langue courante
 */
function currentLang(): string {
    if (isset($_SESSION['lang'])) {
        return $_SESSION['lang'];
    }
    if (isset($_COOKIE['docuflow_lang'])) {
        $_SESSION['lang'] = $_COOKIE['docuflow_lang'];
        return $_COOKIE['docuflow_lang'];
    }
    return 'fr';
}

/**
 * Change la langue
 */
function setLang(string $lang): void {
    $allowed = ['fr', 'en'];
    if (!in_array($lang, $allowed)) {
        $lang = 'fr';
    }
    $_SESSION['lang'] = $lang;
    setcookie('docuflow_lang', $lang, time() + (365 * 24 * 60 * 60), '/');
}

/**
 * Charge les traductions
 */
function loadTranslations(): array {
    static $translations = null;
    if ($translations === null) {
        $file = __DIR__ . '/translations.php';
        if (file_exists($file)) {
            $translations = require $file;
        } else {
            $translations = ['fr' => [], 'en' => []];
        }
    }
    return $translations;
}

/**
 * Traduit une clé
 */
function __(string $key, array $params = []): string {
    $translations = loadTranslations();
    $lang = currentLang();
    
    $text = $translations[$lang][$key] ?? $translations['fr'][$key] ?? $key;
    
    foreach ($params as $param => $value) {
        $text = str_replace(':' . $param, $value, $text);
    }
    
    return $text;
}

/**
 * Alias court pour echo
 */
function _e(string $key, array $params = []): void {
    echo __($key, $params);
}
