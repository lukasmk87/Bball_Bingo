<?php
// header.php (Frontend)
// Diese Datei sollte im Hauptverzeichnis liegen, z. B. neben index.php, login.php, etc.
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
  <style>
    /* Grundlegende Styles für den Header und Navigation */
    header {
      background: #333;
      color: #fff;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: relative;
    }
    header h1 {
      margin: 0;
      font-size: 1.5em;
    }
    nav ul {
      display: flex;
      list-style: none;
      margin: 0;
      padding: 0;
    }
    nav ul li {
      margin-left: 20px;
    }
    nav ul li a {
      color: #fff;
      text-decoration: none;
    }
    /* Hamburger-Symbol (zunächst versteckt) */
    .hamburger {
      display: none;
      font-size: 1.5em;
      cursor: pointer;
    }
    /* Responsive Styles: Bei kleineren Bildschirmen als Hamburger-Menü */
    @media only screen and (max-width: 768px) {
      nav ul {
        flex-direction: column;
        position: absolute;
        top: 60px;
        left: 0;
        right: 0;
        background: #333;
        display: none;
        margin: 0;
        padding: 0;
      }
      nav ul.active {
        display: flex;
      }
      nav ul li {
        margin: 10px 0;
        text-align: center;
      }
      .hamburger {
        display: block;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Bingo Basketball</h1>
    <div class="hamburger" onclick="toggleNav()">&#9776;</div>
    <nav>
      <ul id="navLinks">
		<li><a href="index.php">Home</a></li>
		<li><a href="anleitung.php">Anleitung</a></li>
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
  <script>
    function toggleNav() {
      var navLinks = document.getElementById('navLinks');
      navLinks.classList.toggle('active');
    }
    // Standardmäßig soll das Menü im mobilen Modus zugeklappt sein
    document.addEventListener("DOMContentLoaded", function() {
      var navLinks = document.getElementById('navLinks');
      navLinks.classList.remove('active');
    });
  </script>
