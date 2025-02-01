<?php
// admin/unblock_team.php
include 'header.php';
include '../db.php';

if (!isset($_GET['id'])) {
    echo "Keine Team-ID angegeben.";
    exit;
}

$team_id = intval($_GET['id']);

$stmt = $pdo->prepare("UPDATE teams SET blocked = 0 WHERE id = ?");
if ($stmt->execute([$team_id])) {
    header("Location: teams.php");
    exit;
} else {
    echo "Fehler beim Entsperren des Teams.";
}
?>
