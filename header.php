<?php
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
    <div class="logo">
      <a href="index.php">
        <img src="https://crossoverpodcast.de/wp-content/uploads/2025/02/DALL%C2%B7E-2025-02-02-01.04.33-Minimalist-logo-for-a-basketball-podcast-named-CrossOver.-The-design-includes-a-stylized-basketball-possibly-as-an-outline-integrated-with-the-wor.webp" alt="CrossOver Basketball Podcast Logo">
      </a>
    </div>
    <h1>Bingo Basketball</h1>
    <div class="hamburger" onclick="toggleNav()">&#9776;</div>
    <nav>
      <ul id="navLinks">
        <li><a href="index.php">Home</a></li>
        <?php if (!isset($_SESSION['user'])): ?>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Registrieren</a></li>
          <li><a href="guest.php">Als Gast spielen</a></li>
        <?php else: ?>
          <li><a href="logout.php">Logout</a></li>
        <?php endif; ?>
        <li><a href="scoreboard.php">Bestenliste</a></li>
        <li><a href="suggestions.php">Vorschläge</a></li>
		<li><a href="support.php">Support</a></li>
        <li><a href="anleitung.php">Anleitung</a></li>
        <?php if (isset($_SESSION['user']) && isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin']): ?>
          <li><a href="admin/dashboard.php">Admin Dashboard</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>
  <script>
    function toggleNav() {
      var navLinks = document.getElementById('navLinks');
      navLinks.classList.toggle('active');
    }
    document.addEventListener("DOMContentLoaded", function() {
      var navLinks = document.getElementById('navLinks');
      navLinks.classList.remove('active');
    });
  </script>
