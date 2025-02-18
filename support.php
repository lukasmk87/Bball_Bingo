<?php
// support.php – für Benutzer, um ein Ticket zu erstellen
session_start();
include 'header.php';
include 'db.php';

// Formularverarbeitung
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $messageText = $_POST['message'];
    // Falls der Benutzer angemeldet ist, kann user_id gesetzt werden
    $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : NULL;
    
    $stmt = $pdo->prepare("INSERT INTO tickets (user_id, subject, message) VALUES (?, ?, ?)");
    if ($stmt->execute([$user_id, $subject, $messageText])) {
        $message = "Dein Ticket wurde erstellt. Wir melden uns zeitnah bei Dir.";
    } else {
        $message = "Fehler beim Erstellen des Tickets. Bitte versuche es erneut.";
    }
}
?>
<main class="container">
  <h1>Support Ticket erstellen</h1>
  <?php if(!empty($message)): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
  <?php endif; ?>
  <form method="post" action="support.php">
    <div class="form-group">
      <label for="subject">Betreff:</label>
      <input type="text" id="subject" name="subject" required>
    </div>
    <div class="form-group">
      <label for="message">Nachricht:</label>
      <textarea id="message" name="message" rows="6" required></textarea>
    </div>
    <div class="actions">
      <input type="submit" value="Ticket absenden">
    </div>
  </form>
</main>
<?php include 'footer.php'; ?>
