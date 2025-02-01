<?php
// admin/edit_field.php
include 'header.php';
include '../db.php';

if (!isset($_GET['id'])) {
    echo "Keine Bingo-Feld-ID angegeben.";
    exit;
}

$field_id = intval($_GET['id']);

// Bingo-Feld laden
$stmt = $pdo->prepare("SELECT * FROM bingo_fields WHERE id = ?");
$stmt->execute([$field_id]);
$field = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$field) {
    echo "Bingo-Feld nicht gefunden.";
    exit;
}

// Teams für die optionale Zuordnung laden
$teams = $pdo->query("SELECT * FROM teams")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $team_id = !empty($_POST['team_id']) ? $_POST['team_id'] : null;
    $is_standard = isset($_POST['is_standard']) ? 1 : 0;
    $approved = isset($_POST['approved']) ? 1 : 0;
    
    $stmt = $pdo->prepare("UPDATE bingo_fields SET description = ?, team_id = ?, is_standard = ?, approved = ? WHERE id = ?");
    if ($stmt->execute([$description, $team_id, $is_standard, $approved, $field_id])) {
        $message = "Bingo-Feld erfolgreich aktualisiert.";
        // Erneut laden
        $stmt = $pdo->prepare("SELECT * FROM bingo_fields WHERE id = ?");
        $stmt->execute([$field_id]);
        $field = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error = "Fehler beim Aktualisieren des Bingo-Felds.";
    }
}
?>

<h1>Bingo-Feld bearbeiten</h1>
<?php if (isset($message)): ?>
  <p style="color:green;"><?php echo $message; ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
  <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="post" action="edit_field.php?id=<?php echo $field_id; ?>">
  <label for="description">Beschreibung:</label>
  <input type="text" name="description" id="description" value="<?php echo htmlspecialchars($field['description']); ?>" required>
  <br>
  <label for="team_id">Team (optional):</label>
  <select name="team_id" id="team_id">
    <option value="">Kein Team</option>
    <?php foreach ($teams as $team): ?>
      <option value="<?php echo $team['id']; ?>" <?php if($field['team_id'] == $team['id']) echo 'selected'; ?>>
        <?php echo htmlspecialchars($team['name']); ?>
      </option>
    <?php endforeach; ?>
  </select>
  <br>
  <label for="is_standard">Standard Feld:</label>
  <input type="checkbox" name="is_standard" id="is_standard" value="1" <?php if($field['is_standard']) echo 'checked'; ?>>
  <br>
  <label for="approved">Freigegeben:</label>
  <input type="checkbox" name="approved" id="approved" value="1" <?php if($field['approved']) echo 'checked'; ?>>
  <br>
  <input type="submit" value="Bingo-Feld aktualisieren">
</form>

<?php include 'footer.php'; ?>
