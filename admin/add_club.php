<?php
session_start();
include 'header.php';
include '../db.php';

// Vereine abrufen (für die Teamzuordnung)
try {
    $stmtClubs = $pdo->query("SELECT id, name FROM clubs ORDER BY name");
    $clubs = $stmtClubs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Fehler beim Laden der Vereine: " . htmlspecialchars($e->getMessage()) . "</p>";
    $clubs = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $club_id = $_POST['club_id'];
    $team_name = $_POST['team_name'];
    
    $stmt = $pdo->prepare("INSERT INTO teams (club_id, name) VALUES (?, ?)");
    if ($stmt->execute([$club_id, $team_name])) {
        $message = "Team erfolgreich hinzugefügt.";
    } else {
        $error = "Fehler beim Hinzufügen des Teams.";
    }
}
?>
<div class="teams-container container">
    <h1>Team hinzufügen</h1>
    <?php if(isset($message)): ?>
      <p style="color:green;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <?php if(isset($error)): ?>
      <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="add_team.php">
      <div class="form-group">
          <label for="team_name">Teamname:</label>
          <input type="text" id="team_name" name="team_name" required>
      </div>
      <div class="form-group">
          <label for="club_id">Verein auswählen:</label>
          <select id="club_id" name="club_id" required>
              <option value="">-- Verein auswählen --</option>
              <?php foreach ($clubs as $club): ?>
                  <option value="<?php echo $club['id']; ?>"><?php echo htmlspecialchars($club['name']); ?></option>
              <?php endforeach; ?>
          </select>
      </div>
      <div class="actions">
          <input type="submit" value="Team hinzufügen">
      </div>
    </form>
</div>
<?php include 'footer.php'; ?>
