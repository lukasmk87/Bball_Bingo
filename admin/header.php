<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['is_admin']) || $_SESSION['user']['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

include '../db.php';
include_once '../settings.php';  // Verwende include_once, um Mehrfachdeklarationen zu vermeiden
$debug_mode = get_debug_mode($pdo);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="sidebar">
    <h2>Admin Dashboard</h2>
    <ul>
      <li><a href="dashboard.php">Übersicht</a></li>
      <li><a href="users.php">Benutzerverwaltung</a></li>
      <li><a href="clubs.php">Vereine</a></li>
      <li><a href="teams.php">Teams</a></li>
      <li><a href="games.php">Spiele</a></li>
      <li><a href="bingofields.php">Bingo Felder</a></li>
      <li><a href="suggestions.php">Vorschläge</a></li>
      <li><a href="statistics.php">Statistiken</a></li>
      <li><a href="debug_settings.php">Debug Einstellungen</a></li>
      <li><a href="site_settings.php">Einstellungen</a></li>
      <li><a href="../index.php">Zum Frontend</a></li>
      <li><a href="export_game.php">CSV Export Spiele</a></li>
      <li><a href="import_game.php">CSV Import Spiele</a></li>
    </ul>
  </div>
  <div class="content">
    <?php if ($debug_mode) echo '<div class="debug-indicator">Debug Mode ON</div>'; ?>
