<?php
// admin/add_user.php
include 'header.php';
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $password, $is_admin])) {
        $message = "Benutzer erfolgreich hinzugefügt.";
    } else {
        $error = "Fehler beim Hinzufügen des Benutzers.";
    }
}
?>

<h1>Benutzer hinzufügen</h1>
<?php if (isset($message)): ?>
  <p style="color:green;"><?php echo $message; ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
  <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>
<form method="post" action="add_user.php">
  <label for="username">Benutzername:</label>
  <input type="text" name="username" id="username" required>
  <br>
  <label for="password">Passwort:</label>
  <input type="password" name="password" id="password" required>
  <br>
  <label for="is_admin">Admin Rechte:</label>
  <input type="checkbox" name="is_admin" id="is_admin" value="1">
  <br>
  <input type="submit" value="Benutzer hinzufügen">
</form>

<?php include 'footer.php'; ?>
