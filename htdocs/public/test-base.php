<?php
/**
 * TEST - Vérifier BaseController
 * Upload dans: htdocs/public/test-base.php
 */

echo "<h2>Vérification BaseController</h2>";

$basePath = __DIR__ . '/../src/Controllers/BaseController.php';

echo "<p>Chemin recherché: <code>$basePath</code></p>";

if (file_exists($basePath)) {
    echo "<p style='color:green;'>✅ BaseController.php EXISTE</p>";
    echo "<p>Taille: " . filesize($basePath) . " octets</p>";
    
    // Vérifier le namespace
    $content = file_get_contents($basePath);
    if (strpos($content, 'namespace App\\Controllers') !== false) {
        echo "<p style='color:green;'>✅ Namespace correct</p>";
    } else {
        echo "<p style='color:red;'>❌ Namespace incorrect ou manquant</p>";
    }
    
    if (strpos($content, 'class BaseController') !== false) {
        echo "<p style='color:green;'>✅ Classe BaseController déclarée</p>";
    } else {
        echo "<p style='color:red;'>❌ Classe BaseController non trouvée dans le fichier</p>";
    }
} else {
    echo "<p style='color:red;font-weight:bold;'>❌ BaseController.php N'EXISTE PAS !</p>";
    
    // Lister les fichiers dans Controllers
    echo "<h3>Fichiers dans /src/Controllers/ :</h3>";
    $controllersDir = __DIR__ . '/../src/Controllers/';
    if (is_dir($controllersDir)) {
        $files = scandir($controllersDir);
        echo "<ul>";
        foreach ($files as $f) {
            if ($f !== '.' && $f !== '..') {
                echo "<li>$f</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p style='color:red;'>Le dossier Controllers n'existe pas!</p>";
    }
}
