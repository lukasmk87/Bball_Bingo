<?php
// admin/unblock_user.php
include 'header.php';
include '../db.php';

if (!isset($_GET['id'])) {
    echo "Keine Benutzer-ID angegeben.";
    exit;
}

$user_id = intval($_GET['id']);

$stmt = $pdo->prepare("UPDATE users SET blocked = 0 WHERE id = ?");
if ($stmt->execute([$user_id])) {
    header("Location: users.php");
    exit;
} else {
    echo "Fehler beim Entsperren des Benutzers.";
}
?>
