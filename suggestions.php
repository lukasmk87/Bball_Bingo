<?php
include 'db.php';
include 'header.php';

$message = "";
$error = "";

// Alle Vereine abrufen (für Team-Vorschläge)
try {
    $stmtClubs = $pdo->query("SELECT id, name FROM clubs ORDER BY name");
    $clubs = $stmtClubs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $clubs = [];
    $error = "Fehler beim Laden der Vereine: " . $e->getMessage();
}

// Alle Teams abrufen (für Bingo-Feld- und Spiel-Vorschläge)
try {
    $stmtTeams = $pdo->query("SELECT id, name FROM teams ORDER BY name");
    $teams = $stmtTeams->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $teams = [];
    $error = "Fehler beim Laden der Teams: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    
    if ($type === 'game') {
        // Spiel-Vorschlag: Erwarte Team-Zuordnung, Gegner und Datum/Uhrzeit
        $selectedTeam = $_POST['selected_team'];
        $opponent = $_POST['opponent'];
        $time = $_POST['time']; // Format: "YYYY-MM-DDTHH:MM"
        $time = str_replace("T", " ", $time);  // Für MySQL (z.B. "2025-05-20 18:30")
        
        $stmt = $pdo->prepare("INSERT INTO games (team_id, opponent, time) VALUES (?, ?, ?)");
        if ($stmt->execute([$selectedTeam, $opponent, $time])) {
            $message = "Spielvorschlag wurde erfolgreich hinzugefügt.";
        } else {
            $error = "Fehler beim Hinzufügen des Spielvorschlags.";
        }
    } elseif ($type === 'team') {
        // Team-Vorschlag: Erwarte Teamnamen und zugehörigen Verein
        $team_name = $_POST['team_name'];
        $club_id = $_POST['club_id'];
        // Hier speichern wir den Vorschlag als kombinierte Information.
        $value = $team_name . " (Verein-ID: " . $club_id . ")";
        $stmt = $pdo->prepare("INSERT INTO suggestions (type, name) VALUES (?, ?)");
        if ($stmt->execute([$type, $value])) {
            $message = "Team-Vorschlag wurde gesendet.";
        } else {
            $error = "Fehler beim Senden des Team-Vorschlags.";
        }
    } elseif ($type === 'field') {
        // Bingo-Feld-Vorschlag: Erwarte Feldbeschreibung, optional Team-Zuordnung und Checkbox für Standardfeld
        $field_description = $_POST['field_description'];
        $optional_team_id = isset($_POST['field_team_id']) && $_POST['field_team_id'] != "" ? $_POST['field_team_id'] : null;
        $is_standard = isset($_POST['is_standard']) ? 1 : 0;
        // Speichere den Vorschlag als kombinierte Information
        $value = $field_description;
        if ($optional_team_id) {
            $value .= " (Team-ID: " . $optional_team_id . ")";
        }
        if ($is_standard) {
            $value .= " [Standardfeld]";
        }
        $stmt = $pdo->prepare("INSERT INTO suggestions (type, name) VALUES (?, ?)");
        if ($stmt->execute([$type, $value])) {
            $message = "Bingo-Feld-Vorschlag wurde gesendet.";
        } else {
            $error = "Fehler beim Senden des Bingo-Feld-Vorschlags.";
        }
    } else {
        // Vereins-Vorschlag: Einfacher Text (Name/Bezeichnung)
        $name = $_POST['name'];
        $stmt = $pdo->prepare("INSERT INTO suggestions (type, name) VALUES (?, ?)");
        if ($stmt->execute([$type, $name])) {
            $message = "Verein-Vorschlag wurde gesendet.";
        } else {
            $error = "Fehler beim Senden des Vereins-Vorschlags.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Vorschläge</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main>
    <h1>Vorschläge</h1>
    <?php 
      if (!empty($message)) echo "<p style='color:green;'>$message</p>";
      if (!empty($error)) echo "<p style='color:red;'>$error</p>";
    ?>
    <form method="post" action="suggestions.php" id="suggestionForm">
        <label for="type">Kategorie:</label>
        <select name="type" id="type" onchange="toggleFields()" required>
            <option value="club">Verein</option>
            <option value="team">Team</option>
            <option value="field">Bingo Feld</option>
            <option value="game">Spiel</option>
        </select>
        <br>
        <!-- Felder für Vereins-Vorschlag -->
        <div id="clubFields" style="display:none;">
            <label for="name">Vereinsname:</label>
            <input type="text" name="name" id="name">
        </div>
        <!-- Felder für Team-Vorschlag -->
        <div id="teamFields" style="display:none;">
            <label for="team_name">Teamname:</label>
            <input type="text" name="team_name" id="team_name">
            <br>
            <label for="club_id">Verein auswählen:</label>
            <select name="club_id" id="club_id">
                <option value="">-- Verein auswählen --</option>
                <?php foreach ($clubs as $club): ?>
                    <option value="<?php echo $club['id']; ?>"><?php echo htmlspecialchars($club['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- Felder für Bingo-Feld-Vorschlag -->
        <div id="fieldFields" style="display:none;">
            <label for="field_description">Bingo-Feld Beschreibung:</label>
            <input type="text" name="field_description" id="field_description">
            <br>
            <label for="field_team_id">Team (optional):</label>
            <select name="field_team_id" id="field_team_id">
                <option value="">Kein Team</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?php echo $team['id']; ?>"><?php echo htmlspecialchars($team['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="is_standard">Als Standardfeld markieren:</label>
            <input type="checkbox" name="is_standard" id="is_standard" value="1">
        </div>
        <!-- Felder für Spiel-Vorschlag -->
        <div id="gameFields" style="display:none;">
            <label for="selected_team">Team auswählen:</label>
            <select name="selected_team" id="selected_team">
                <option value="">-- Team auswählen --</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?php echo $team['id']; ?>"><?php echo htmlspecialchars($team['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="opponent">Gegner:</label>
            <input type="text" name="opponent" id="opponent">
            <br>
            <label for="time">Datum und Uhrzeit:</label>
            <input type="datetime-local" name="time" id="time">
        </div>
        <br>
        <input type="submit" value="Vorschlag senden">
    </form>
</main>
<script>
// Funktion zum Umschalten der Formularfelder basierend auf der ausgewählten Kategorie
function toggleFields() {
    var type = document.getElementById('type').value;
    
    // Alle Bereiche zunächst ausblenden
    document.getElementById('clubFields').style.display = 'none';
    document.getElementById('teamFields').style.display = 'none';
    document.getElementById('fieldFields').style.display = 'none';
    document.getElementById('gameFields').style.display = 'none';
    
    // Required-Attribute zurücksetzen
    document.getElementById('name').required = false;
    document.getElementById('team_name').required = false;
    document.getElementById('club_id').required = false;
    document.getElementById('field_description').required = false;
    document.getElementById('selected_team').required = false;
    document.getElementById('opponent').required = false;
    document.getElementById('time').required = false;
    
    if (type === 'club') {
        document.getElementById('clubFields').style.display = 'block';
        document.getElementById('name').required = true;
    } else if (type === 'team') {
        document.getElementById('teamFields').style.display = 'block';
        document.getElementById('team_name').required = true;
        document.getElementById('club_id').required = true;
    } else if (type === 'field') {
        document.getElementById('fieldFields').style.display = 'block';
        document.getElementById('field_description').required = true;
    } else if (type === 'game') {
        document.getElementById('gameFields').style.display = 'block';
        document.getElementById('selected_team').required = true;
        document.getElementById('opponent').required = true;
        document.getElementById('time').required = true;
    }
}
</script>
<?php include 'footer.php'; ?>
