<?php
// admin/add_field.php
include 'header.php';
include '../db.php';

// Teams laden für die optionale Zuordnung
$teams = $pdo->query("SELECT * FROM teams")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $team_id = (!empty($_POST['team_id'])) ? $_POST['team_id'] : null;
    $is_standard = isset($_POST['is_standard']) ? 1 : 0;
    // Als Admin direkt freigeschaltet:
    $approved = 1;
    
    $stmt = $pdo->prepare("INSERT INTO bingo_fields (team_id, description, is_standard, approved) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$team_id, $description, $is_standard, $approved])) {
        $message = "Bingo Feld erfolgreich hinzugefügt.";
    } else {
        $error = "Fehler beim Hinzufügen des Bingo Feldes.";
    }
}
?>

<h1>Bingo Feld hinzufügen</h1>
<?php if (isset($message)): ?>
  <p style="color:green;"><?php echo $message; ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
  <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>
<form method="post" action="add_fields.php">
  <label for="description">Beschreibung:</label>
  <input type="text" name="description" id="description" required>
  <br>
  <label for="team_id">Team (optional):</label>
  <select name="team_id" id="team_id">
    <option value="">Kein Team</option>
    <?php foreach ($teams as $team): ?>
      <option value="<?php echo $team['id']; ?>"><?php echo htmlspecialchars($team['name']); ?></option>
    <?php endforeach; ?>
  </select>
  <br>
  <label for="is_standard">Standard Feld:</label>
  <input type="checkbox" name="is_standard" id="is_standard" value="1">
  <br>
  <input type="submit" value="Bingo Feld hinzufügen">
</form>

<?php include 'footer.php'; ?>
