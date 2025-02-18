<?php
include 'db.php';

if (isset($_GET['team_id'])) {
    $team_id = intval($_GET['team_id']);
    // Wähle nur Spiele, deren Startzeit NICHT mehr als 3 Stunden zurückliegt
    $stmt = $pdo->prepare("SELECT * FROM games WHERE team_id = ? AND time >= DATE_SUB(NOW(), INTERVAL 3 HOUR) ORDER BY time ASC");
    $stmt->execute([$team_id]);
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($games) {
        foreach ($games as $game) {
            // Formatieren der Spielzeit, z. B. "TT.MM.JJJJ HH:MM"
            $displayTime = date("d.m.Y H:i", strtotime($game['time']));
            echo '<option value="' . htmlspecialchars($game['id']) . '">Gegner: ' . htmlspecialchars($game['opponent']) . ' - ' . $displayTime . '</option>';
        }
    } else {
        echo '<option value="">Keine Spiele verfügbar</option>';
    }
}
?>