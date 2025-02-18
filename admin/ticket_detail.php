<?php
session_start();
include 'header.php';
include '../db.php';
include_once '../settings.php';

// Zugriff nur für Administratoren
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    die("Zugriff verweigert. Nur Administratoren dürfen Ticketdetails anzeigen.");
}

// Überprüfen, ob eine Ticket-ID übergeben wurde
if (!isset($_GET['id'])) {
    die("Keine Ticket-ID angegeben.");
}

$ticket_id = intval($_GET['id']);
$message = "";
$error = "";

// Ticket aus der Datenbank laden
try {
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$ticket) {
        die("Ticket nicht gefunden.");
    }
} catch (PDOException $e) {
    die("Fehler beim Laden des Tickets: " . htmlspecialchars($e->getMessage()));
}

// Formularverarbeitung: Aktualisierung des Ticketstatus und (optional) Speicherung einer Antwort
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    // Für dieses Beispiel speichern wir die Antwort nicht in der Datenbank,
    // könnten sie aber auch in einem separaten Feld oder in einer eigenen Reply-Tabelle speichern.
    $reply = trim($_POST['reply']);
    
    try {
        $stmt = $pdo->prepare("UPDATE tickets SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        if ($stmt->execute([$status, $ticket_id])) {
            $message = "Ticket erfolgreich aktualisiert.";
            // Optional: Hier könntest Du zusätzlich eine E-Mail an den Ticket-Ersteller versenden.
        } else {
            $error = "Fehler beim Aktualisieren des Tickets.";
        }
        // Ticket neu laden
        $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
        $stmt->execute([$ticket_id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Fehler: " . htmlspecialchars($e->getMessage());
    }
}
?>
<div class="container">
    <h1>Ticket-Details (ID: <?php echo htmlspecialchars($ticket['id']); ?>)</h1>
    
    <?php if (!empty($message)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <div class="ticket-details">
        <p><strong>Betreff:</strong> <?php echo htmlspecialchars($ticket['subject']); ?></p>
        <p><strong>Nachricht:</strong><br><?php echo nl2br(htmlspecialchars($ticket['message'])); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($ticket['status']); ?></p>
        <p><strong>Erstellt am:</strong> <?php echo htmlspecialchars($ticket['created_at']); ?></p>
        <p><strong>Letzte Aktualisierung:</strong> <?php echo htmlspecialchars($ticket['updated_at']); ?></p>
    </div>
    
    <hr>
    
    <h2>Ticket aktualisieren</h2>
    <form method="post" action="ticket_detail.php?id=<?php echo $ticket_id; ?>">
        <div class="form-group">
            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="open" <?php if ($ticket['status'] == 'open') echo 'selected'; ?>>Offen</option>
                <option value="pending" <?php if ($ticket['status'] == 'pending') echo 'selected'; ?>>In Bearbeitung</option>
                <option value="closed" <?php if ($ticket['status'] == 'closed') echo 'selected'; ?>>Geschlossen</option>
            </select>
        </div>
        <div class="form-group">
            <label for="reply">Antwort (optional):</label>
            <textarea id="reply" name="reply" rows="4" placeholder="Hier können Sie eine Antwort verfassen..."></textarea>
        </div>
        <div class="actions">
            <input type="submit" value="Ticket aktualisieren">
        </div>
    </form>
</div>
<?php include 'footer.php'; ?>
