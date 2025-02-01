<?php
session_start();
include 'header.php';
include '../db.php';

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import'])) {
    $selectedTeam = $_POST['team_id'];
    
    // Prüfen, ob eine Datei hochgeladen wurde
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        $error = "Fehler beim Hochladen der CSV-Datei.";
    } else {
        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
        if ($file === false) {
            $error = "Die Datei konnte nicht geöffnet werden.";
        } else {
            // Optional: Überspringe die Kopfzeile (wenn vorhanden)
            $header = fgetcsv($file);
            
            $insertStmt = $pdo->prepare("INSERT INTO games (team_id, opponent, time) VALUES (?, ?, ?)");
            $importCount = 0;
            while (($row = fgetcsv($file)) !== false) {
                // Es wird erwartet, dass die CSV mindestens 2 Spalten hat: opponent und time
                if (count($row) >= 2) {
                    $opponent = $row[0];
                    $time = $row[1];
                    if ($insertStmt->execute([$selectedTeam, $opponent, $time])) {
                        $importCount++;
                    }
                }
            }
            fclose($file);
            $message = "Es wurden " . $importCount . " Spiele importiert.";
        }
    }
}

// Teams abrufen, um das Dropdown zu füllen
$stmtTeams = $pdo->query("SELECT id, name FROM teams ORDER BY name");
$teams = $stmtTeams->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>CSV Import - Spiele</h1>
<?php if ($message): ?>
    <p style="color:green;"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>
<?php if ($error): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>
<form method="post" action="import_games.php" enctype="multipart/form-data">
    <label for="team_id">Team auswählen:</label>
    <select name="team_id" id="team_id" required>
        <option value="">-- Team auswählen --</option>
        <?php foreach ($teams as $team): ?>
            <option value="<?php echo $team['id']; ?>"><?php echo htmlspecialchars($team['name']); ?></option>
        <?php endforeach; ?>
    </select>
    <br>
    <label for="csv_file">CSV-Datei auswählen:</label>
    <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
    <br>
    <input type="submit" name="import" value="CSV Importieren">
</form>
<?php include 'footer.php'; ?>
