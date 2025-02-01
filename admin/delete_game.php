<?php
// admin/delete_game.php
include 'header.php';
include '../db.php';

if (!isset($_GET['id'])) {
    echo "Keine Spiel-ID angegeben.";
    exit;
}

$game_id = intval($_GET['id']);

$stmt = $pdo->prepare("DELETE FROM games WHERE id = ?");
if ($stmt->execute([$game_id])) {
    header("Location: games.php");
    exit;
} else {
    echo "Fehler beim Löschen des Spiels.";
}
?>
