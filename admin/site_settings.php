<?php
session_start();
include 'header.php';
include '../db.php';
include '../settings.php';

// Zugriff nur für Administratoren
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    die("Zugriff verweigert. Nur Administratoren dürfen diese Seite aufrufen.");
}

// Definiere die zu bearbeitenden Einstellungen: Farbwerte und Versionsnummer
$settingsList = [
    'site_bg_color'         => 'Hintergrundfarbe der Seite',
    'site_text_color'       => 'Textfarbe der Seite',
    'site_header_bg_color'  => 'Hintergrundfarbe des Headers',
    'site_link_color'       => 'Linkfarbe',
    'site_version'          => 'Version'
];

$message = "";

// Formularverarbeitung: Beim POST werden die Einstellungen aktualisiert.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($settingsList as $key => $label) {
        if (isset($_POST[$key])) {
            $value = trim($_POST[$key]);
            // Für Farbwerte: validiere mit Regex (erwartet Hex-Wert, z.B. #ffffff)
            if (strpos($key, 'color') !== false) {
                if (preg_match('/^#[a-fA-F0-9]{6}$/', $value)) {
                    set_setting($pdo, $key, $value);
                }
            } else {
                // Für die Versionsnummer (oder andere Textwerte) keine strikte Validierung
                set_setting($pdo, $key, $value);
            }
        }
    }
    $message = "Einstellungen erfolgreich aktualisiert.";
}

// Aktuelle Einstellungen abrufen
$currentSettings = [];
foreach ($settingsList as $key => $label) {
    $currentSettings[$key] = get_setting($pdo, $key);
}
?>
<div class="container">
    <h1>Site Einstellungen</h1>
    <?php if (!empty($message)): ?>
        <p style="color:green;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="post" action="site_settings.php">
        <?php foreach ($settingsList as $key => $label): ?>
            <div class="form-group">
                <label for="<?php echo $key; ?>"><?php echo $label; ?>:</label>
                <?php if (strpos($key, 'color') !== false): ?>
                    <input type="color" id="<?php echo $key; ?>" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($currentSettings[$key] ?: '#ffffff'); ?>" required>
                <?php else: ?>
                    <input type="text" id="<?php echo $key; ?>" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($currentSettings[$key] ?: '1.0.0'); ?>" required>
                <?php endif; ?>
            </div>
            <br>
        <?php endforeach; ?>
        <div class="actions">
            <input type="submit" value="Einstellungen speichern">
        </div>
    </form>
</div>
<?php include 'footer.php'; ?>
