


<?php
include 'db.php';
include 'settings.php';
$site_version = get_setting($pdo, 'site_version') ?: '0.0.4';
// Aktualisiere Seitenaufrufe (nur einmal pro Seitenaufruf)
$stmt = $pdo->prepare("UPDATE global_stats SET page_views = page_views + 1 WHERE id = 1");
$stmt->execute();

// Lese die aktuellen globalen Statistiken
$stmt = $pdo->query("SELECT page_views, games_played FROM global_stats LIMIT 1");
$globalStats = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<footer>
  <p>&copy; 2025 CrossOver Podcast | Version: <?php echo htmlspecialchars($site_version); ?></p>
</footer>
