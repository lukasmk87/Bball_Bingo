<?php
session_start();
include 'header.php';
include '../db.php';

echo "<h1>Statistiken</h1>";

// Globale Statistiken abrufen
$stmt = $pdo->query("SELECT page_views, games_played FROM global_stats LIMIT 1");
$globalStats = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<h2>Globale Statistiken</h2>";
echo "<p>Seitenaufrufe: " . htmlspecialchars($globalStats['page_views']) . "</p>";
echo "<p>Gespielte Spiele: " . htmlspecialchars($globalStats['games_played']) . "</p>";

// Spiel-spezifische Statistiken anzeigen
echo "<h2>Spiel-spezifische Statistiken</h2>";
$stmt2 = $pdo->query("SELECT gs.game_id, g.opponent, gs.play_count
                      FROM game_stats gs
                      LEFT JOIN games g ON gs.game_id = g.id
                      ORDER BY gs.play_count DESC");
$gameStats = $stmt2->fetchAll(PDO::FETCH_ASSOC);
if (count($gameStats) > 0) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Spiel-ID</th><th>Gegner</th><th>Anzahl Spiele</th></tr>";
    foreach ($gameStats as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['game_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['opponent']) . "</td>";
        echo "<td>" . htmlspecialchars($row['play_count']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Keine spielbezogenen Statistiken vorhanden.</p>";
}

include 'footer.php';
?>
