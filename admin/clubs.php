<?php
include 'header.php';
include '../db.php';

$stmt = $pdo->query("SELECT * FROM clubs");
$clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Vereine</h1>
<table>
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Aktionen</th>
  </tr>
  <?php foreach ($clubs as $club): ?>
    <tr>
      <td><?php echo $club['id']; ?></td>
      <td><?php echo htmlspecialchars($club['name']); ?></td>
      <td>
        <a href="edit_club.php?id=<?php echo $club['id']; ?>">Bearbeiten</a> | 
        <a href="block_club.php?id=<?php echo $club['id']; ?>">Sperren</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<a href="add_club.php">Verein hinzufügen</a>
<?php include 'footer.php'; ?>
