<?php
// admin/edit_team.php
include 'header.php';
include '../db.php';

if (!isset($_GET['id'])) {
    echo "Keine Team-ID angegeben.";
    exit;
}

$team_id = intval($_GET['id']);

// Teamdaten laden
$stmt = $pdo->prepare("SELECT * FROM teams WHERE id = ?");
$stmt->execute([$team_id]);
$team = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$team) {
    echo "Team nicht gefunden.";
    exit;
}

// Vereine für die Auswahl laden
$clubs = $pdo->query("SELECT * FROM clubs")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $club_id = $_POST['club_id'];
    $name = $_POST['name'];
    
    $stmt = $pdo->prepare("UPDATE teams SET club_id = ?, name = ? WHERE id = ?");
    if ($stmt->execute([$club_id, $name, $team_id])) {
        $message = "Team erfolgreich aktualisiert.";
        // Daten neu laden
        $stmt = $pdo->prepare("SELECT * FROM teams WHERE id = ?");
        $stmt->execute([$team_id]);
        $team = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error = "Fehler beim Aktualisieren des Teams.";
    }
}
?>

<h1>Team bearbeiten</h1>
<?php if (isset($message)): ?>
  <p style="color:green;"><?php echo $message; ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
  <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="post" action="edit_team.php?id=<?php echo $team_id; ?>">
  <label for="club_id">Verein:</label>
  <select name="club_id" id="club_id" required>
    <?php foreach ($clubs as $club): ?>
      <option value="<?php echo $club['id']; ?>" <?php if ($club['id'] == $team['club_id']) echo 'selected'; ?>>
        <?php echo htmlspecialchars($club['name']); ?>
      </option>
    <?php endforeach; ?>
  </select>
  <br>
  <label for="name">Teamname:</label>
  <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($team['name']); ?>" required>
  <br>
  <input type="submit" value="Team aktualisieren">
</form>

<?php include 'footer.php'; ?>
