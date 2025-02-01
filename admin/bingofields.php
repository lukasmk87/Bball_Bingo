<?php
include 'header.php';
include '../db.php';

$stmt = $pdo->query("SELECT * FROM bingo_fields");
$fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Bingo Felder</h1>
<table>
  <tr>
    <th>ID</th>
    <th>Beschreibung</th>
    <th>Team (optional)</th>
    <th>Status</th>
    <th>Aktionen</th>
  </tr>
  <?php foreach ($fields as $field): 
    $teamName = '';
    if ($field['team_id']) {
      $stmtTeam = $pdo->prepare("SELECT name FROM teams WHERE id = ?");
      $stmtTeam->execute([$field['team_id']]);
      $team = $stmtTeam->fetch(PDO::FETCH_ASSOC);
      $teamName = $team['name'];
    }
  ?>
    <tr>
      <td><?php echo $field['id']; ?></td>
      <td><?php echo htmlspecialchars($field['description']); ?></td>
      <td><?php echo htmlspecialchars($teamName); ?></td>
      <td><?php echo $field['approved'] ? 'Freigegeben' : 'Ausstehend'; ?></td>
      <td>
        <a href="edit_field.php?id=<?php echo $field['id']; ?>">Bearbeiten</a> | 
        <?php if (!$field['approved']): ?>
          <a href="approve_field.php?id=<?php echo $field['id']; ?>">Freigeben</a> | 
        <?php endif; ?>
        <a href="delete_field.php?id=<?php echo $field['id']; ?>">Löschen</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<a href="add_field.php">Bingo Feld hinzufügen</a>
<?php include 'footer.php'; ?>
