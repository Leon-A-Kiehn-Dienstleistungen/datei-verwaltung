<?php
// Konfiguration der Datenbank
$host = 'localhost';
$db = 'dashboard_db';
$user = 'username'; // Ersetzen Sie dies mit Ihrem DB-Benutzernamen
$pass = 'password'; // Ersetzen Sie dies mit Ihrem DB-Passwort

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fehler bei der Verbindung zur Datenbank: " . $e->getMessage());
}
?>