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
<!-- Inline-CSS für das Scoreboard-Layout (kann auch in admin/style.css ausgelagert werden) -->
<style>
.scoreboard-container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    border-radius: 8px;
}

.scoreboard-container h1 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

.scoreboard-actions {
    text-align: right;
    margin-bottom: 15px;
}

.scoreboard-actions a {
    background: #dc3545;
    color: #fff;
    padding: 8px 12px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: bold;
}

.scoreboard-actions a:hover {
    background: #c82333;
}

.scoreboard-table {
    width: 100%;
    border-collapse: collapse;
}

.scoreboard-table th,
.scoreboard-table td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

.scoreboard-table th {
    background-color: #343a40;
    color: #fff;
}

.scoreboard-table tbody tr:hover {
    background-color: #f1f1f1;
}

.btn-delete {
    background: #dc3545;
    color: #fff;
    padding: 6px 10px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.9em;
}

.btn-delete:hover {
    background: #c82333;
}
</style>

<div class="scoreboard-container">
    <h1>Scoreboard Verwaltung</h1>
    
    <div class="scoreboard-actions">
        <!-- Link zum Löschen aller Scoreboard-Einträge -->
        <a href="delete_all_scoreboard.php" onclick="return confirm('Möchten Sie wirklich ALLE Scoreboard-Einträge löschen?');">Alle Einträge löschen</a>
    </div>
    
    <?php if(count($scoreboardEntries) > 0): ?>
        <table class="scoreboard-table">
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
                            <a href="delete_scoreboard.php?id=<?php echo $entry['id']; ?>" class="btn-delete" onclick="return confirm('Möchten Sie diesen Eintrag wirklich löschen?');">Löschen</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Keine Scoreboard-Einträge gefunden.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
