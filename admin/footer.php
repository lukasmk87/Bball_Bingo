<?php
include '../db.php';
include '../settings.php';
$site_version = get_setting($pdo, 'site_version') ?: '1.0.0';
?>
<footer>
  <p>&copy; 2025 Admin Dashboard | Version: <?php echo htmlspecialchars($site_version); ?></p>
</footer>
</div> <!-- Schließt den Content-Bereich -->
</body>
</html>
