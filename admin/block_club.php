<?php
// admin/block_club.php
include 'header.php';
include '../db.php';

if (!isset($_GET['id'])) {
    echo "Keine Vereins-ID angegeben.";
    exit;
}

$club_id = intval($_GET['id']);

$stmt = $pdo->prepare("UPDATE clubs SET blocked = 1 WHERE id = ?");
if ($stmt->execute([$club_id])) {
    header("Location: clubs.php");
    exit;
} else {
    echo "Fehler beim Sperren des Vereins.";
}
?>
