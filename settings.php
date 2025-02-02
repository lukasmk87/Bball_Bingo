<?php
/**
 * settings.php
 *
 * Hilfsfunktionen zur Verwaltung von Systemeinstellungen, die in der Datenbank (Tabelle "settings") gespeichert sind.
 */

function get_setting(PDO $pdo, $name) {
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE name = :name LIMIT 1");
    $stmt->execute([':name' => $name]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['value'] : null;
}

function set_setting(PDO $pdo, $name, $value) {
    $stmt = $pdo->prepare("INSERT INTO settings (name, value) VALUES (:name, :value)
                           ON DUPLICATE KEY UPDATE value = :value");
    return $stmt->execute([':name' => $name, ':value' => $value]);
}

function get_debug_mode(PDO $pdo) {
    $debug_value = get_setting($pdo, 'debug_mode');
    return ($debug_value === '1');
}
?>
