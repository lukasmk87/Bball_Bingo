<?php
// style.php – Dynamische CSS-Datei (wie bereits in einer früheren Antwort gezeigt)
// Hier wird angenommen, dass die Farbwerte bereits aus der Datenbank geladen werden.
header("Content-type: text/css; charset: UTF-8");

include 'db.php';
include 'settings.php';

$bg_color         = get_setting($pdo, 'site_bg_color')         ?: '#f4f4f4';
$text_color       = get_setting($pdo, 'site_text_color')       ?: '#333333';
$header_bg_color  = get_setting($pdo, 'site_header_bg_color')  ?: '#333333';
$link_color       = get_setting($pdo, 'site_link_color')       ?: '#ffffff';
?>
/* Grundlegende Reset- und Basis-Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: Arial, sans-serif;
  line-height: 1.6;
  background: <?php echo $bg_color; ?>;
  color: <?php echo $text_color; ?>;
}

/* Header (Frontend) */
header {
  background: <?php echo $header_bg_color; ?>;
  color: #fff;
  padding: 10px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
}

/* Header im "loggedin" Zustand (kleinere Abstände, kompakteres Layout) */
header.loggedin {
  padding: 5px 10px;
}

header h1 {
  font-size: 1.5em;
}

/* Navigation */
nav ul {
  display: flex;
  list-style: none;
}

nav ul li {
  margin-left: 20px;
}

nav ul li a {
  color: <?php echo $link_color; ?>;
  text-decoration: none;
}

/* Hamburger-Symbol: Standardmäßig ausgeblendet */
.hamburger {
  display: none;
  font-size: 1.5em;
  cursor: pointer;
}

/* Responsive Styles: Für Bildschirme bis 768px */
@media only screen and (max-width: 768px) {
  .hamburger {
    display: block;
    z-index: 1001;
  }
  nav ul {
    flex-direction: column;
    position: absolute;
    top: 60px; /* Höhe des Headers */
    left: 0;
    right: 0;
    background: <?php echo $header_bg_color; ?>;
    display: none;
    margin: 0;
    padding: 0;
    z-index: 1000;
  }
  nav ul.active {
    display: flex;
  }
  nav ul li {
    margin: 10px 0;
    text-align: center;
  }
}

/* Main Content */
main {
  padding: 20px;
}

/* Footer */
footer {
  background: <?php echo $header_bg_color; ?>;
  color: #fff;
  text-align: center;
  padding: 10px;
  position: relative;
  bottom: 0;
  width: 100%;
  z-index: 1;
}

/* Debug-Indikator (z.B. im Admin-Bereich) */
.debug-indicator {
  position: fixed;
  bottom: 0;
  right: 0;
  background: red;
  color: white;
  padding: 5px;
  font-size: 0.8em;
  z-index: 1000;
}
