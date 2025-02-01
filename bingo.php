<?php
session_start();
include 'db.php';
include 'header.php';

$debug = true; // Debug-Modus: true = Debug-Ausgaben, false = keine Debug-Ausgaben

// Prüfe, ob ein Team ausgewählt wurde
if (!isset($_SESSION['team_id'])) {
    if ($debug) {
        echo "<!-- Debug: Kein Team ausgewählt. Session: " . var_export($_SESSION, true) . " -->";
    }
    echo "<p>Kein Team ausgewählt. Bitte wähle zuerst ein Team im Spiel-Auswahlbereich.</p>";
    exit;
}

$team_id = $_SESSION['team_id'];

// Lade teambezogene Bingo-Felder
$stmt = $pdo->prepare("SELECT * FROM bingo_fields WHERE team_id = ? AND approved = 1");
$stmt->execute([$team_id]);
$teamFields = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($debug) {
    echo "<!-- Debug: teamFields count: " . count($teamFields) . " -->";
}

// Falls weniger als 25 teambezogene Felder vorhanden sind, Standardfelder laden
if (count($teamFields) < 25) {
    $stmt = $pdo->prepare("SELECT * FROM bingo_fields WHERE is_standard = 1 AND approved = 1");
    $stmt->execute();
    $standardFields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($debug) {
        echo "<!-- Debug: standardFields count: " . count($standardFields) . " -->";
    }
    $fields = array_merge($teamFields, $standardFields);
    $fields = array_slice($fields, 0, 25);
} else {
    $fields = array_slice($teamFields, 0, 25);
}

shuffle($fields);

if ($debug) {
    echo "<!-- Debug: finale Felder count: " . count($fields) . " -->";
}
?>
<main>
  <h1>Bingo Spiel - Viertel: <span id="quarter">1</span></h1>
  <div id="bingo-board">
    <?php 
    for ($i = 0; $i < 25; $i++):
      $fieldText = isset($fields[$i]) ? $fields[$i]['description'] : 'Feld ' . ($i + 1);
    ?>
      <div class="bingo-cell" onclick="toggleActive(this)"><?php echo htmlspecialchars($fieldText); ?></div>
    <?php endfor; ?>
  </div>
  <button id="next-quarter" onclick="nextQuarter()">Nächstes Viertel</button>
</main>
<script>
var bingoAchieved = false;
var quarter = 1;

function toggleActive(cell) {
  cell.classList.toggle('active');
  checkBingo();
}

function checkBingo() {
  if (bingoAchieved) return; // Gewinn wurde bereits ermittelt
  var cells = document.querySelectorAll('.bingo-cell');
  var board = [];
  for (var i = 0; i < cells.length; i++) {
    board.push(cells[i].classList.contains('active'));
  }
  
  // Prüfe alle 5 Reihen
  for (var r = 0; r < 5; r++) {
    var rowWin = true;
    for (var c = 0; c < 5; c++) {
      if (!board[r * 5 + c]) {
        rowWin = false;
        break;
      }
    }
    if (rowWin) {
      bingoAchieved = true;
      break;
    }
  }
  // Prüfe alle 5 Spalten
  if (!bingoAchieved) {
    for (var c = 0; c < 5; c++) {
      var colWin = true;
      for (var r = 0; r < 5; r++) {
        if (!board[r * 5 + c]) {
          colWin = false;
          break;
        }
      }
      if (colWin) {
        bingoAchieved = true;
        break;
      }
    }
  }
  // Prüfe Diagonale (oben links → unten rechts)
  if (!bingoAchieved) {
    var diagWin1 = true;
    for (var i = 0; i < 5; i++) {
      if (!board[i * 5 + i]) {
        diagWin1 = false;
        break;
      }
    }
    if (diagWin1) {
      bingoAchieved = true;
    }
  }
  // Prüfe Diagonale (oben rechts → unten links)
  if (!bingoAchieved) {
    var diagWin2 = true;
    for (var i = 0; i < 5; i++) {
      if (!board[i * 5 + (4 - i)]) {
        diagWin2 = false;
        break;
      }
    }
    if (diagWin2) {
      bingoAchieved = true;
    }
  }
  
  if (bingoAchieved) {
    // In der Mitte (Zelle mit Index 12) "Bingo" anzeigen
    var centerCell = document.querySelectorAll('.bingo-cell')[12];
    centerCell.innerText = "Bingo";
    // Ergebnis in Scoreboard eintragen
    recordBingo();
  }
}

function countActiveFields() {
  var cells = document.querySelectorAll('.bingo-cell');
  var count = 0;
  cells.forEach(function(cell) {
    if (cell.classList.contains('active')) {
      count++;
    }
  });
  return count;
}

function recordBingo() {
  var activeCount = countActiveFields();
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'update_scoreboard.php', true);
  xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      console.log("Scoreboard updated: " + xhr.responseText);
    }
  };
  xhr.send('active_fields=' + activeCount + '&bingos=1');
}

function nextQuarter() {
  if (quarter < 4) {
    quarter++;
    document.getElementById('quarter').innerText = quarter;
    // Für das nächste Viertel: Rücksetzen der Bingo-Überprüfung
    bingoAchieved = false;
    // Hier können auch die aktiven Zustände zurückgesetzt werden (je nach Spielregel)
    document.querySelectorAll('.bingo-cell').forEach(function(cell) {
      cell.classList.remove('active');
      // Optional: Den ursprünglichen Text wiederherstellen (wenn gewünscht)
      // Hier wird angenommen, dass der ursprüngliche Text nicht dynamisch wiederhergestellt wird.
    });
  } else {
    alert("Spiel vorbei!");
    // Hier kann abschließende Logik (z. B. Endauswertung) implementiert werden.
  }
}
</script>
<style>
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
