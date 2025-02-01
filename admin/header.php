<?php
session_start();

// Prüfen, ob ein Admin eingeloggt ist
if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
    header("Location: ../login.php");
    exit;
}

// Datenbank- und Debug-Einstellungen einbinden
include '../db.php';
include '../settings.php';
$debug_mode = get_debug_mode($pdo);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style.css">
  <style>
    /* Sidebar-Stile für das Admin-Dashboard */
    .sidebar {
      width: 200px;
      float: left;
    }
    .content {
      margin-left: 210px;
    }
    .sidebar ul {
      list-style-type: none;
      padding: 0;
    }
    .sidebar li {
      margin: 10px 0;
    }
    /* Debug-Indikator, fixiert am unteren rechten Rand */
    .debug-indicator {
      position: fixed;
      bottom: 0;
      right: 0;
      background-color: red;
      color: white;
      padding: 5px;
      font-size: 0.8em;
      z-index: 1000;
    }
  </style>
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
      <li><a href="debug_settings.php">Debug Einstellungen</a></li>
    </ul>
  </div>
  <div class="content">
    <?php
    // Falls der Debug-Modus aktiviert ist, wird ein Hinweis eingeblendet
    if ($debug_mode) {
        echo '<div class="debug-indicator">Debug Mode ON</div>';
    }
    ?>
