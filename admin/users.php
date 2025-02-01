<?php
include 'header.php';
include '../db.php';

// Alle Benutzer laden
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Benutzerverwaltung</h1>
<table>
  <tr>
    <th>ID</th>
    <th>Benutzername</th>
    <th>Status</th>
    <th>Aktionen</th>
  </tr>
  <?php foreach ($users as $user): ?>
    <tr>
      <td><?php echo $user['id']; ?></td>
      <td><?php echo htmlspecialchars($user['username']); ?></td>
      <td><?php echo $user['blocked'] ? 'Gesperrt' : 'Aktiv'; ?></td>
      <td>
        <a href="edit_user.php?id=<?php echo $user['id']; ?>">Bearbeiten</a> | 
        <?php if (!$user['blocked']): ?>
          <a href="block_user.php?id=<?php echo $user['id']; ?>">Sperren</a>
        <?php else: ?>
          <a href="unblock_user.php?id=<?php echo $user['id']; ?>">Entsperren</a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<a href="add_user.php">Benutzer hinzufügen</a>
<?php include 'footer.php'; ?>
