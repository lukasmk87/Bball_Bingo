<?php
session_start();
include 'header.php';
include '../db.php';

// Prüfe, ob das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export'])) {
    $selectedTeam = $_POST['team_id'];
    
    // Spiele für das ausgewählte Team abrufen
    $stmt = $pdo->prepare("SELECT id, team_id, opponent, time FROM games WHERE team_id = ?");
    $stmt->execute([$selectedTeam]);
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Setze die HTTP-Header, damit der Browser die Ausgabe als CSV-Download interpretiert
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="games_team_' . $selectedTeam . '.csv"');

    // Öffne den PHP-Output-Stream zum Schreiben der CSV-Daten
    $output = fopen('php://output', 'w');
    // Schreibe die Überschriftenzeile
    fputcsv($output, ['ID', 'Team ID', 'Gegner', 'Zeit']);
    // Schreibe die Datenzeilen
    foreach ($games as $game) {
        fputcsv($output, $game);
    }
    fclose($output);
    exit;
}

// Falls das Formular nicht abgeschickt wurde, wird das Auswahlformular angezeigt.
// Alle Teams abrufen, um das Dropdown zu füllen
$stmtTeams = $pdo->query("SELECT id, name FROM teams ORDER BY name");
$teams = $stmtTeams->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>CSV Export - Spiele</h1>
<form method="post" action="export_games.php">
    <label for="team_id">Team auswählen:</label>
    <select name="team_id" id="team_id" required>
        <option value="">-- Team auswählen --</option>
        <?php foreach ($teams as $team): ?>
            <option value="<?php echo $team['id']; ?>"><?php echo htmlspecialchars($team['name']); ?></option>
        <?php endforeach; ?>
    </select>
    <br>
    <input type="submit" name="export" value="CSV Exportieren">
</form>
<?php include 'footer.php'; ?>
