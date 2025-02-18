<?php
// Aktivieren des Output Buffering – das verhindert unerwünschte Ausgaben vor dem Redirect
ob_start();

session_start();
include 'db.php';
include 'header.php';

// Vereine aus der Datenbank abrufen
try {
    $stmt = $pdo->query("SELECT * FROM clubs ORDER BY name");
    $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Hier sollte keine Ausgabe erfolgen – stattdessen loggen wir den Fehler
    error_log("Fehler beim Laden der Vereine: " . $e->getMessage());
    $clubs = [];
}

// Formularverarbeitung – muss vor jeglicher Ausgabe erfolgen!
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['club']) && !empty($_POST['team']) && !empty($_POST['game'])) {
        $_SESSION['club_id'] = $_POST['club'];
        $_SESSION['team_id'] = $_POST['team'];
        $_SESSION['game_id'] = $_POST['game'];
        header("Location: bingo.php");
        exit;
    } else {
        $errorMessage = "Bitte alle Felder ausfüllen.";
    }
}
?>
<main>
  <h1>Spiel Auswahl</h1>
  <?php if(isset($errorMessage)): ?>
    <p style="color:red;"><?php echo htmlspecialchars($errorMessage); ?></p>
  <?php endif; ?>
  <?php if(empty($clubs)): ?>
    <p>Keine Vereine gefunden. Bitte füge zuerst Vereine im Admin-Bereich hinzu.</p>
  <?php else: ?>
    <form method="post" action="game.php">
      <div class="form-group">
         <label for="club">Verein:</label>
         <select name="club" id="club" required onchange="fetchTeams(this.value)">
           <option value="">-- Verein auswählen --</option>
           <?php foreach ($clubs as $club): ?>
             <option value="<?php echo $club['id']; ?>"><?php echo htmlspecialchars($club['name']); ?></option>
           <?php endforeach; ?>
         </select>
      </div>
      <div class="form-group">
         <label for="team">Team:</label>
         <select name="team" id="team" required onchange="fetchGames(this.value)">
           <option value="">-- Team auswählen --</option>
         </select>
      </div>
      <div class="form-group">
         <label for="game">Spiel (Gegner &amp; Uhrzeit):</label>
         <select name="game" id="game" required>
           <option value="">-- Spiel auswählen --</option>
         </select>
      </div>
      <div class="actions">
         <input type="submit" value="Spiel starten">
      </div>
    </form>
  <?php endif; ?>
</main>
<script>
function fetchTeams(clubId) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', 'fetch_teams.php?club_id=' + clubId, true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      document.getElementById('team').innerHTML = xhr.responseText;
      document.getElementById('game').innerHTML = '<option value="">-- Spiel auswählen --</option>';
    }
  };
  xhr.send();
}

function fetchGames(teamId) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', 'fetch_games.php?team_id=' + teamId, true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      document.getElementById('game').innerHTML = xhr.responseText;
    }
  };
  xhr.send();
}
</script>
<?php
// Schließen des Output Buffers (die Ausgabe wird jetzt erst gesendet)
ob_end_flush();
include 'footer.php';
?>
