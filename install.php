<?php
// install.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Prüfen, ob die Installation bereits durchgeführt wurde
$installLockFile = 'install.lock';
if (file_exists($installLockFile)) {
    echo "Die Installation wurde bereits durchgeführt. Um neu zu installieren, löschen Sie bitte die Datei 'install.lock'.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formular-Daten abrufen
    $dbHost     = trim($_POST['db_host']);
    $dbName     = trim($_POST['db_name']);
    $dbUser     = trim($_POST['db_user']);
    $dbPass     = trim($_POST['db_pass']);
    $adminUser  = trim($_POST['admin_user']);
    $adminEmail = trim($_POST['admin_email']);
    $adminPass  = $_POST['admin_pass'];
    
    // Datenbankverbindung herstellen
    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo "Verbindung fehlgeschlagen: " . htmlspecialchars($e->getMessage());
        exit;
    }
    
    // Array mit den SQL-Statements zur Erstellung aller Tabellen
    $tables = [];
    
    // settings Tabelle – für systemweite Einstellungen (z. B. SMTP, Farbwerte, Version)
    $tables[] = "CREATE TABLE IF NOT EXISTS settings (
         id INT AUTO_INCREMENT PRIMARY KEY,
         name VARCHAR(100) NOT NULL UNIQUE,
         value TEXT DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    // users Tabelle – Benutzer, inkl. E-Mail
    $tables[] = "CREATE TABLE IF NOT EXISTS users (
         id INT AUTO_INCREMENT PRIMARY KEY,
         username VARCHAR(255) NOT NULL,
         email VARCHAR(255) NOT NULL,
         password VARCHAR(255) NOT NULL,
         is_admin TINYINT(1) DEFAULT 0,
         blocked TINYINT(1) DEFAULT 0,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    // clubs Tabelle – Vereine
    $tables[] = "CREATE TABLE IF NOT EXISTS clubs (
         id INT AUTO_INCREMENT PRIMARY KEY,
         name VARCHAR(255) NOT NULL,
         blocked TINYINT(1) DEFAULT 0,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    // teams Tabelle – Teams, mit Verweis auf clubs
    $tables[] = "CREATE TABLE IF NOT EXISTS teams (
         id INT AUTO_INCREMENT PRIMARY KEY,
         club_id INT DEFAULT NULL,
         name VARCHAR(255) NOT NULL,
         blocked TINYINT(1) DEFAULT 0,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    // games Tabelle – Spiele, mit Verweis auf teams
    $tables[] = "CREATE TABLE IF NOT EXISTS games (
         id INT AUTO_INCREMENT PRIMARY KEY,
         team_id INT DEFAULT NULL,
         opponent VARCHAR(255) NOT NULL,
         time DATETIME NOT NULL,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    // bingo_fields Tabelle – Bingo-Felder, optional mit Teamzuordnung
    $tables[] = "CREATE TABLE IF NOT EXISTS bingo_fields (
         id INT AUTO_INCREMENT PRIMARY KEY,
         team_id INT DEFAULT NULL,
         description TEXT NOT NULL,
         is_standard TINYINT(1) DEFAULT 0,
         approved TINYINT(1) DEFAULT 0,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    // scoreboard Tabelle – Ergebnisse, mit Verweis auf das Spiel
    $tables[] = "CREATE TABLE IF NOT EXISTS scoreboard (
         id INT AUTO_INCREMENT PRIMARY KEY,
         username VARCHAR(255) NOT NULL,
         activated_fields INT DEFAULT 0,
         bingos INT DEFAULT 0,
         win_rate DECIMAL(5,2) DEFAULT 0,
         field_rate DECIMAL(5,2) DEFAULT 0,
         game INT DEFAULT NULL,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         FOREIGN KEY (game) REFERENCES games(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    // suggestions Tabelle – Vorschläge, optional mit Teamzuordnung
    $tables[] = "CREATE TABLE IF NOT EXISTS suggestions (
         id INT AUTO_INCREMENT PRIMARY KEY,
         type VARCHAR(50) NOT NULL,
         name VARCHAR(255) NOT NULL,
         approved TINYINT(1) DEFAULT 0,
         team_id INT DEFAULT NULL,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    // tickets Tabelle – Support-Tickets
    $tables[] = "CREATE TABLE IF NOT EXISTS tickets (
         id INT AUTO_INCREMENT PRIMARY KEY,
         user_id INT DEFAULT NULL,
         subject VARCHAR(255) NOT NULL,
         message TEXT NOT NULL,
         status ENUM('open','pending','closed') DEFAULT 'open',
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
         FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    // bingo_log Tabelle – Protokoll der Bingo-Felder, die zum Gewinn geführt haben
    $tables[] = "CREATE TABLE IF NOT EXISTS bingo_log (
         id INT AUTO_INCREMENT PRIMARY KEY,
         game_id INT NOT NULL,
         winning_fields TEXT, -- JSON-kodiertes Array der Feld-IDs
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    // Tabellen erstellen
    try {
        foreach ($tables as $sql) {
            $pdo->exec($sql);
        }
    } catch (PDOException $e) {
        echo "Fehler beim Erstellen der Tabellen: " . htmlspecialchars($e->getMessage());
        exit;
    }
    
    // Erstelle den Admin-Benutzer
    $hashedPassword = password_hash($adminPass, PASSWORD_DEFAULT);
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, 1)");
        $stmt->execute([$adminUser, $adminEmail, $hashedPassword]);
    } catch (PDOException $e) {
        echo "Fehler beim Erstellen des Admin-Benutzers: " . htmlspecialchars($e->getMessage());
        exit;
    }
    
	// 50 Standard-Bingo-Felder mit Basketball-Bezug einfügen
        $standardFields = [
            "Dreier Treffer", "Zweier Treffer", "Freier Wurf Treffer", "Assist", "Offensiver Rebound",
            "Defensiver Rebound", "Steal", "Block", "Turnover", "Foul begangen",
            "Foul kassiert", "Dunk", "Fast Break", "3-Punkte-Versuch", "2-Punkte-Versuch",
            "Anspiel zum Dunk", "Defensivaktion", "Offensivaktion", "Backcourt-Press", "Pick and Roll",
            "Isolation Spielzug", "Ballverlust", "Spielmacher-Moment", "Zeitmanagement", "Buzzer Beater",
            "Alley-Oop", "Pick and Pop", "Crossover Dribble", "Double-Double", "Triple-Double",
            "Slam Dunk", "Layup", "Fadeaway Shot", "Hook Shot", "Post Move",
            "Rebound-Duell gewonnen", "Block Party", "Charge Taken", "Offensive Foul", "Technical Foul",
            "Timeout Effekt", "Clutch Performance", "Offensive Rebound Dunk", "Defensive Stop", "Pick and Roll Assist",
            "Isolation Dunk", "Fast Break Dunk", "Behind the Back Pass", "No-Look Pass", "Full-Court Press Stop"
        ];
        
        $stmtInsert = $pdo->prepare("INSERT INTO bingo_fields (team_id, description, is_standard, approved) VALUES (NULL, ?, 1, 1)");
        foreach ($standardFields as $field) {
            $stmtInsert->execute([$field]);
        }
    
    // Erstelle eine Installations-Lock-Datei, um eine erneute Installation zu verhindern
    file_put_contents($installLockFile, "Installation abgeschlossen am " . date("Y-m-d H:i:s"));
    echo "Installation erfolgreich durchgeführt. Bitte löschen oder sichern Sie die Datei install.php.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Installation – Basketball Bingo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Optional: CSS einbinden -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container">
    <h1>Installation</h1>
    <form method="post" action="install.php">
        <h2>Datenbankverbindung</h2>
        <div class="form-group">
            <label for="db_host">Datenbank Host:</label>
            <input type="text" id="db_host" name="db_host" required value="localhost">
        </div>
        <div class="form-group">
            <label for="db_name">Datenbank Name:</label>
            <input type="text" id="db_name" name="db_name" required>
        </div>
        <div class="form-group">
            <label for="db_user">Datenbank Benutzer:</label>
            <input type="text" id="db_user" name="db_user" required>
        </div>
        <div class="form-group">
            <label for="db_pass">Datenbank Passwort:</label>
            <input type="password" id="db_pass" name="db_pass" required>
        </div>
        
        <h2>Admin-Benutzer erstellen</h2>
        <div class="form-group">
            <label for="admin_user">Admin Benutzername:</label>
            <input type="text" id="admin_user" name="admin_user" required>
        </div>
        <div class="form-group">
            <label for="admin_email">Admin E-Mail-Adresse:</label>
            <input type="email" id="admin_email" name="admin_email" required>
        </div>
        <div class="form-group">
            <label for="admin_pass">Admin Passwort:</label>
            <input type="password" id="admin_pass" name="admin_pass" required>
        </div>
        
        <div class="actions">
            <input type="submit" value="Installation starten">
        </div>
    </form>
</main>
</body>
</html>
