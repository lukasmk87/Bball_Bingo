<?php
include 'db.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vorschlag speichern: type kann "club", "team" oder "field" sein
    $type = $_POST['type'];
    $name = $_POST['name'];

    $stmt = $pdo->prepare("INSERT INTO suggestions (type, name) VALUES (?, ?)");
    if ($stmt->execute([$type, $name])) {
        $message = "Vorschlag wurde gesendet.";
    } else {
        $message = "Fehler beim Senden des Vorschlags.";
    }
}
?>
<main>
  <h1>Vorschläge</h1>
  <?php if (isset($message)) echo "<p>$message</p>"; ?>
  <form method="post" action="suggestions.php">
    <label for="type">Kategorie:</label>
    <select name="type" id="type">
      <option value="club">Verein</option>
      <option value="team">Team</option>
      <option value="field">Bingo Feld</option>
    </select>
    <br>
    <label for="name">Name/Bezeichnung:</label>
    <input type="text" name="name" id="name" required>
    <br>
    <input type="submit" value="Vorschlag senden">
  </form>
</main>
<?php include 'footer.php'; ?>
