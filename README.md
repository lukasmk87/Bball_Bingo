# Basketball Bingo

Basketball Bingo ist ein webbasiertes Spiel, das Basketball-Elemente mit dem klassischen Bingo-Prinzip kombiniert. Spieler wählen Vereine, Teams und Spiele aus und spielen ein 5x5-Bingo, das in jedem Viertel neu generiert wird. Die Ergebnisse werden in einem Scoreboard protokolliert, und es gibt einen integrierten Supportbereich mit einem Ticket-System.

---

## Inhaltsverzeichnis

- [Features](#features)
- [Architektur](#architektur)
- [Datenbankstruktur](#datenbankstruktur)
- [Installation und Setup](#installation-und-setup)
- [Nutzung](#nutzung)
  - [Frontend](#frontend)
  - [Admin-Bereich](#admin-bereich)
- [E-Mail-Versand](#e-mail-versand)
- [Technische Dokumentation](#technische-dokumentation)
- [Contributing](#contributing)
- [Lizenz](#lizenz)

---

## Features

- **Benutzer-Authentifizierung:** Registrierung, Login (oder Gastspiel) und Passwort-Reset.
- **Spielauswahl:** Auswahl von Vereinen, Teams und Spielen (nur aktuelle Spiele, die innerhalb von 3 Stunden beginnen).
- **Bingo-Spiel:** 5x5-Bingo-Felder, die in jedem Viertel neu generiert werden. Vollbildmodus und responsive Gestaltung.
- **Scoreboard:** Protokollierung der Ergebnisse inklusive Spiel-Details (Mannschaft, Gegner, Zeit) und eines Protokolls der Bingo-Ereignisse.
- **Support-Ticket-System:** Benutzer können Support-Tickets erstellen und erhalten per E-Mail Antworten.
- **Admin-Bereich:** Verwaltung von Benutzern, Vereinen, Teams, Spielen, Bingofeldern, Vorschlägen, Support-Tickets und Systemeinstellungen (inkl. SMTP-Konfiguration).
- **SMTP-Mailversand:** Integration des Symfony Mailers für den E-Mail-Versand.

---

## Architektur

Das Projekt basiert auf einer klassischen 3-Schichten-Architektur:

- **Frontend (Präsentationsschicht):** HTML, CSS (responsiv und modern) und JavaScript für dynamische Inhalte.
- **Business-Logik:** PHP-Skripte, die Sitzungsverwaltung, Datenbankzugriffe und Spiel-Logik implementieren.
- **Datenzugriff:** MySQL-Datenbank mit Tabellen für Benutzer, Vereine, Teams, Spiele, Bingofelder, Scoreboard, Vorschläge, Support-Tickets, Bingo-Logs und Systemeinstellungen.

---

## Datenbankstruktur

Die wichtigsten Tabellen sind:

- **users:** Enthält Benutzerinformationen (username, email, Passwort, Admin-Status, Blockierung).
- **clubs:** Speichert Vereinsdaten.
- **teams:** Enthält Teams, die einem Verein zugeordnet sind.
- **games:** Enthält Spieldaten (Team, Gegner, Startzeit).
- **bingo_fields:** Enthält Bingo-Felder, die team-spezifisch oder Standard sein können.
- **scoreboard:** Protokolliert Spielergebnisse (inkl. Spiel-Details und Bingo-Ereignissen).
- **suggestions:** Speichert Vorschläge zur Erweiterung (z. B. Vereins-, Team-, Bingo-Felder oder Spielvorschläge).
- **tickets:** Support-Tickets für Benutzeranfragen.
- **bingo_log:** Protokolliert, welche Bingofelder zum Gewinn geführt haben (als JSON-kodiertes Array).
- **settings:** Speichert Systemeinstellungen (z. B. Farbwerte, SMTP-Konfiguration, Version).

Zum Beispiel kannst Du die Spalten `game_details` und `event_log` in der Tabelle `scoreboard` folgendermaßen hinzufügen:

```sql
ALTER TABLE scoreboard 
  ADD COLUMN game_details VARCHAR(255) DEFAULT NULL,
  ADD COLUMN event_log TEXT DEFAULT NULL;
```

## Installation und Setup
**1. Systemvoraussetzungen:**

- PHP 7.4 oder höher
- MySQL (InnoDB empfohlen für FOREIGN KEYs)
- Webserver (Apache oder Nginx)
- Composer (für Symfony Mailer)

**2. Repository klonen:**

```bash
git clone https://github.com/DeinUsername/BasketballBingo.git
```

**3. Datenbank einrichten:**

-Erstelle eine neue MySQL-Datenbank.
-Rufe install.php im Browser auf, um alle Tabellen zu erstellen und den Admin-Benutzer anzulegen.
-Nach erfolgreicher Installation wird eine install.lock-Datei erstellt, um eine erneute Installation zu verhindern.

**4. Konfiguration:**

- Über den Admin-Bereich unter admin/site_settings.php kannst Du Systemeinstellungen wie SMTP-Parameter, Farbwerte und die Versionsnummer konfigurieren.

## Nutzung
### Frontend

- **Startseite, Registrierung und Login:**
Benutzer können sich registrieren, einloggen oder als Gast spielen.

- **Spielauswahl:**
Wähle einen Verein, ein Team und ein Spiel aus. Spiele, die mehr als 3 Stunden in der Vergangenheit liegen, sind nicht auswählbar.

- **Bingo-Spiel:**
Ein 5x5-Bingo-Feld wird in jedem Viertel neu generiert. Bei einem Bingo werden die aktiven Felder protokolliert und das Ergebnis im Scoreboard gespeichert.

- **Scoreboard:**
Zeigt die Ergebnisse der Spieler, inklusive Spiel-Details (Mannschaft, Gegner, Zeit) und eines Protokolls der Bingo-Ereignisse.

- **Support:**
Über die Support-Seite (support.php) können Benutzer Support-Tickets erstellen. E-Mail-Benachrichtigungen informieren den Admin über neue Tickets und Updates.

### Admin-Bereich
- **Dashboard:**
Übersicht über Statistiken (Benutzer, Vereine, Teams, Spiele).

- **Verwaltung:**
Seiten zur Verwaltung von Benutzern, Vereinen, Teams, Spielen, Bingofeldern, Vorschlägen und Support-Tickets.

- **Einstellungen:**
Über admin/site_settings.php werden Systemeinstellungen wie SMTP, Farbwerte und die Versionsnummer konfiguriert.

- **Ticket-System:**
Administratoren können Support-Tickets einsehen, bearbeiten und beantworten.

## E-Mail-Versand mit Symfony Mailer
Der Symfony Mailer wird verwendet, um E-Mails über SMTP zu versenden. Die SMTP-Konfiguration erfolgt über admin/site_settings.php und wird in der Tabelle settings gespeichert.

Beispiel zur Nutzung:

```php

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

$dsn = "smtp://username:password@smtp.example.com:587?encryption=tls";
$transport = Transport::fromDsn($dsn);
$mailer = new Mailer($transport);

$email = (new Email())
    ->from('username@example.com')
    ->to('recipient@example.com')
    ->subject('Betreff')
    ->html('<p>Nachrichtentext...</p>');

$mailer->send($email);
```

## Support-Ticket-System
- **Ticket-Erstellung:**
Benutzer können über die Frontend-Seite support.php ein Ticket erstellen.
- **Ticket-Verwaltung:**
Administratoren können über den Admin-Bereich (admin/tickets.php) alle Support-Tickets einsehen und bearbeiten.
- **Passwort-Reset:**
Benutzer, die ihr Passwort vergessen haben, können über ein Ticket ein neues Passwort anfordern, das per E-Mail (über Symfony Mailer) versendet wird.

## Technische Dokumentation
Das Projekt umfasst:

- **Frontend:** Moderne, responsive HTML/CSS/JavaScript-Implementierungen, die eine optimale Darstellung auf Desktop, Tablet und Smartphone gewährleisten.
- **Backend:** PHP-Skripte zur Sitzungsverwaltung, Datenbankzugriffen, Spiel-Logik, E-Mail-Versand (mit Symfony Mailer) und Support-Ticket-Verwaltung.
- **Datenbank:** Eine MySQL-Datenbank mit Tabellen für alle zentralen Funktionen des Projekts (Benutzer, Vereine, Teams, Spiele, Bingofelder, Scoreboard, Vorschläge, Tickets, Bingo-Logs, Einstellungen).
- **Full-Screen und Responsive Features:** Moderne Funktionen wie ein Vollbildmodus im Bingo-Spiel (unterstützt durch BigScreen.js) und dynamische, responsive Formularelemente.
- **Support:** Ein integriertes Ticket-System, das auch E-Mail-Benachrichtigungen ermöglicht.

## Contributing
Beiträge sind willkommen! Bitte erstelle einen Fork des Repositories, implementiere Deine Änderungen und sende einen Pull Request. Achte darauf, dass Du Dokumentation und Tests aktualisierst.

## Lizenz
Dieses Projekt steht unter der MIT License. Details findest Du in der Datei LICENSE.
