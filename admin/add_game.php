<?php
// admin/add_game.php
include 'header.php';
include '../db.php';

// Teams laden, damit man das zugehörige Team auswählen kann
$teams = $pdo->query("SELECT * FROM teams")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_id  = $_POST['team_id'];
    $opponent = $_POST['opponent'];
    
    // Formatierung: "T" durch Leerzeichen ersetzen
    $timeInput = $_POST['time'];
    $time = str_replace("T", " ", $timeInput);
    
    $stmt = $pdo->prepare("INSERT INTO games (team_id, opponent, time) VALUES (?, ?, ?)");
    if ($stmt->execute([$team_id, $opponent, $time])) {
        $message = "Spiel erfolgreich hinzugefügt.";
    } else {
        $error = "Fehler beim Hinzufügen des Spiels.";
    }
}
?>
<h1>Spiel hinzufügen</h1>
<?php if (isset($message)): ?>
  <p style="color:green;"><?php echo $message; ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
  <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>
<form method="post" action="add_game.php">
    <label for="team_id">Team:</label>
    <select name="team_id" id="team_id" required>
       <option value="">Wählen Sie ein Team</option>
       <?php foreach ($teams as $team): ?>
         <option value="<?php echo $team['id']; ?>"><?php echo htmlspecialchars($team['name']); ?></option>
       <?php endforeach; ?>
    </select>
    <br>
    <label for="opponent">Gegner:</label>
    <input type="text" name="opponent" id="opponent" required>
    <br>
    <label for="time">Datum und Uhrzeit:</label>
    <input type="datetime-local" name="time" id="time" required>
    <br>
    <input type="submit" value="Spiel hinzufügen">
</form>
<?php include 'footer.php'; ?>
