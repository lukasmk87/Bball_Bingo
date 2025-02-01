<?php
// admin/edit_game.php
include 'header.php';
include '../db.php';

if (!isset($_GET['id'])) {
    echo "Keine Spiel-ID angegeben.";
    exit;
}

$game_id = intval($_GET['id']);

// Spiel laden
$stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$game_id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    echo "Spiel nicht gefunden.";
    exit;
}

// Teams laden
$teams = $pdo->query("SELECT * FROM teams")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_id = $_POST['team_id'];
    $opponent = $_POST['opponent'];
    $timeInput = $_POST['time'];
    $time = str_replace("T", " ", $timeInput);
    
    $stmt = $pdo->prepare("UPDATE games SET team_id = ?, opponent = ?, time = ? WHERE id = ?");
    if ($stmt->execute([$team_id, $opponent, $time, $game_id])) {
        $message = "Spiel erfolgreich aktualisiert.";
        // Spiel erneut laden
        $stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
        $stmt->execute([$game_id]);
        $game = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error = "Fehler beim Aktualisieren des Spiels.";
    }
}
?>
<h1>Spiel bearbeiten</h1>
<?php if (isset($message)): ?>
  <p style="color:green;"><?php echo $message; ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
  <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>
<form method="post" action="edit_game.php?id=<?php echo $game_id; ?>">
    <label for="team_id">Team:</label>
    <select name="team_id" id="team_id" required>
       <?php foreach ($teams as $team): ?>
         <option value="<?php echo $team['id']; ?>" <?php if ($team['id'] == $game['team_id']) echo 'selected'; ?>>
             <?php echo htmlspecialchars($team['name']); ?>
         </option>
       <?php endforeach; ?>
    </select>
    <br>
    <label for="opponent">Gegner:</label>
    <input type="text" name="opponent" id="opponent" value="<?php echo htmlspecialchars($game['opponent']); ?>" required>
    <br>
    <label for="time">Datum und Uhrzeit:</label>
    <?php
       // Für das Input-Feld im Format "YYYY-MM-DDTHH:MM" umwandeln
       $timeForInput = str_replace(" ", "T", substr($game['time'], 0, 16));
    ?>
    <input type="datetime-local" name="time" id="time" value="<?php echo $timeForInput; ?>" required>
    <br>
    <input type="submit" value="Spiel aktualisieren">
</form>
<?php include 'footer.php'; ?>
