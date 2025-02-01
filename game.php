<?php
session_start();
include 'db.php';
include 'header.php';

// Vereine aus der Datenbank laden
$clubs = $pdo->query("SELECT * FROM clubs")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $club_id = $_POST['club'];
    $team_id = $_POST['team'];
    $game_id = $_POST['game'];
    
    // Auswahl in der Session speichern
    $_SESSION['club_id'] = $club_id;
    $_SESSION['team_id'] = $team_id;
    $_SESSION['game_id'] = $game_id;
    
    header("Location: bingo.php");
    exit;
}
?>
<main>
  <h1>Spiel Auswahl</h1>
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
</main>
<script>
function fetchTeams(clubId) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', 'fetch_teams.php?club_id=' + clubId, true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      document.getElementById('team').innerHTML = xhr.responseText;
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
