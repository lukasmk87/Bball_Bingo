<?php
session_start();
include 'header.php';
include '../db.php';

// Spiele abrufen – inklusive Teamnamen
try {
    $stmt = $pdo->query("SELECT g.*, t.name as team_name FROM games g LEFT JOIN teams t ON g.team_id = t.id ORDER BY g.id DESC");
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Fehler beim Laden der Spiele: " . htmlspecialchars($e->getMessage()) . "</p>";
    $games = [];
}
?>
<div class="games-container container">
    <h1>Spielverwaltung</h1>
    <div class="actions">
        <a href="add_game.php">+ Spiel hinzufügen</a>
    </div>
    <?php if(count($games) > 0): ?>
      <table class="games-table table">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Team</th>
                  <th>Gegner</th>
                  <th>Zeit</th>
                  <th>Aktionen</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach($games as $game): ?>
              <tr>
                  <td><?php echo htmlspecialchars($game['id']); ?></td>
                  <td><?php echo htmlspecialchars($game['team_name'] ?: 'Nicht zugeordnet'); ?></td>
                  <td><?php echo htmlspecialchars($game['opponent']); ?></td>
                  <td><?php echo htmlspecialchars($game['time']); ?></td>
                  <td>
                      <a href="edit_game.php?id=<?php echo $game['id']; ?>" class="action-btn">Bearbeiten</a>
                      <a href="delete_game.php?id=<?php echo $game['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Spiel wirklich löschen?');">Löschen</a>
                  </td>
              </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
    <?php else: ?>
      <p>Keine Spiele gefunden.</p>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
