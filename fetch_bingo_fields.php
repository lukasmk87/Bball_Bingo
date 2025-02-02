<?php
// fetch_bingo_fields.php
session_start();
include 'db.php';

// Hole die Team-ID aus der Session (falls vorhanden) – ansonsten werden nur Standardfelder verwendet
$team_id = isset($_SESSION['team_id']) ? $_SESSION['team_id'] : null;

// Zuerst teambezogene Felder laden (falls vorhanden)
if ($team_id) {
    $stmt = $pdo->prepare("SELECT * FROM bingo_fields WHERE team_id = ? AND approved = 1");
    $stmt->execute([$team_id]);
    $teamFields = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $teamFields = [];
}

// Wenn weniger als 25 Felder vorhanden, Standardfelder laden
if (count($teamFields) < 25) {
    $stmt = $pdo->prepare("SELECT * FROM bingo_fields WHERE is_standard = 1 AND approved = 1");
    $stmt->execute();
    $standardFields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $fields = array_merge($teamFields, $standardFields);
    $fields = array_slice($fields, 0, 25);
} else {
    $fields = array_slice($teamFields, 0, 25);
}

// Mische die Felder zufällig
shuffle($fields);

// Generiere den HTML-Code für 25 Felder
foreach ($fields as $field) {
    $text = htmlspecialchars($field['description']);
    echo "<div class='bingo-cell' onclick='toggleActive(this)'>$text</div>";
}
?>
