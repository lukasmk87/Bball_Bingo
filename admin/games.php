<?php
// admin/games.php
include 'header.php';
include '../db.php';

// Spiele aus der Datenbank laden (mit Join zu teams, um den Teamnamen anzuzeigen)
$stmt = $pdo->query("SELECT g.*, t.name as team_name FROM games g LEFT JOIN teams t ON g.team_id = t.id ORDER BY g.time DESC");
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Spieleverwaltung</h1>
<a href="add_game.php">Spiel hinzufügen</a>
<table>
    <tr>
        <th>ID</th>
        <th>Team</th>
        <th>Gegner</th>
        <th>Datum &amp; Uhrzeit</th>
        <th>Aktionen</th>
    </tr>
    <?php foreach ($games as $game): ?>
    <tr>
        <td><?php echo $game['id']; ?></td>
        <td><?php echo htmlspecialchars($game['team_name']); ?></td>
        <td><?php echo htmlspecialchars($game['opponent']); ?></td>
        <td><?php echo htmlspecialchars($game['time']); ?></td>
        <td>
            <a href="edit_game.php?id=<?php echo $game['id']; ?>">Bearbeiten</a> | 
            <a href="delete_game.php?id=<?php echo $game['id']; ?>" onclick="return confirm('Spiel wirklich löschen?');">Löschen</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php include 'footer.php'; ?>
