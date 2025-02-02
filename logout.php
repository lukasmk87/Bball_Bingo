<?php
session_start();

// Alle Session-Daten löschen
$_SESSION = array();

// Optional: Session-Cookie löschen
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Session zerstören
session_destroy();

// Zurück zur Startseite oder Login-Seite umleiten
header("Location: index.php");
exit;
?>
