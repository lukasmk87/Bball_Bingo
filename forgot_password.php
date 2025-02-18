<?php
session_start();
require_once __DIR__.'/vendor/autoload.php';
include 'db.php';
include 'settings.php';

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailInput = trim($_POST['email']);
    // Suche den Benutzer anhand der E-Mail-Adresse
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ?");
    $stmt->execute([$emailInput]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Generiere ein neues zufälliges Passwort
        $newPassword = bin2hex(random_bytes(4)); // 8 Zeichen lang
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Aktualisiere das Passwort in der Datenbank
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($stmt->execute([$hashedPassword, $user['id']])) {
            // SMTP Einstellungen aus der settings-Tabelle abrufen
            $smtpHost = get_setting($pdo, 'smtp_host');
            $smtpPort = get_setting($pdo, 'smtp_port');
            $smtpUser = get_setting($pdo, 'smtp_username');
            $smtpPassword = get_setting($pdo, 'smtp_password');
            $smtpSecure = get_setting($pdo, 'smtp_secure'); // "tls" oder "ssl"

            // DSN für den Symfony Mailer konfigurieren
            if ($smtpSecure === 'ssl') {
                $dsn = "smtps://$smtpUser:$smtpPassword@$smtpHost:$smtpPort";
            } elseif ($smtpSecure === 'tls') {
                $dsn = "smtp://$smtpUser:$smtpPassword@$smtpHost:$smtpPort?encryption=tls";
            } else {
                $dsn = "smtp://$smtpUser:$smtpPassword@$smtpHost:$smtpPort";
            }
            $transport = Transport::fromDsn($dsn);
            $mailer = new Mailer($transport);

            $email = (new Email())
                ->from($smtpUser)
                ->to($emailInput)
                ->subject('Neues Passwort für Ihren Account')
                ->html('<p>Hallo ' . htmlspecialchars($user['username']) . ',</p>
                        <p>Ihr neues Passwort lautet: <strong>' . $newPassword . '</strong></p>
                        <p>Bitte ändern Sie es nach dem Login.</p>');

            try {
                $mailer->send($email);
                $message = "Ein neues Passwort wurde an Ihre E-Mail-Adresse gesendet.";
            } catch (TransportExceptionInterface $e) {
                $message = "Fehler beim Senden der E-Mail: " . $e->getMessage();
            }
        } else {
            $message = "Fehler beim Aktualisieren des Passworts.";
        }
    } else {
        $message = "Kein Benutzer mit dieser E-Mail-Adresse gefunden.";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Passwort vergessen</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1>Passwort vergessen</h1>
  </header>
  <main class="container">
    <?php if(!empty($message)): ?>
      <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="post" action="forgot_password.php">
      <div class="form-group">
        <label for="email">Geben Sie Ihre E-Mail-Adresse ein:</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="actions">
        <input type="submit" value="Neues Passwort anfordern">
      </div>
    </form>
  </main>
  <footer>
    <p>&copy; 2025 Bingo Basketball</p>
  </footer>
</body>
</html>
