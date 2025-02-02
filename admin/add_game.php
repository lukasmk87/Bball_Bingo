<?php
session_start();
include 'header.php';
include '../db.php';

// Teams abrufen (für Spielzuordnung)
try {
    $stmtTeams = $pdo->query("SELECT id, name FROM teams ORDER BY name");
    $teams = $stmtTeams->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Fehler beim Laden der Teams: " . htmlspecialchars($e->getMessage()) . "</p>";
    $teams = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_id = $_POST['team_id'];
    $opponent = $_POST['opponent'];
    $time = $_POST['time'];  // erwartet Format "YYYY-MM-DDTHH:MM" (HTML5 datetime-local)
    // Optional: Falls notwendig, konvertiere "T" zu einem Leerzeichen
    $time = str_replace("T", " ", $time);
    
    $stmt = $pdo->prepare("INSERT INTO games (team_id, opponent, time) VALUES (?, ?, ?)");
    if ($stmt->execute([$team_id, $opponent, $time])) {
        $message = "Spiel erfolgreich hinzugefügt.";
    } else {
        $error = "Fehler beim Hinzufügen des Spiels.";
    }
}
?>
<div class="games-container container">
    <h1>Spiel hinzufügen</h1>
    <?php if(isset($message)): ?>
      <p style="color:green;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <?php if(isset($error)): ?>
      <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="add_game.php">
      <div class="form-group">
          <label for="team_id">Team auswählen:</label>
          <select id="team_id" name="team_id" required>
              <option value="">-- Team auswählen --</option>
              <?php foreach ($teams as $team): ?>
                  <option value="<?php echo $team['id']; ?>"><?php echo htmlspecialchars($team['name']); ?></option>
              <?php endforeach; ?>
          </select>
      </div>
      <div class="form-group">
          <label for="opponent">Gegner:</label>
          <input type="text" id="opponent" name="opponent" required>
      </div>
      <div class="form-group">
          <label for="time">Datum und Uhrzeit:</label>
          <input type="datetime-local" id="time" name="time" required>
      </div>
      <div class="actions">
          <input type="submit" value="Spiel hinzufügen">
      </div>
    </form>
</div>
<?php include 'footer.php'; ?>
