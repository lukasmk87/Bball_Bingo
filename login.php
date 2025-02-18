<?php
session_start();
include 'db.php';
include 'header.php';

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Eingaben trimmen
    $login = trim($_POST["login"]);
    $password = $_POST["password"];
    
    if (empty($login) || empty($password)) {
        $error = "Bitte füllen Sie alle Felder aus.";
    } else {
        // Suche den Benutzer entweder per Username oder E-Mail
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            if (password_verify($password, $user['password'])) {
                // Login erfolgreich
                $_SESSION['user'] = $user;
                header("Location: index.php");
                exit;
            } else {
                $error = "Falsches Passwort.";
            }
        } else {
            $error = "Benutzer nicht gefunden.";
        }
    }
}
?>
<main class="container">
  <h1>Login</h1>
  <?php if (!empty($error)): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>
  <?php if (!empty($message)): ?>
    <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
  <?php endif; ?>
  <form method="post" action="login.php">
    <div class="form-group">
      <label for="login">Benutzername oder E-Mail:</label>
      <input type="text" id="login" name="login" required>
    </div>
    <div class="form-group">
      <label for="password">Passwort:</label>
      <input type="password" id="password" name="password" required>
    </div>
    <div class="actions">
      <input type="submit" value="Login">
    </div>
  </form>
  <p><a href="forgot_password.php">Passwort vergessen?</a></p>
</main>
<?php include 'footer.php'; ?>
