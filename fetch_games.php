<?php
// fetch_games.php
include 'db.php';

if (isset($_GET['team_id'])) {
    $team_id = intval($_GET['team_id']);
    $stmt = $pdo->prepare("SELECT * FROM games WHERE team_id = ?");
    $stmt->execute([$team_id]);
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo '<option value="">Wählen Sie ein Spiel</option>';
    foreach ($games as $game) {
        // Es wird angenommen, dass die Tabelle "games" Spalten wie opponent und time besitzt
        echo '<option value="' . $game['id'] . '">' . htmlspecialchars($game['opponent']) . ' - ' . htmlspecialchars($game['time']) . '</option>';
    }
}
