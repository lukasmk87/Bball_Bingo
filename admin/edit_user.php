<?php
// admin/edit_user.php
include 'header.php';
include '../db.php';

if (!isset($_GET['id'])) {
    echo "Keine Benutzer-ID angegeben.";
    exit;
}

$user_id = intval($_GET['id']);

// Aktuelle Benutzerinformationen laden
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Benutzer nicht gefunden.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    
    // Optional: Passwort aktualisieren, falls ein neues eingegeben wurde
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ?, is_admin = ? WHERE id = ?");
        $result = $stmt->execute([$username, $password, $is_admin, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, is_admin = ? WHERE id = ?");
        $result = $stmt->execute([$username, $is_admin, $user_id]);
    }
    
    if ($result) {
        $message = "Benutzer erfolgreich aktualisiert.";
        // Aktualisierte Daten erneut laden
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error = "Fehler beim Aktualisieren des Benutzers.";
    }
}
?>

<h1>Benutzer bearbeiten</h1>
<?php if (isset($message)): ?>
  <p style="color:green;"><?php echo $message; ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
  <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="post" action="edit_user.php?id=<?php echo $user_id; ?>">
  <label for="username">Benutzername:</label>
  <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
  <br>
  <label for="password">Neues Passwort (optional):</label>
  <input type="password" name="password" id="password">
  <br>
  <label for="is_admin">Admin Rechte:</label>
  <input type="checkbox" name="is_admin" id="is_admin" value="1" <?php if($user['is_admin']) echo 'checked'; ?>>
  <br>
  <input type="submit" value="Benutzer aktualisieren">
</form>

<?php include 'footer.php'; ?>
