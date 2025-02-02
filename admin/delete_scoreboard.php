<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    die("Keine ID angegeben.");
}

$id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("DELETE FROM scoreboard WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: scoreboard.php");
    exit;
} catch (PDOException $e) {
    echo "Fehler beim Löschen des Eintrags: " . htmlspecialchars($e->getMessage());
}
?>
