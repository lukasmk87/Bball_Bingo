<?php
session_start();
include 'header.php';
include '../db.php';

// Bingo-Felder abrufen – inklusive optionaler Teamzuordnung
try {
    $stmt = $pdo->query("SELECT bf.*, t.name as team_name FROM bingo_fields bf LEFT JOIN teams t ON bf.team_id = t.id ORDER BY bf.id DESC");
    $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Fehler beim Laden der Bingo-Felder: " . htmlspecialchars($e->getMessage()) . "</p>";
    $fields = [];
}
?>
<div class="fields-container container">
    <h1>Bingo-Felder Verwaltung</h1>
    <div class="actions">
        <a href="add_fields.php">+ Bingo-Feld hinzufügen</a>
    </div>
    <?php if(count($fields) > 0): ?>
      <table class="fields-table table">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Beschreibung</th>
                  <th>Team</th>
                  <th>Standard</th>
                  <th>Freigegeben</th>
                  <th>Aktionen</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($fields as $field): ?>
              <tr>
                  <td><?php echo htmlspecialchars($field['id']); ?></td>
                  <td><?php echo htmlspecialchars($field['description']); ?></td>
                  <td><?php echo htmlspecialchars($field['team_name'] ?: 'Nicht zugeordnet'); ?></td>
                  <td><?php echo $field['is_standard'] ? 'Ja' : 'Nein'; ?></td>
                  <td><?php echo $field['approved'] ? 'Freigegeben' : 'Ausstehend'; ?></td>
                  <td>
                      <a href="edit_field.php?id=<?php echo $field['id']; ?>" class="action-btn">Bearbeiten</a>
                      <a href="delete_field.php?id=<?php echo $field['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Bingo-Feld wirklich löschen?');">Löschen</a>
                  </td>
              </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
    <?php else: ?>
      <p>Keine Bingo-Felder gefunden.</p>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
