<?php
include 'header.php';
include '../db.php';

$stmt = $pdo->query("SELECT * FROM teams");
$teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Teams</h1>
<table>
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Verein</th>
    <th>Aktionen</th>
  </tr>
  <?php foreach ($teams as $team): 
    // Vereinsname laden
    $stmtClub = $pdo->prepare("SELECT name FROM clubs WHERE id = ?");
    $stmtClub->execute([$team['club_id']]);
    $club = $stmtClub->fetch(PDO::FETCH_ASSOC);
  ?>
    <tr>
      <td><?php echo $team['id']; ?></td>
      <td><?php echo htmlspecialchars($team['name']); ?></td>
      <td><?php echo htmlspecialchars($club['name']); ?></td>
      <td>
        <a href="edit_team.php?id=<?php echo $team['id']; ?>">Bearbeiten</a> | 
        <a href="block_team.php?id=<?php echo $team['id']; ?>">Sperren</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<a href="add_team.php">Team hinzufügen</a>
<?php include 'footer.php'; ?>
