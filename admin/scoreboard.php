<?php
session_start();
include 'header.php';
include '../db.php';

// Scoreboard-Einträge abrufen
try {
    $stmt = $pdo->query("SELECT * FROM scoreboard ORDER BY id DESC");
    $scoreboardEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Fehler beim Laden der Scoreboard-Einträge: " . htmlspecialchars($e->getMessage()) . "</p>";
    $scoreboardEntries = [];
}
?>
<main>
    <h1>Scoreboard Verwaltung</h1>
    <?php if(count($scoreboardEntries) > 0): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Benutzer</th>
                    <th>Spiel</th>
                    <th>Aktivierte Felder</th>
                    <th>Bingos</th>
                    <th>Gewinnquote</th>
                    <th>Feldquote</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($scoreboardEntries as $entry): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($entry['id']); ?></td>
                        <td><?php echo htmlspecialchars($entry['username']); ?></td>
                        <td><?php echo htmlspecialchars($entry['game']); ?></td>
                        <td><?php echo htmlspecialchars($entry['activated_fields']); ?></td>
                        <td><?php echo htmlspecialchars($entry['bingos']); ?></td>
                        <td><?php echo htmlspecialchars($entry['win_rate']); ?>%</td>
                        <td><?php echo htmlspecialchars($entry['field_rate']); ?>%</td>
                        <td>
                            <a href="delete_scoreboard.php?id=<?php echo $entry['id']; ?>" onclick="return confirm('Möchten Sie diesen Eintrag wirklich löschen?');">Löschen</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <form method="post" action="delete_all_scoreboard.php" onsubmit="return confirm('Möchten Sie wirklich ALLE Scoreboard-Einträge löschen?');">
            <input type="submit" value="Alle Einträge löschen">
        </form>
    <?php else: ?>
        <p>Keine Scoreboard-Einträge gefunden.</p>
    <?php endif; ?>
</main>
<?php include 'footer.php'; ?>
