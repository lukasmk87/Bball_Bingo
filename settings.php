<?php
function get_debug_mode($pdo) {
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE name = 'debug_mode'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($result && $result['value'] === '1');
}
?>
