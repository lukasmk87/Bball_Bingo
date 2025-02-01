<?php
// admin/debug_settings.php
include 'header.php';  // Admin-header (sichert den Zugriff ab)
include '../db.php';

// Verarbeite Formular-Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ist das Kontrollkästchen gesetzt, wird debug_mode auf '1' gesetzt, sonst auf '0'
    $debug_mode = isset($_POST['debug_mode']) ? '1' : '0';
    $stmt = $pdo->prepare("UPDATE settings SET value = ? WHERE name = 'debug_mode'");
    if ($stmt->execute([$debug_mode])) {
        $message = "Debug-Modus aktualisiert.";
    } else {
        $error = "Fehler beim Aktualisieren des Debug-Modus.";
    }
}

// Aktuellen Debug-Modus abrufen
$stmt = $pdo->prepare("SELECT value FROM settings WHERE name = 'debug_mode'");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$current_debug = ($result && $result['value'] === '1') ? true : false;
?>
<h1>Debug-Einstellungen</h1>
<?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post" action="debug_settings.php">
    <label for="debug_mode">Debug-Modus aktivieren:</label>
    <input type="checkbox" name="debug_mode" id="debug_mode" <?php if($current_debug) echo "checked"; ?>>
    <br>
    <input type="submit" value="Einstellungen speichern">
</form>
<?php include 'footer.php'; ?>
