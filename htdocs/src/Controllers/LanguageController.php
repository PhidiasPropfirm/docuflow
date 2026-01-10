<?php
namespace App\Controllers;

class LanguageController {
    public function change(): void {
        $lang = $_GET['lang'] ?? $_POST['lang'] ?? 'fr';
        setLang($lang);
        $redirect = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $redirect);
        exit;
    }
}
