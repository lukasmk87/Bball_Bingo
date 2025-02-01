<?php
// admin/approve_field.php
include 'header.php';
include '../db.php';

if (!isset($_GET['id'])) {
    echo "Keine Bingo-Feld-ID angegeben.";
    exit;
}

$field_id = intval($_GET['id']);

$stmt = $pdo->prepare("UPDATE bingo_fields SET approved = 1 WHERE id = ?");
if ($stmt->execute([$field_id])) {
    header("Location: bingofields.php");
    exit;
} else {
    echo "Fehler beim Freigeben des Bingo-Felds.";
}
?>
