<?php
// fetch_teams.php
include 'db.php';

if (isset($_GET['club_id'])) {
    $club_id = intval($_GET['club_id']);
    $stmt = $pdo->prepare("SELECT * FROM teams WHERE club_id = ?");
    $stmt->execute([$club_id]);
    $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo '<option value="">Wählen Sie ein Team</option>';
    foreach ($teams as $team) {
        echo '<option value="' . $team['id'] . '">' . htmlspecialchars($team['name']) . '</option>';
    }
}
