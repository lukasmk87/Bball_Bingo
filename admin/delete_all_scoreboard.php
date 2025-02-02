<?php
session_start();
include 'db.php';

try {
    $stmt = $pdo->prepare("DELETE FROM scoreboard");
    $stmt->execute();
    header("Location: scoreboard.php");
    exit;
} catch (PDOException $e) {
    echo "Fehler beim Löschen aller Einträge: " . htmlspecialchars($e->getMessage());
}
?>
