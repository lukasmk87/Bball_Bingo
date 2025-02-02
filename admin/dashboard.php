<?php
session_start();
include 'header.php';
include '../db.php';

// Beispielhafte Statistiken ermitteln – passe diese Abfragen nach Deinen Bedürfnissen an
try {
    // Gesamtanzahl der Benutzer
    $stmtUsers = $pdo->query("SELECT COUNT(*) AS total FROM users");
    $userCount = $stmtUsers->fetch(PDO::FETCH_ASSOC)['total'];

    // Gesamtanzahl der Vereine
    $stmtClubs = $pdo->query("SELECT COUNT(*) AS total FROM clubs");
    $clubCount = $stmtClubs->fetch(PDO::FETCH_ASSOC)['total'];

    // Gesamtanzahl der Teams
    $stmtTeams = $pdo->query("SELECT COUNT(*) AS total FROM teams");
    $teamCount = $stmtTeams->fetch(PDO::FETCH_ASSOC)['total'];

    // Gesamtanzahl der Spiele
    $stmtGames = $pdo->query("SELECT COUNT(*) AS total FROM games");
    $gameCount = $stmtGames->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    $userCount = $clubCount = $teamCount = $gameCount = 0;
}
?>
<div class="dashboard-container container">
    <h1>Admin Dashboard</h1>
    
    <div class="stats-panel">
        <div class="stat-item">
            <h2>Benutzer</h2>
            <p><?php echo htmlspecialchars($userCount); ?> Benutzer</p>
            <p><a href="users.php">Details ansehen</a></p>
        </div>
        <div class="stat-item">
            <h2>Vereine</h2>
            <p><?php echo htmlspecialchars($clubCount); ?> Vereine</p>
            <p><a href="clubs.php">Details ansehen</a></p>
        </div>
        <div class="stat-item">
            <h2>Teams</h2>
            <p><?php echo htmlspecialchars($teamCount); ?> Teams</p>
            <p><a href="teams.php">Details ansehen</a></p>
        </div>
        <div class="stat-item">
            <h2>Spiele</h2>
            <p><?php echo htmlspecialchars($gameCount); ?> Spiele</p>
            <p><a href="games.php">Details ansehen</a></p>
        </div>
    </div>
    
    <div class="other-links">
        <a href="users.php">Benutzerverwaltung</a> |
        <a href="clubs.php">Vereinsverwaltung</a> |
        <a href="teams.php">Teamverwaltung</a> |
        <a href="games.php">Spielverwaltung</a> |
        <a href="bingofields.php">Bingo-Felder</a> |
        <a href="suggestions.php">Vorschläge</a> |
        <a href="statistics.php">Statistiken</a> |
        <a href="site_settings.php">Einstellungen</a> |
        <a href="../index.php">Zum Frontend</a>
    </div>
</div>
<?php include 'footer.php'; ?>
