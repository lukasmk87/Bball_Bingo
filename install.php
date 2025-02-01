<?php
// install.php – Installationsassistent für die Datenbankverbindung und das Anlegen des Admin-Kontos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Aus dem Formular erhaltene Werte
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    
    $admin_username = $_POST['admin_username'];
    $admin_password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
    
    // Speichern der Datenbankkonfiguration in config.php
    $configContent = "<?php\n";
    $configContent .= "// Datenbankkonfiguration\n";
    $configContent .= "define('DB_HOST', '$db_host');\n";
    $configContent .= "define('DB_NAME', '$db_name');\n";
    $configContent .= "define('DB_USER', '$db_user');\n";
    $configContent .= "define('DB_PASS', '$db_pass');\n";
    file_put_contents('config.php', $configContent);
    
    // Verbindung zur Datenbank herstellen
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Tabellen erstellen (Beispielhafte SQL-Befehle – ggf. erweitern)
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE,
            password VARCHAR(255),
            is_admin TINYINT(1) DEFAULT 0,
            blocked TINYINT(1) DEFAULT 0
        )");
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS clubs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100)
        )");
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS teams (
            id INT AUTO_INCREMENT PRIMARY KEY,
            club_id INT,
            name VARCHAR(100)
        )");
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS games (
            id INT AUTO_INCREMENT PRIMARY KEY,
            team_id INT,
            opponent VARCHAR(100),
            time DATETIME
        )");
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS bingo_fields (
            id INT AUTO_INCREMENT PRIMARY KEY,
            team_id INT DEFAULT NULL,
            description VARCHAR(255),
            is_standard TINYINT(1) DEFAULT 0,
            approved TINYINT(1) DEFAULT 0
        )");
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS scoreboard (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50),
            game VARCHAR(100),
            activated_fields INT,
            bingos INT,
            win_rate FLOAT,
            field_rate FLOAT
        )");
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS suggestions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(20),
            name VARCHAR(100)
        )");
        
        // Admin-Benutzer anlegen
        $stmt = $pdo->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, 1)");
        $stmt->execute([$admin_username, $admin_password]);
        
        $message = "Installation erfolgreich abgeschlossen!";
    } catch (PDOException $e) {
        $error = "Fehler bei der Installation: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Installationsassistent</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<main>
  <h1>Installationsassistent</h1>
  <?php if (isset($message)): ?>
    <p style="color:green;"><?php echo $message; ?></p>
  <?php elseif (isset($error)): ?>
    <p style="color:red;"><?php echo $error; ?></p>
  <?php endif; ?>
  <form method="post" action="install.php">
    <h2>Datenbank Konfiguration</h2>
    <label for="db_host">Host:</label>
    <input type="text" name="db_host" id="db_host" required value="localhost">
    <br>
    <label for="db_name">Datenbankname:</label>
    <input type="text" name="db_name" id="db_name" required>
    <br>
    <label for="db_user">Benutzername:</label>
    <input type="text" name="db_user" id="db_user" required>
    <br>
    <label for="db_pass">Passwort:</label>
    <input type="password" name="db_pass" id="db_pass">
    <br>
    <h2>Admin Konto</h2>
    <label for="admin_username">Admin Benutzername:</label>
    <input type="text" name="admin_username" id="admin_username" required>
    <br>
    <label for="admin_password">Admin Passwort:</label>
    <input type="password" name="admin_password" id="admin_password" required>
    <br>
    <input type="submit" value="Installation starten">
  </form>
</main>
</body>
</html>
