<?php
session_start();
include 'header.php';
include '../db.php';

try {
    $stmt = $pdo->query("SELECT * FROM tickets ORDER BY created_at DESC");
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Fehler beim Laden der Tickets: " . htmlspecialchars($e->getMessage()) . "</p>";
    $tickets = [];
}
?>
<div class="container">
    <h1>Support Tickets</h1>
    <?php if(count($tickets) > 0): ?>
      <table class="table">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Benutzer</th>
                  <th>Betreff</th>
                  <th>Nachricht</th>
                  <th>Status</th>
                  <th>Erstellt am</th>
                  <th>Aktionen</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach($tickets as $ticket): ?>
              <tr>
                  <td><?php echo htmlspecialchars($ticket['id']); ?></td>
                  <td><?php echo htmlspecialchars($ticket['user_id'] ?: 'Gast'); ?></td>
                  <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                  <td><?php echo nl2br(htmlspecialchars($ticket['message'])); ?></td>
                  <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                  <td><?php echo htmlspecialchars($ticket['created_at']); ?></td>
                  <td>
                    <a href="ticket_detail.php?id=<?php echo $ticket['id']; ?>" class="action-btn">Details</a>
                    <!-- Weitere Aktionen wie Status ändern, Antwort verfassen etc. können hier hinzugefügt werden -->
                  </td>
              </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
    <?php else: ?>
      <p>Keine Tickets gefunden.</p>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
