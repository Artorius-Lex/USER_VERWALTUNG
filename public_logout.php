<?php
/**
 * logout.php - Beendet die Benutzersession
 * 
 * Dieser Script:
 * 1. Löscht alle Session-Daten
 * 2. Beendet die Session
 * 3. Leitet zurück zur Login-Seite
 */

// Session starten (um darauf zuzugreifen)
session_start();

// Alle Session-Variablen löschen
$_SESSION = [];

// Session-Cookie löschen
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Session beenden
session_destroy();

// Zur Login-Seite weiterleiten
header('Location: login.php');
exit();