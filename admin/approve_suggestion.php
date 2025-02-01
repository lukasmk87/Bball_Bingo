<?php
// admin/approve_suggestion.php
include 'header.php';
include '../db.php';

if (!isset($_GET['id'])) {
    echo "Keine Vorschlags-ID angegeben.";
    exit;
}

$suggestion_id = intval($_GET['id']);

// Im Beispiel wird die Freigabe durch Löschen des Vorschlags realisiert
$stmt = $pdo->prepare("DELETE FROM suggestions WHERE id = ?");
if ($stmt->execute([$suggestion_id])) {
    header("Location: suggestions.php");
    exit;
} else {
    echo "Fehler beim Freigeben des Vorschlags.";
}
?>
