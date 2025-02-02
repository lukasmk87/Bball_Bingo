<?php
session_start();
include 'header.php';
include '../db.php';

// Alle Vereine abrufen
try {
    $stmt = $pdo->query("SELECT * FROM clubs ORDER BY id DESC");
    $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Fehler beim Laden der Vereine: " . htmlspecialchars($e->getMessage()) . "</p>";
    $clubs = [];
}
?>
<div class="clubs-container container">
    <h1>Vereinsverwaltung</h1>
    <div class="actions">
        <a href="add_club.php">+ Verein hinzufügen</a>
    </div>
    <?php if (count($clubs) > 0): ?>
      <table class="clubs-table table">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Vereinsname</th>
                  <th>Status</th>
                  <th>Aktionen</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($clubs as $club): ?>
              <tr>
                  <td><?php echo htmlspecialchars($club['id']); ?></td>
                  <td><?php echo htmlspecialchars($club['name']); ?></td>
                  <td><?php echo $club['blocked'] ? 'Gesperrt' : 'Aktiv'; ?></td>
                  <td>
                      <a href="edit_club.php?id=<?php echo $club['id']; ?>" class="action-btn">Bearbeiten</a>
                      <?php if (!$club['blocked']): ?>
                          <a href="block_club.php?id=<?php echo $club['id']; ?>" class="action-btn btn-block" onclick="return confirm('Verein wirklich sperren?');">Sperren</a>
                      <?php else: ?>
                          <a href="unblock_club.php?id=<?php echo $club['id']; ?>" class="action-btn btn-unblock" onclick="return confirm('Verein wirklich entsperren?');">Entsperren</a>
                      <?php endif; ?>
                  </td>
              </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
    <?php else: ?>
      <p>Keine Vereine gefunden.</p>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
