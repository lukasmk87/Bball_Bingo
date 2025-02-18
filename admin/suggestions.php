<?php
session_start();
include 'header.php';
include '../db.php';

// Vorschl�ge aus der Datenbank abrufen � dabei wird per LEFT JOIN auch der Teamname (falls vorhanden) geholt
try {
    $stmt = $pdo->query("SELECT s.*, t.name AS team_name FROM suggestions s LEFT JOIN teams t ON s.team_id = t.id ORDER BY s.id DESC");
    $suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Fehler beim Laden der Vorschl�ge: " . htmlspecialchars($e->getMessage()) . "</p>";
    $suggestions = [];
}
?>
<div class="suggestions-container container">
    <h1>Vorschlagsverwaltung</h1>
    <div class="actions">
        <a href="add_suggestion.php">+ Vorschlag hinzuf�gen</a>
    </div>
    <?php if (count($suggestions) > 0): ?>
      <table class="suggestions-table table">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Kategorie</th>
                  <th>Vorschlag</th>
                  <th>Team</th>
                  <th>Freigabe</th>
                  <th>Aktionen</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($suggestions as $suggestion): ?>
              <tr>
                  <td><?php echo htmlspecialchars($suggestion['id']); ?></td>
                  <td><?php echo htmlspecialchars($suggestion['type']); ?></td>
                  <td><?php echo htmlspecialchars($suggestion['name']); ?></td>
                  <td>
                    <?php 
                      if (!empty($suggestion['team_id'])) {
                          // Wenn team_id vorhanden ist, wird der Teamname angezeigt; falls kein Name vorhanden, wird die ID ausgegeben.
                          echo htmlspecialchars($suggestion['team_name'] ?: $suggestion['team_id']);
                      } else {
                          echo "Keine";
                      }
                    ?>
                  </td>
                  <td>
                      <?php if ($suggestion['approved'] == 1): ?>
                        <span style="color: green; font-weight: bold;">Freigegeben</span>
                      <?php else: ?>
                        <a href="approve_suggestion.php?id=<?php echo $suggestion['id']; ?>" class="action-btn">Freigeben</a>
                      <?php endif; ?>
                  </td>
                  <td>
                      <a href="edit_suggestion.php?id=<?php echo $suggestion['id']; ?>" class="action-btn">Bearbeiten</a>
                      <a href="delete_suggestion.php?id=<?php echo $suggestion['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Vorschlag wirklich l�schen?');">L�schen</a>
                  </td>
              </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
    <?php else: ?>
      <p>Keine Vorschl�ge gefunden.</p>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
