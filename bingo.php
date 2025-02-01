<?php
session_start();
include 'db.php';
include 'settings.php';
$debug = get_debug_mode($pdo);

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
    echo "<!-- Debug: finale Felder: " . print_r($fields, true) . " -->";
}
?>
<main>
  <h1>Bingo Spiel - Viertel: <span id="quarter">1</span></h1>
  <!-- Relativer Container, damit der Banner absolut positioniert werden kann -->
  <div id="bingo-board" style="position: relative;">
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
// Globale Variablen zur Kumulierung der Ergebnisse
var cumulativeActivatedFields = 0;
var cumulativeBingos = 0;
var bingoAchieved = false; // Flag: Bingo im aktuellen Viertel bereits erzielt
var quarter = 1;           // Aktuelles Viertel (max. 4)

function toggleActive(cell) {
  cell.classList.toggle('active');
  checkBingo();
}

function checkBingo() {
  if (bingoAchieved) return; // Falls schon ein Bingo erzielt wurde
  var cells = document.querySelectorAll('.bingo-cell');
  var board = [];
  for (var i = 0; i < cells.length; i++) {
    board.push(cells[i].classList.contains('active'));
  }
  
  // Prüfung: Reihen
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
  // Prüfung: Spalten
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
  // Prüfung: Diagonale (oben links → unten rechts)
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
  // Prüfung: Diagonale (oben rechts → unten links)
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
    showBingoBanner();
    cumulativeBingos++; // Ein Bingo im aktuellen Viertel
  }
}

function showBingoBanner() {
  var board = document.getElementById('bingo-board');
  var banner = document.getElementById('bingo-banner');
  if (!banner) {
    banner = document.createElement('div');
    banner.id = 'bingo-banner';
    banner.innerText = "Bingo";
    board.appendChild(banner);
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

function nextQuarter() {
  // Vor dem Wechsel: Zähle die aktivierten Felder des aktuellen Viertels
  cumulativeActivatedFields += countActiveFields();
  
  if (quarter < 4) {
    quarter++;
    document.getElementById('quarter').innerText = quarter;
    bingoAchieved = false;
    // Entferne den Banner, falls vorhanden
    var banner = document.getElementById('bingo-banner');
    if (banner) {
      banner.parentNode.removeChild(banner);
    }
    // Setze alle aktiven Markierungen zurück
    document.querySelectorAll('.bingo-cell').forEach(function(cell) {
      cell.classList.remove('active');
    });
  } else {
    // Letztes Viertel: auch hier Felder zählen und finale Ergebnisse erfassen
    recordFinalScore();
    alert("Spiel vorbei!");
  }
}

function recordFinalScore() {
  // Gewinnquote: (Gesamtzahl Bingos / 4) * 100
  var win_rate = (cumulativeBingos / 4) * 100;
  // Feldquote: (Gesamtzahl aktivierter Felder / (25*4)) * 100
  var field_rate = (cumulativeActivatedFields / (25 * 4)) * 100;
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'update_scoreboard.php', true);
  xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      console.log("Scoreboard updated: " + xhr.responseText);
    }
  };
  xhr.send('active_fields=' + cumulativeActivatedFields + 
           '&bingos=' + cumulativeBingos + 
           '&win_rate=' + win_rate + 
           '&field_rate=' + field_rate);
}
</script>
<style>
  /* Grid-Stile für das Bingo-Feld */
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
    background-color: #fff;
  }
  .bingo-cell.active {
    background-color: #ffff99;
  }
  /* Banner, der bei Bingo quer über das Feld angezeigt wird */
  #bingo-banner {
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    transform: translateY(-50%);
    background-color: #ff6666;
    color: #fff;
    text-align: center;
    font-size: 3em;
    padding: 10px;
    z-index: 10;
  }
</style>
<?php include 'footer.php'; ?>
