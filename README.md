# Dateiverwaltung

Ein PHP-basiertes Dateiverwaltungs-Dashboard mit Benutzerverwaltung, Dateiuploads, Freigabefunktionen und Vorschau-Optionen. Dieses Projekt bietet Administratoren die Möglichkeit, Dateien hochzuladen, für bestimmte Benutzer freizugeben, und Benutzern den Zugriff auf freigegebene Dateien zu ermöglichen.

## Funktionen

- **Benutzerverwaltung**: Admins können Benutzer erstellen, verwalten und bestimmten Dateien Zugriff erteilen.
- **Dateiverwaltung**: Dateien können hochgeladen, gelöscht, und für Benutzer freigegeben werden.
- **Suchfunktion**: Dateien können über eine Suchleiste gefunden werden.
- **Dateivorschau und Download**: Vorschau im neuen Tab für unterstützte Dateitypen und Download-Option.
- **Temporäre Freigabe**: Dateien können für begrenzte Zeit freigegeben werden.
- **Benachrichtigungssystem**: Benutzer werden über neue Dateien benachrichtigt.


## Datenbank einrichten

- Erstelle eine neue MySQL-Datenbank und importiere die database.sql-Datei, um die benötigten Tabellen und Strukturen einzurichten.
- Aktualisiere die config.php mit deinen MySQL-Zugangsdaten.

## Dateiberechtigungen

  Stelle sicher, dass der Ordner für hochgeladene Dateien beschreibbar ist:

chmod -R 755 uploads/

## Nutzung

Als Administrator
- Dateien verwalten: Über das Dashboard können Dateien hochgeladen, gesucht und Vorschauen angezeigt werden.
- Benutzerfreigabe: Wähle eine Datei, um sie für bestimmte Benutzer für einen Zeitraum von 2 Stunden freizugeben.
- Benutzer verwalten: Neue Benutzer erstellen, löschen und verwalten.

Als Benutzer
- Zugriff auf freigegebene Dateien: Eingeloggte Benutzer sehen die freigegebenen Dateien und können diese herunterladen.
- Benachrichtigungen: Benutzer sehen neue Benachrichtigungen zu hochgeladenen oder freigegebenen Dateien.

## Technologien
- Backend: PHP
- Frontend: HTML, CSS, JavaScript
- Datenbank: MySQL

## Projektstruktur
- admin_dashboard.php – Verwaltung der Dateien und Benutzer für den Administrator.
- user_dashboard.php – Dashboard für Benutzer mit Zugriff auf freigegebene Dateien.
- actions.php – Bearbeitung von Dateiaktionen wie Freigabe, Löschen und Herunterladen.
- upload.php – Bearbeitet Dateiuploads durch den Admin.
- config.php – Verbindungsdetails zur Datenbank.
