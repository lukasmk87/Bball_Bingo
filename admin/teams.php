<?php
session_start();
include 'header.php';
include '../db.php';

// Teams abrufen – inklusive Vereinsnamen
try {
    $stmt = $pdo->query("SELECT t.*, c.name as club_name FROM teams t LEFT JOIN clubs c ON t.club_id = c.id ORDER BY t.id DESC");
    $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Fehler beim Laden der Teams: " . htmlspecialchars($e->getMessage()) . "</p>";
    $teams = [];
}
?>
<div class="teams-container container">
    <h1>Teamverwaltung</h1>
    <div class="actions">
        <a href="add_team.php">+ Team hinzufügen</a>
    </div>
    <?php if(count($teams) > 0): ?>
      <table class="teams-table table">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Teamname</th>
                  <th>Verein</th>
                  <th>Status</th>
                  <th>Aktionen</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($teams as $team): ?>
              <tr>
                  <td><?php echo htmlspecialchars($team['id']); ?></td>
                  <td><?php echo htmlspecialchars($team['name']); ?></td>
                  <td><?php echo htmlspecialchars($team['club_name'] ?: 'Nicht zugeordnet'); ?></td>
                  <td><?php echo $team['blocked'] ? 'Gesperrt' : 'Aktiv'; ?></td>
                  <td>
                      <a href="edit_team.php?id=<?php echo $team['id']; ?>" class="action-btn">Bearbeiten</a>
                      <?php if (!$team['blocked']): ?>
                          <a href="block_team.php?id=<?php echo $team['id']; ?>" class="action-btn btn-block" onclick="return confirm('Team wirklich sperren?');">Sperren</a>
                      <?php else: ?>
                          <a href="unblock_team.php?id=<?php echo $team['id']; ?>" class="action-btn btn-unblock" onclick="return confirm('Team wirklich entsperren?');">Entsperren</a>
                      <?php endif; ?>
                  </td>
              </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
    <?php else: ?>
      <p>Keine Teams gefunden.</p>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
