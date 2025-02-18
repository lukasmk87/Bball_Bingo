<?php
session_start();
include 'header.php';
include '../db.php';

// Zugriff nur für Administratoren
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    die("Zugriff verweigert. Nur Administratoren dürfen Vorschläge freigeben.");
}

// Überprüfen, ob eine Vorschlags-ID übergeben wurde
if (!isset($_GET['id'])) {
    die("Keine Vorschlags-ID angegeben.");
}

$suggestion_id = intval($_GET['id']);

try {
    // Vorschlag aus der Datenbank laden
    $stmt = $pdo->prepare("SELECT * FROM suggestions WHERE id = ?");
    $stmt->execute([$suggestion_id]);
    $suggestion = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$suggestion) {
        die("Vorschlag nicht gefunden.");
    }
    
    // Vorschlag als freigegeben markieren
    $stmtUpdate = $pdo->prepare("UPDATE suggestions SET approved = 1 WHERE id = ?");
    $stmtUpdate->execute([$suggestion_id]);
    
    // Wenn der Vorschlag vom Typ "field" ist, wird er in die Tabelle bingo_fields übernommen.
    if ($suggestion['type'] === 'field') {
        // Prüfe, ob eine Team-ID im Vorschlag vorhanden ist (optional)
        // Falls nicht vorhanden, wird NULL verwendet.
        $team_id = (isset($suggestion['team_id']) && !empty($suggestion['team_id'])) ? $suggestion['team_id'] : null;
        
        // Die Feldbeschreibung wird aus dem 'name'-Feld des Vorschlags übernommen.
        $description = $suggestion['name'];
        // Hier kannst Du optional weitere Logik einbauen, z. B. ob der Vorschlag als Standardfeld markiert werden soll.
        $is_standard = 0;
        
        $stmtInsert = $pdo->prepare("INSERT INTO bingo_fields (team_id, description, is_standard, approved) VALUES (?, ?, ?, 1)");
        $stmtInsert->execute([$team_id, $description, $is_standard]);
    }
    
    header("Location: suggestions.php?msg=Vorschlag+freigegeben");
    exit;
} catch (PDOException $e) {
    echo "Fehler: " . htmlspecialchars($e->getMessage());
}
?>
