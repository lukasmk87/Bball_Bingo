<?php
session_start();
include 'db.php';
include 'header.php';

try {
    // Alle Scoreboard-Einträge abrufen – sortiert z. B. nach Gewinnquote absteigend
    $stmt = $pdo->query("SELECT * FROM scoreboard ORDER BY win_rate DESC");
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Fehler beim Laden der Scoreboard-Einträge: " . htmlspecialchars($e->getMessage()) . "</p>";
    $entries = [];
}
?>
<main>
  <h1>Bestenliste</h1>
  <div class="scoreboard-container">
    <?php if (count($entries) > 0): ?>
      <table class="scoreboard-table">
        <thead>
          <tr>
            <th>Benutzer</th>
            <th>Spiel</th>
            <th>Aktivierte Felder</th>
            <th>Bingos</th>
            <th>Gewinnquote</th>
            <th>Feldquote</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($entries as $entry): ?>
            <tr>
              <td><?php echo htmlspecialchars($entry['username']); ?></td>
              <td><?php echo htmlspecialchars($entry['game']); ?></td>
              <td><?php echo htmlspecialchars($entry['activated_fields']); ?></td>
              <td><?php echo htmlspecialchars($entry['bingos']); ?></td>
              <td><?php echo number_format($entry['win_rate'], 2) . "%"; ?></td>
              <td><?php echo number_format($entry['field_rate'], 2) . "%"; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>Keine Scoreboard-Einträge vorhanden.</p>
    <?php endif; ?>
  </div>
</main>
<!-- Inline-CSS für das Scoreboard. Diesen Code kannst Du auch in Deine externe CSS-Datei (z. B. style.css) übertragen. -->
<style>
.scoreboard-container {
    max-width: 800px;
    margin: 20px auto;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.scoreboard-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95em;
}

.scoreboard-table th, 
.scoreboard-table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: center;
}

.scoreboard-table th {
    background-color: #007bff;
    color: #fff;
}

.scoreboard-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.scoreboard-table tr:hover {
    background-color: #f1f1f1;
}
</style>
<?php include 'footer.php'; ?>
