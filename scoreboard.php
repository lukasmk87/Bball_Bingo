<?php
include 'db.php';
include 'header.php';

// Bestenliste aus der Datenbank laden (hier als Beispiel aus einer Tabelle "scoreboard")
$stmt = $pdo->query("SELECT * FROM scoreboard ORDER BY bingos DESC, activated_fields DESC");
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<main>
  <h1>Bestenliste</h1>
  <table>
    <tr>
      <th>Benutzer</th>
      <th>Spiel</th>
      <th>Aktivierte Felder</th>
      <th>Bingos</th>
      <th>Gewinnquote</th>
      <th>Feldquote</th>
    </tr>
    <?php foreach ($scores as $score): ?>
      <tr>
        <td><?php echo htmlspecialchars($score['username']); ?></td>
        <td><?php echo htmlspecialchars($score['game']); ?></td>
        <td><?php echo htmlspecialchars($score['activated_fields']); ?></td>
        <td><?php echo htmlspecialchars($score['bingos']); ?></td>
        <td><?php echo number_format($score['win_rate'] * 100, 2); ?>%</td>
        <td><?php echo number_format($score['field_rate'] * 100, 2); ?>%</td>
      </tr>
    <?php endforeach; ?>
  </table>
</main>
<?php include 'footer.php'; ?>
