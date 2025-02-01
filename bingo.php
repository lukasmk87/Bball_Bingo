<?php
session_start();
include 'db.php';
include 'header.php';

// Annahme: Ausgewählte Team- und Spiel-ID liegen in der Session
$team_id = $_SESSION['team_id'];
$game_id = $_SESSION['game_id'];

// Zuerst werden teambezogene Bingo-Felder geladen, falls vorhanden
$stmt = $pdo->prepare("SELECT * FROM bingo_fields WHERE team_id = ? AND approved = 1");
$stmt->execute([$team_id]);
$teamFields = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Falls nicht genügend teambezogene Felder vorhanden sind, werden Standardfelder hinzugefügt
if (count($teamFields) < 25) {
    $stmt = $pdo->prepare("SELECT * FROM bingo_fields WHERE is_standard = 1 AND approved = 1");
    $stmt->execute();
    $standardFields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $fields = array_merge($teamFields, $standardFields);
    $fields = array_slice($fields, 0, 25);
} else {
    $fields = array_slice($teamFields, 0, 25);
}
shuffle($fields);
?>
<main>
  <h1>Bingo Spiel - Viertel: <span id="quarter">1</span></h1>
  <div id="bingo-board">
    <?php 
    for ($i = 0; $i < 25; $i++):
      $field = isset($fields[$i]) ? $fields[$i]['description'] : 'Feld ' . ($i + 1);
    ?>
      <div class="bingo-cell" onclick="toggleActive(this)"><?php echo htmlspecialchars($field); ?></div>
    <?php endfor; ?>
  </div>
  <button id="next-quarter" onclick="nextQuarter()">Nächstes Viertel</button>
</main>
<script>
// Umschalten der aktiven Zellen
function toggleActive(cell) {
  cell.classList.toggle('active');
}

let quarter = 1;
function nextQuarter() {
  if (quarter < 4) {
    quarter++;
    document.getElementById('quarter').innerText = quarter;
    // Beispiel: Bei jedem Viertel könnten neue Felder geladen werden.
    // Hier werden lediglich alle aktiven Zustände zurückgesetzt.
    document.querySelectorAll('.bingo-cell').forEach(function(cell) {
      cell.classList.remove('active');
    });
  } else {
    alert("Spiel vorbei!");
    // Hier könnten die Spielergebnisse (aktivierte Felder, Bingos etc.) in die Bestenliste übernommen werden.
  }
}
</script>
<style>
/* Volle Breite/Höhe – alle 25 Felder sind immer gleich groß */
#bingo-board {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 5px;
  width: 100vw;
  height: 80vh;
}
.bingo-cell {
  border: 1px solid #000;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: calc(10px + 2vmin);
  text-align: center;
  padding: 5px;
  box-sizing: border-box;
}
.bingo-cell.active {
  background-color: yellow;
}
</style>
<?php include 'footer.php'; ?>
