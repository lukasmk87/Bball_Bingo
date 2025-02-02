<?php
session_start();
include 'header.php';
include '../db.php';

// Vorschläge aus der Datenbank abrufen
try {
    $stmt = $pdo->query("SELECT * FROM suggestions ORDER BY id DESC");
    $suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Fehler beim Laden der Vorschläge: " . htmlspecialchars($e->getMessage()) . "</p>";
    $suggestions = [];
}
?>
<!-- Inline-CSS für das Layout (kann alternativ in admin/style.css ausgelagert werden) -->
<style>
.suggestions-container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    border-radius: 8px;
}

.suggestions-container h1 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

.suggestions-actions {
    text-align: right;
    margin-bottom: 15px;
}

.suggestions-actions a {
    background: #28a745;
    color: #fff;
    padding: 8px 12px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: bold;
}

.suggestions-actions a:hover {
    background: #218838;
}

.suggestions-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95em;
}

.suggestions-table th,
.suggestions-table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: center;
}

.suggestions-table th {
    background-color: #343a40;
    color: #fff;
}

.suggestions-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.suggestions-table tbody tr:hover {
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

.btn-delete {
    background: #dc3545;
}

.btn-delete:hover {
    background: #c82333;
}
</style>

<div class="suggestions-container">
    <h1>Vorschlagsverwaltung</h1>
    <div class="suggestions-actions">
        <!-- Hier könntest Du z. B. einen Link zum Hinzufügen eines neuen Vorschlags einfügen -->
        <a href="add_suggestion.php">+ Vorschlag hinzufügen</a>
    </div>
    <?php if(count($suggestions) > 0): ?>
      <table class="suggestions-table">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Kategorie</th>
                  <th>Vorschlag</th>
                  <th>Aktionen</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach($suggestions as $suggestion): ?>
              <tr>
                  <td><?php echo htmlspecialchars($suggestion['id']); ?></td>
                  <td><?php echo htmlspecialchars($suggestion['type']); ?></td>
                  <td><?php echo htmlspecialchars($suggestion['name']); ?></td>
                  <td>
                      <a href="edit_suggestion.php?id=<?php echo $suggestion['id']; ?>" class="action-btn">Bearbeiten</a>
                      <a href="delete_suggestion.php?id=<?php echo $suggestion['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Vorschlag wirklich löschen?');">Löschen</a>
                  </td>
              </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
    <?php else: ?>
      <p>Keine Vorschläge gefunden.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
