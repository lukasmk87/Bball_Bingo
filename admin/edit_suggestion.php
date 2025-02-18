<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

include 'header.php';
include '../db.php';
include_once '../settings.php'; // include_once statt include

// Zugriff nur für Admins
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    die("Zugriff verweigert. Nur Administratoren dürfen Vorschläge bearbeiten.");
}

// Überprüfen, ob eine Vorschlags-ID übergeben wurde
if (!isset($_GET['id'])) {
    die("Keine Vorschlags-ID angegeben.");
}

$suggestion_id = intval($_GET['id']);
$message = "";
$error = "";

// Formularverarbeitung: Aktualisieren des Vorschlags
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $name = $_POST['name'];
    $approved = isset($_POST['approved']) ? 1 : 0;
    
    try {
        $stmt = $pdo->prepare("UPDATE suggestions SET type = ?, name = ?, approved = ? WHERE id = ?");
        if ($stmt->execute([$type, $name, $approved, $suggestion_id])) {
            $message = "Vorschlag erfolgreich aktualisiert.";
        } else {
            $error = "Fehler beim Aktualisieren des Vorschlags.";
        }
    } catch (PDOException $e) {
        $error = "Fehler: " . htmlspecialchars($e->getMessage());
    }
}

// Vorschlag aus der Datenbank laden
try {
    $stmt = $pdo->prepare("SELECT * FROM suggestions WHERE id = ?");
    $stmt->execute([$suggestion_id]);
    $suggestion = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$suggestion) {
        die("Vorschlag nicht gefunden.");
    }
} catch (PDOException $e) {
    die("Fehler beim Laden des Vorschlags: " . htmlspecialchars($e->getMessage()));
}
?>

<div class="suggestions-container container">
    <h1>Vorschlag bearbeiten</h1>
    <?php if (!empty($message)): ?>
        <p style="color:green;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="edit_suggestion.php?id=<?php echo $suggestion_id; ?>">
        <div class="form-group">
            <label for="type">Kategorie:</label>
            <select name="type" id="type" required>
                <option value="club" <?php if ($suggestion['type'] == 'club') echo 'selected'; ?>>Verein</option>
                <option value="team" <?php if ($suggestion['type'] == 'team') echo 'selected'; ?>>Team</option>
                <option value="field" <?php if ($suggestion['type'] == 'field') echo 'selected'; ?>>Bingo Feld</option>
                <option value="game" <?php if ($suggestion['type'] == 'game') echo 'selected'; ?>>Spiel</option>
            </select>
        </div>
        <div class="form-group">
            <label for="name">Vorschlag:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($suggestion['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="approved">
                <input type="checkbox" id="approved" name="approved" value="1" <?php if ($suggestion['approved'] == 1) echo 'checked'; ?>>
                Freigegeben
            </label>
        </div>
        <div class="actions">
            <input type="submit" value="Vorschlag aktualisieren">
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
