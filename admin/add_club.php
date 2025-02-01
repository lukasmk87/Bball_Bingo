<?php
// admin/add_club.php
include 'header.php';
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    
    $stmt = $pdo->prepare("INSERT INTO clubs (name) VALUES (?)");
    if ($stmt->execute([$name])) {
        $message = "Verein erfolgreich hinzugefügt.";
    } else {
        $error = "Fehler beim Hinzufügen des Vereins.";
    }
}
?>

<h1>Verein hinzufügen</h1>
<?php if (isset($message)): ?>
  <p style="color:green;"><?php echo $message; ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
  <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>
<form method="post" action="add_club.php">
  <label for="name">Vereinsname:</label>
  <input type="text" name="name" id="name" required>
  <br>
  <input type="submit" value="Verein hinzufügen">
</form>

<?php include 'footer.php'; ?>
