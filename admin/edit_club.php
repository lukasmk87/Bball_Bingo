<?php
// admin/edit_club.php
include 'header.php';
include '../db.php';

if (!isset($_GET['id'])) {
    echo "Keine Vereins-ID angegeben.";
    exit;
}

$club_id = intval($_GET['id']);

// Vereinsdaten laden
$stmt = $pdo->prepare("SELECT * FROM clubs WHERE id = ?");
$stmt->execute([$club_id]);
$club = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$club) {
    echo "Verein nicht gefunden.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $stmt = $pdo->prepare("UPDATE clubs SET name = ? WHERE id = ?");
    if ($stmt->execute([$name, $club_id])) {
        $message = "Verein erfolgreich aktualisiert.";
        // Erneut laden
        $stmt = $pdo->prepare("SELECT * FROM clubs WHERE id = ?");
        $stmt->execute([$club_id]);
        $club = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error = "Fehler beim Aktualisieren des Vereins.";
    }
}
?>

<h1>Verein bearbeiten</h1>
<?php if (isset($message)): ?>
  <p style="color:green;"><?php echo $message; ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
  <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="post" action="edit_club.php?id=<?php echo $club_id; ?>">
  <label for="name">Vereinsname:</label>
  <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($club['name']); ?>" required>
  <br>
  <input type="submit" value="Verein aktualisieren">
</form>

<?php include 'footer.php'; ?>
