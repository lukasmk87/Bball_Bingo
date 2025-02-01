<?php
include 'header.php';
include '../db.php';

$stmt = $pdo->query("SELECT * FROM suggestions");
$suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Vorschläge verwalten</h1>
<table>
  <tr>
    <th>ID</th>
    <th>Typ</th>
    <th>Name</th>
    <th>Aktionen</th>
  </tr>
  <?php foreach ($suggestions as $suggestion): ?>
    <tr>
      <td><?php echo $suggestion['id']; ?></td>
      <td><?php echo htmlspecialchars($suggestion['type']); ?></td>
      <td><?php echo htmlspecialchars($suggestion['name']); ?></td>
      <td>
        <a href="approve_suggestion.php?id=<?php echo $suggestion['id']; ?>">Freigeben</a> | 
        <a href="delete_suggestion.php?id=<?php echo $suggestion['id']; ?>">Löschen</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<?php include 'footer.php'; ?>
