<?php
include 'db.php';

try {
    // Beispiel: Es werden 25 zufällig ausgewählte und freigegebene Bingofelder geladen.
    // Passe den Query ggf. an, wenn Du zusätzlich nach team_id filtern möchtest.
    $stmt = $pdo->query("SELECT * FROM bingo_fields WHERE approved = 1 ORDER BY RAND() LIMIT 25");
    $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Fehler beim Laden der Bingofelder: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

if ($fields) {
    foreach ($fields as $field) {
        // Ausgabe jedes Feldes mit data-field-id und onclick, um toggleActive aufzurufen.
        echo '<div class="bingo-cell" data-field-id="' . htmlspecialchars($field['id']) . '" onclick="toggleActive(this)">';
        echo htmlspecialchars($field['description']);
        echo '</div>';
    }
} else {
    echo '<p>Keine Bingofelder gefunden.</p>';
}
?>
