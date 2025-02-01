<?php
// admin/add_team.php
include 'header.php';
include '../db.php';

// Vereine laden für die Auswahl
$clubs = $pdo->query("SELECT * FROM clubs")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $club_id = $_POST['club_id'];
    $name = $_POST['name'];
    
    $stmt = $pdo->prepare("INSERT INTO teams (club_id, name) VALUES (?, ?)");
    if ($stmt->execute([$club_id, $name])) {
        $message = "Team erfolgreich hinzugefügt.";
    } else {
        $error = "Fehler beim Hinzufügen des Teams.";
    }
}
?>

<h1>Team hinzufügen</h1>
<?php if (isset($message)): ?>
  <p style="color:green;"><?php echo $message; ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
  <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>
<form method="post" action="add_team.php">
  <label for="club_id">Verein:</label>
  <select name="club_id" id="club_id" required>
    <option value="">Wählen Sie einen Verein</option>
    <?php foreach ($clubs as $club): ?>
      <option value="<?php echo $club['id']; ?>"><?php echo htmlspecialchars($club['name']); ?></option>
    <?php endforeach; ?>
  </select>
  <br>
  <label for="name">Teamname:</label>
  <input type="text" name="name" id="name" required>
  <br>
  <input type="submit" value="Team hinzufügen">
</form>

<?php include 'footer.php'; ?>
