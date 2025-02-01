<?php
// header.php (Frontend)
// Session starten, falls noch nicht geschehen:
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Bingo Basketball</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Registrieren</a></li>
        <li><a href="guest.php">Als Gast spielen</a></li>
        <li><a href="scoreboard.php">Bestenliste</a></li>
        <li><a href="suggestions.php">Vorschläge</a></li>
        <?php if (isset($_SESSION['user']) && !empty($_SESSION['user']) && isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin']): ?>
          <li><a href="admin/dashboard.php">Admin Dashboard</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>
