<?php
session_start();
include 'db.php';

$active_fields = isset($_POST['active_fields']) ? intval($_POST['active_fields']) : 0;
$bingos = isset($_POST['bingos']) ? intval($_POST['bingos']) : 0;
$win_rate = isset($_POST['win_rate']) ? floatval($_POST['win_rate']) : 0;
$field_rate = isset($_POST['field_rate']) ? floatval($_POST['field_rate']) : 0;

// Benutzername aus der Session (oder "Gast")
$username = isset($_SESSION['user']['username']) ? $_SESSION['user']['username'] : 'Gast';
// Spiel-ID aus der Session (oder "Unbekannt")
$game = isset($_SESSION['game_id']) ? $_SESSION['game_id'] : 'Unbekannt';

$stmt = $pdo->prepare("INSERT INTO scoreboard (username, game, activated_fields, bingos, win_rate, field_rate) VALUES (?, ?, ?, ?, ?, ?)");
if ($stmt->execute([$username, $game, $active_fields, $bingos, $win_rate, $field_rate])) {
    echo "Erfolgreich";
} else {
    echo "Fehler";
}
?>
