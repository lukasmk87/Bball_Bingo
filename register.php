<?php
session_start();
include 'db.php';
include 'header.php';

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Eingaben trimmen und speichern
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    
    // Überprüfen, ob alle Felder ausgefüllt sind
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Bitte alle Felder ausfüllen.";
    } else {
        // E-Mail validieren
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Ungültige E-Mail-Adresse.";
        } else {
            // Prüfen, ob Benutzername oder E-Mail bereits vergeben sind
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->rowCount() > 0) {
                $error = "Benutzername oder E-Mail bereits vergeben.";
            } else {
                // Passwort hashen und Benutzer einfügen
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                if ($stmt->execute([$username, $email, $hashedPassword])) {
                    $message = "Registrierung erfolgreich. Bitte loggen Sie sich ein.";
                } else {
                    $error = "Fehler bei der Registrierung. Bitte versuchen Sie es erneut.";
                }
            }
        }
    }
}
?>

<main class="container">
  <h1>Registrierung</h1>
  <?php if (!empty($error)): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>
  <?php if (!empty($message)): ?>
    <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
  <?php endif; ?>
  <form method="post" action="register.php">
    <div class="form-group">
      <label for="username">Benutzername:</label>
      <input type="text" id="username" name="username" required>
    </div>
    <div class="form-group">
      <label for="email">E-Mail-Adresse:</label>
      <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
      <label for="password">Passwort:</label>
      <input type="password" id="password" name="password" required>
    </div>
    <div class="actions">
      <input type="submit" value="Registrieren">
    </div>
  </form>
</main>

<?php include 'footer.php'; ?>
