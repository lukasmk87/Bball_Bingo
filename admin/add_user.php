<?php
session_start();
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
<div class="users-container container">
    <h1>Benutzer hinzufügen</h1>
    <?php if(isset($message)): ?>
      <p style="color:green;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <?php if(isset($error)): ?>
      <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="add_user.php">
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
      <div class="form-group">
          <label for="is_admin">Admin Rechte:</label>
          <input type="checkbox" id="is_admin" name="is_admin" value="1">
      </div>
      <div class="actions">
          <input type="submit" value="Benutzer hinzufügen">
      </div>
    </form>
</div>
<?php include 'footer.php'; ?>
