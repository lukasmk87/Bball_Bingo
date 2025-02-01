<?php
// admin/header.php
session_start();
if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
    header("Location: ../login.php");
    exit;
}
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
      <li><a href="games.php">Spiele</a></li> <!-- Neuer Menüpunkt -->
      <li><a href="bingofields.php">Bingo Felder</a></li>
      <li><a href="suggestions.php">Vorschläge</a></li>
    </ul>
  </div>
  <div class="content">
