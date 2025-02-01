<?php
session_start();
include 'db.php';

$debug = true; // Debug-Modus: true = Debug-Ausgaben in das Error-Log, false = keine Debug-Ausgaben
$errorMessage = "";

// Formularverarbeitung: Diese Logik erfolgt vor jeglicher Ausgabe, um Header-Redirects zu ermöglichen.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['club']) && !empty($_POST['team']) && !empty($_POST['game'])) {
        $_SESSION['club_id'] = $_POST['club'];
        $_SESSION['team_id'] = $_POST['team'];
        $_SESSION['game_id'] = $_POST['game'];
        if ($debug) {
            error_log("DEBUG: Session gesetzt: " . print_r($_SESSION, true));
        }
        header("Location: bingo.php");
        exit;
    } else {
        $errorMessage = "Bitte alle Felder ausfüllen.";
        if ($debug) {
            error_log("DEBUG: POST-Daten: " . print_r($_POST, true));
        }
    }
}

include 'header.php';

// Lade alle Vereine aus der Datenbank für die Auswahl
$stmt = $pdo->query("SELECT * FROM clubs");
$clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($debug) {
    error_log("DEBUG: clubs count: " . count($clubs));
    error_log("DEBUG: clubs: " . print_r($clubs, true));
}
?>
<main>
  <h1>Spiel Auswahl</h1>
  <?php if (!empty($errorMessage)): ?>
    <p style="color:red;"><?php echo htmlspecialchars($errorMessage); ?></p>
  <?php endif; ?>
  <?php if (empty($clubs)): ?>
    <p>Keine Vereine gefunden. Bitte füge im Admin-Bereich zunächst Vereine hinzu.</p>
  <?php else: ?>
    <form method="post" action="game.php">
      <label for="club">Verein:</label>
      <select name="club" id="club" required onchange="fetchTeams(this.value)">
        <option value="">Wählen Sie einen Verein</option>
        <?php foreach ($clubs as $club): ?>
          <option value="<?php echo $club['id']; ?>"><?php echo htmlspecialchars($club['name']); ?></option>
        <?php endforeach; ?>
      </select>
      <br>
      <label for="team">Team:</label>
      <select name="team" id="team" required onchange="fetchGames(this.value)">
        <option value="">Wählen Sie ein Team</option>
        <!-- Teams werden per AJAX geladen -->
      </select>
      <br>
      <label for="game">Spiel (Gegner und Uhrzeit):</label>
      <select name="game" id="game" required>
        <option value="">Wählen Sie ein Spiel</option>
        <!-- Spiele werden per AJAX geladen -->
      </select>
      <br>
      <input type="submit" value="Spiel starten">
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
      // Setze das Spiele-Dropdown zurück
      document.getElementById('game').innerHTML = '<option value="">Wählen Sie ein Spiel</option>';
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
<?php include 'footer.php'; ?>
