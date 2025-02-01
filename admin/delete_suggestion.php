<?php
// admin/delete_suggestion.php
include 'header.php';
include '../db.php';

if (!isset($_GET['id'])) {
    echo "Keine Vorschlags-ID angegeben.";
    exit;
}

$suggestion_id = intval($_GET['id']);

$stmt = $pdo->prepare("DELETE FROM suggestions WHERE id = ?");
if ($stmt->execute([$suggestion_id])) {
    header("Location: suggestions.php");
    exit;
} else {
    echo "Fehler beim Löschen des Vorschlags.";
}
?>
