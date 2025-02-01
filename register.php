<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Benutzer in der Datenbank anlegen
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if ($stmt->execute([$username, $password])) {
        header("Location: login.php");
        exit;
    } else {
        $error = "Registrierung fehlgeschlagen!";
    }
}

include 'header.php';
?>
<main>
  <h1>Registrierung</h1>
  <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <form method="post" action="register.php">
    <label for="username">Benutzername:</label>
    <input type="text" name="username" id="username" required>
    <br>
    <label for="password">Passwort:</label>
    <input type="password" name="password" id="password" required>
    <br>
    <input type="submit" value="Registrieren">
  </form>
</main>
<?php include 'footer.php'; ?>
