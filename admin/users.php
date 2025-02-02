<?php
session_start();
include 'header.php';
include '../db.php';

// Alle Benutzer abrufen
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Fehler beim Laden der Benutzer: " . htmlspecialchars($e->getMessage()) . "</p>";
    $users = [];
}
?>
<!-- Inline-CSS für das Layout der Benutzerverwaltung -->
<style>
.users-container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    border-radius: 8px;
}

.users-container h1 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

.users-actions {
    text-align: right;
    margin-bottom: 15px;
}

.users-actions a {
    background: #28a745;
    color: #fff;
    padding: 8px 12px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: bold;
}

.users-actions a:hover {
    background: #218838;
}

.users-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95em;
}

.users-table th,
.users-table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: center;
}

.users-table th {
    background-color: #343a40;
    color: #fff;
}

.users-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.users-table tbody tr:hover {
    background-color: #f1f1f1;
}

.action-btn {
    background: #007bff;
    color: #fff;
    padding: 6px 10px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.9em;
    margin: 0 2px;
}

.action-btn:hover {
    background: #0056b3;
}

.btn-block {
    background: #dc3545;
}

.btn-block:hover {
    background: #c82333;
}

.btn-unblock {
    background: #28a745;
}

.btn-unblock:hover {
    background: #218838;
}
</style>

<div class="users-container">
    <h1>Benutzerverwaltung</h1>
    <div class="users-actions">
        <a href="add_user.php">+ Benutzer hinzufügen</a>
    </div>
    <?php if (count($users) > 0): ?>
      <table class="users-table">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Benutzername</th>
                  <th>Admin</th>
                  <th>Status</th>
                  <th>Aktionen</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($users as $user): ?>
              <tr>
                  <td><?php echo htmlspecialchars($user['id']); ?></td>
                  <td><?php echo htmlspecialchars($user['username']); ?></td>
                  <td><?php echo $user['is_admin'] ? 'Ja' : 'Nein'; ?></td>
                  <td><?php echo $user['blocked'] ? 'Gesperrt' : 'Aktiv'; ?></td>
                  <td>
                      <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="action-btn">Bearbeiten</a>
                      <?php if (!$user['blocked']): ?>
                          <a href="block_user.php?id=<?php echo $user['id']; ?>" class="action-btn btn-block" onclick="return confirm('Möchten Sie diesen Benutzer wirklich sperren?');">Sperren</a>
                      <?php else: ?>
                          <a href="unblock_user.php?id=<?php echo $user['id']; ?>" class="action-btn btn-unblock" onclick="return confirm('Möchten Sie diesen Benutzer wirklich entsperren?');">Entsperren</a>
                      <?php endif; ?>
                  </td>
              </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
    <?php else: ?>
      <p>Keine Benutzer gefunden.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
