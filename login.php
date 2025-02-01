<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Benutzer anhand des Benutzernamens laden
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: index.php");
        exit;
    } else {
        $error = "Ungültige Anmeldedaten!";
    }
}

include 'header.php';
?>
<main>
  <h1>Login</h1>
  <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <form method="post" action="login.php">
    <label for="username">Benutzername:</label>
    <input type="text" name="username" id="username" required>
    <br>
    <label for="password">Passwort:</label>
    <input type="password" name="password" id="password" required>
    <br>
    <input type="submit" value="Login">
  </form>
</main>
<?php include 'footer.php'; ?>
