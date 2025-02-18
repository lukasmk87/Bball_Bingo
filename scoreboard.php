<?php
session_start();
include 'header.php';
include 'db.php';

try {
    // Scoreboard-Einträge werden zusammen mit den Spiel- und Teamdetails abgerufen.
    // Hier wird angenommen, dass in der Scoreboard-Tabelle die Spalte 'game' den Bezug zur Spiele-Tabelle enthält.
    $stmt = $pdo->query("
      SELECT s.id, s.username, s.activated_fields, s.bingos, s.win_rate, s.field_rate,
             g.opponent, g.time, t.name AS team_name 
      FROM scoreboard s
      JOIN games g ON s.game = g.id
      JOIN teams t ON g.team_id = t.id
      ORDER BY s.id DESC
    ");
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "<p>Fehler beim Laden der Scoreboard-Einträge: " . htmlspecialchars($e->getMessage()) . "</p>";
    $entries = [];
}
?>
<div class="scoreboard-container container">
    <h1>Bestenliste</h1>
    <?php if(count($entries) > 0): ?>
      <table class="scoreboard-table table">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Benutzer</th>
                  <th>Mannschaft</th>
                  <th>Gegner</th>
                  <th>Zeit</th>
                  <th>Aktivierte Felder</th>
                  <th>Bingos</th>
                  <th>Gewinnquote</th>
                  <th>Feldquote</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach($entries as $entry): ?>
              <tr>
                  <td><?php echo htmlspecialchars($entry['id']); ?></td>
                  <td><?php echo htmlspecialchars($entry['username']); ?></td>
                  <td><?php echo htmlspecialchars($entry['team_name']); ?></td>
                  <td><?php echo htmlspecialchars($entry['opponent']); ?></td>
                  <td><?php echo htmlspecialchars(date("d.m.Y H:i", strtotime($entry['time']))); ?></td>
                  <td><?php echo htmlspecialchars($entry['activated_fields']); ?></td>
                  <td><?php echo htmlspecialchars($entry['bingos']); ?></td>
                  <td><?php echo htmlspecialchars($entry['win_rate']); ?>%</td>
                  <td><?php echo htmlspecialchars($entry['field_rate']); ?>%</td>
              </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
    <?php else: ?>
      <p>Keine Scoreboard-Einträge gefunden.</p>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
