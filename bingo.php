<?php
session_start();
include 'db.php';
include 'header.php';

// Überprüfen, ob ein Team ausgewählt wurde
if (!isset($_SESSION['team_id'])) {
    echo "<p>Kein Team ausgewählt. Bitte wähle zuerst ein Team im Spiel-Auswahlbereich.</p>";
    exit;
}
?>
<main>
  <h1>Bingo Spiel - Viertel: <span id="quarter">1</span></h1>
  <!-- Container für das Bingo-Feld -->
  <div id="bingo-board" style="position: relative;">
    <?php
      // Initiales Laden der Bingo-Felder über fetch_bingo_fields.php
      include 'fetch_bingo_fields.php';
    ?>
  </div>
  <button id="next-quarter" onclick="nextQuarter()">Nächstes Viertel</button>
</main>
<script>
// Globale Variablen zur Kumulierung (optional)
var quarter = 1;
var cumulativeActivatedFields = 0;
var cumulativeBingos = 0;
var bingoAchieved = false; // Flag: ob im aktuellen Viertel bereits ein Bingo erzielt wurde

// Umschalten der aktiven Zellen
function toggleActive(cell) {
  cell.classList.toggle('active');
  checkBingo();
}

// Prüft, ob in der aktuellen 5x5-Matrix ein Bingo (komplette Reihe, Spalte oder Diagonale) erzielt wurde
function checkBingo() {
  if (bingoAchieved) return; // Bereits registriert
  var cells = document.querySelectorAll('.bingo-cell');
  var board = [];
  for (var i = 0; i < cells.length; i++) {
    board.push(cells[i].classList.contains('active'));
  }
  
  // Prüfe Reihen
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
  // Prüfe Spalten
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
  // Prüfe Diagonalen
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
    // Optional: Du kannst hier den Text "Bingo" in einer speziellen Zelle anzeigen
    showBingoBanner();
    // Zähle den Bingo im aktuellen Viertel (einmal pro Viertel)
    cumulativeBingos++;
  }
}

// Blendet einen Banner "Bingo" quer über das Spielfeld ein
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

// Zählt die aktivierten Felder im aktuellen Viertel
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

// Lädt neue Bingo-Felder per AJAX
function loadNewFields() {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', 'fetch_bingo_fields.php', true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      document.getElementById('bingo-board').innerHTML = xhr.responseText;
      // Reset Bingo-Flag
      bingoAchieved = false;
    } else {
      console.error("Fehler beim Laden der neuen Bingo-Felder.");
    }
  };
  xhr.send();
}

// Zeigt Optionen für Spielabschluss oder Verlängerung an, wenn das 4. Viertel erreicht ist
function showEndGameOptions() {
  var overlay = document.createElement('div');
  overlay.id = "endGameOverlay";
  overlay.style.position = "fixed";
  overlay.style.top = "0";
  overlay.style.left = "0";
  overlay.style.width = "100%";
  overlay.style.height = "100%";
  overlay.style.backgroundColor = "rgba(0,0,0,0.7)";
  overlay.style.display = "flex";
  overlay.style.flexDirection = "column";
  overlay.style.justifyContent = "center";
  overlay.style.alignItems = "center";
  overlay.style.zIndex = "1000";
  
  var message = document.createElement('p');
  message.style.color = "#fff";
  message.style.fontSize = "1.5em";
  message.style.marginBottom = "20px";
  message.innerText = "4. Viertel abgeschlossen. Bitte wählen Sie:";
  overlay.appendChild(message);
  
  var btnContainer = document.createElement('div');
  btnContainer.style.display = "flex";
  btnContainer.style.gap = "20px";
  
  var btnEnd = document.createElement('button');
  btnEnd.innerText = "Spiel beendet";
  btnEnd.style.padding = "10px 20px";
  btnEnd.style.fontSize = "1em";
  btnEnd.onclick = function() {
    document.body.removeChild(overlay);
    recordFinalScore();
    alert("Spiel vorbei!");
    window.location.href = "scoreboard.php";
  };
  
  var btnExt = document.createElement('button');
  btnExt.innerText = "Verlängerung";
  btnExt.style.padding = "10px 20px";
  btnExt.style.fontSize = "1em";
  btnExt.onclick = function() {
    document.body.removeChild(overlay);
    quarter++; // Erhöhe die Viertelzahl (jetzt 5, Verlängerung)
    document.getElementById('quarter').innerText = quarter;
    loadNewFields();
  };
  
  btnContainer.appendChild(btnEnd);
  btnContainer.appendChild(btnExt);
  overlay.appendChild(btnContainer);
  
  document.body.appendChild(overlay);
}

// Beim Klick auf "Nächstes Viertel" wird geprüft, ob ein Ende erreicht wurde oder ob ein neues Viertel gestartet wird.
function nextQuarter() {
  // Zähle die aktivierten Felder des aktuellen Viertels und addiere sie zu den kumulierten Werten
  cumulativeActivatedFields += countActiveFields();
  
  if (quarter < 4) {
    quarter++;
    document.getElementById('quarter').innerText = quarter;
    loadNewFields();
  } else if (quarter === 4) {
    // Am Ende des 4. Viertels: Optionen anzeigen
    showEndGameOptions();
  } else if (quarter === 5) {
    // Bei Verlängerung (5. Viertel) wird das Spiel nach Abschluss beendet
    cumulativeActivatedFields += countActiveFields(); // Erneut Felder zählen, falls noch Änderungen vorliegen
    recordFinalScore();
    alert("Spiel vorbei!");
    window.location.href = "scoreboard.php";
  }
}

// Sendet die finalen Ergebnisse (kumulative aktivierte Felder, Bingos, Gewinnquote und Feldquote) an update_scoreboard.php
function recordFinalScore() {
  // Gewinnquote: (Gesamtzahl Bingos / 4) * 100, bei Verlängerung werden alle Viertel zusammen gerechnet (z.B. 5 Viertel)
  var totalQuarters = quarter; // kann 4 oder 5 sein
  var win_rate = (cumulativeBingos / totalQuarters) * 100;
  // Feldquote: (Gesamtzahl aktivierter Felder / (25 * totalQuarters)) * 100
  var field_rate = (cumulativeActivatedFields / (25 * totalQuarters)) * 100;
  
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
    cursor: pointer;
  }
  .bingo-cell.active {
    background-color: #ffff99;
  }
  /* Banner, der bei Bingo angezeigt wird */
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
  /* Overlay für Endspiel-Optionen */
  #endGameOverlay {
    font-family: Arial, sans-serif;
  }
</style>
<?php include 'footer.php'; ?>
