<?php
session_start();
// Gast spielt – hier wird ein Gast-Benutzer in der Session gesetzt.
$_SESSION['user'] = ['username' => 'Gast', 'guest' => true];
header("Location: game.php");
exit;
