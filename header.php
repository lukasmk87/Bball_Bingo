<?php
// header.php (Frontend)
// Diese Datei sollte im Hauptverzeichnis liegen, z. B. neben index.php, login.php, etc.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$loggedIn = isset($_SESSION['user']);  // Wenn ein Benutzer angemeldet ist
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Bingo Basketball</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Dynamische CSS-Datei einbinden -->
  <link rel="stylesheet" href="style.php">
</head>
<body>
  <header class="<?php echo $loggedIn ? 'loggedin' : ''; ?>">
    <h1>Bingo Basketball</h1>
    <!-- Hamburger-Symbol, sichtbar bei kleinen Bildschirmen -->
    <div class="hamburger" onclick="toggleNav()">&#9776;</div>
    <nav>
      <ul id="navLinks">
        <li><a href="index.php">Home</a></li>
        <?php if (!$loggedIn): ?>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Registrieren</a></li>
          <li><a href="guest.php">Als Gast spielen</a></li>
        <?php endif; ?>
        <li><a href="scoreboard.php">Bestenliste</a></li>
        <li><a href="suggestions.php">Vorschläge</a></li>
        <li><a href="anleitung.php">Anleitung</a></li>
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
    document.addEventListener("DOMContentLoaded", function() {
      var navLinks = document.getElementById('navLinks');
      navLinks.classList.remove('active');
    });
  </script>
