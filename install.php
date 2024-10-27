<?php
// Konfiguration der Datenbank
$host = 'localhost';
$db = 'dashboard_db';
$user = 'username'; // Ersetzen Sie dies mit Ihrem DB-Benutzernamen
$pass = 'password'; // Ersetzen Sie dies mit Ihrem DB-Passwort

try {
    // Verbindung zur Datenbank herstellen
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Datenbank erstellen, falls sie nicht existiert
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $db");
    $pdo->exec("USE $db");

    // Tabellen erstellen
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS files (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        filename VARCHAR(255) NOT NULL,
        uploaded_at DATETIME NOT NULL,
        is_shared BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    // Admin-Account erstellen
    $admin_email = 'admin@example.com';
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT); // Passwort-Hash erstellen

    $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'admin')");
    $stmt->execute([$admin_email, $admin_password]);

    echo "Installation erfolgreich abgeschlossen! Der Admin-Account wurde erstellt.<br>";
    echo "Admin E-Mail: $admin_email<br>";
    echo "Admin Passwort: admin123<br>";
    echo "Bitte löschen Sie dieses Skript aus Sicherheitsgründen.<br>";

    // Erstellen der benötigten PHP-Dateien
    $files = [
        'config.php' => "<?php
// Konfiguration der Datenbank
\$host = 'localhost';
\$db = 'dashboard_db';
\$user = 'your_db_username'; // Ersetzen Sie dies mit Ihrem DB-Benutzernamen
\$pass = 'your_db_password'; // Ersetzen Sie dies mit Ihrem DB-Passwort

try {
    \$pdo = new PDO(\"mysql:host=\$host;dbname=\$db\", \$user, \$pass);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException \$e) {
    die(\"Fehler bei der Verbindung zur Datenbank: \" . \$e->getMessage());
}
?>",
        'register.php' => "<?php
session_start();
require 'config.php';

\$registration_open = true; // Setzen Sie auf false, um die Registrierung zu blockieren.

if (!\$registration_open) {
    die(\"Die Registrierung ist derzeit deaktiviert.\");
}

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$email = \$_POST['email'];
    \$password = password_hash(\$_POST['password'], PASSWORD_DEFAULT);

    try {
        \$stmt = \$pdo->prepare(\"INSERT INTO users (email, password, role) VALUES (?, ?, 'user')\");
        \$stmt->execute([\$email, \$password]);
        echo \"Registrierung erfolgreich. <a href='login.php'>Zum Login</a>\";
    } catch (PDOException \$e) {
        echo \"Fehler bei der Registrierung: \" . \$e->getMessage();
    }
}
?>

<form method=\"POST\" action=\"\">
    <input type=\"email\" name=\"email\" placeholder=\"E-Mail\" required>
    <input type=\"password\" name=\"password\" placeholder=\"Passwort\" required>
    <button type=\"submit\">Registrieren</button>
</form>",
        'login.php' => "<?php
session_start();
require 'config.php';

if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    \$email = \$_POST['email'];
    \$password = \$_POST['password'];

    \$stmt = \$pdo->prepare(\"SELECT * FROM users WHERE email = ?\");
    \$stmt->execute([\$email]);
    \$user = \$stmt->fetch();

    if (\$user && password_verify(\$password, \$user['password'])) {
        \$_SESSION['user_id'] = \$user['id'];
        \$_SESSION['role'] = \$user['role'];

        if (\$user['role'] === 'admin') {
            header(\"Location: admin_dashboard.php\");
        } else {
            header(\"Location: user_dashboard.php\");
        }
        exit;
    } else {
        echo \"Ungültige Anmeldedaten!\";
    }
}
?>

<form method=\"POST\" action=\"\">
    <input type=\"email\" name=\"email\" placeholder=\"E-Mail\" required>
    <input type=\"password\" name=\"password\" placeholder=\"Passwort\" required>
    <button type=\"submit\">Anmelden</button>
</form>",
        'admin_dashboard.php' => "<?php
session_start();
require 'config.php';

if (!isset(\$_SESSION['user_id']) || \$_SESSION['role'] !== 'admin') {
    header(\"Location: login.php\");
    exit;
}

if (\$_SERVER['REQUEST_METHOD'] === 'POST' && isset(\$_FILES['file'])) {
    \$file = \$_FILES['file'];
    \$upload_dir = 'uploads/'; // Stellen Sie sicher, dass dieses Verzeichnis existiert
    \$upload_file = \$upload_dir . basename(\$file['name']);

    if (move_uploaded_file(\$file['tmp_name'], \$upload_file)) {
        \$stmt = \$pdo->prepare(\"INSERT INTO files (user_id, filename, uploaded_at) VALUES (?, ?, NOW())\");
        \$stmt->execute([\$_SESSION['user_id'], \$file['name']]);
        echo \"Datei erfolgreich hochgeladen!\";
    } else {
        echo \"Fehler beim Hochladen der Datei.\";
    }
}

// Dateien anzeigen
\$stmt = \$pdo->prepare(\"SELECT * FROM files\");
\$stmt->execute();
\$files = \$stmt->fetchAll();

echo \"<h1>Admin-Dashboard</h1>\";
echo \"<form method='POST' enctype='multipart/form-data'>\";
echo \"<input type='file' name='file' required>\";
echo \"<button type='submit'>Hochladen</button>\";
echo \"</form>\";

echo \"<h2>Hochgeladene Dateien:</h2>\";
foreach (\$files as \$file) {
    echo \"<p>{\$file['filename']} (Hochgeladen am: {\$file['uploaded_at']})</p>\";
}
?>
<a href=\"logout.php\">Abmelden</a>",
        'user_dashboard.php' => "<?php
session_start();
require 'config.php';

if (!isset(\$_SESSION['user_id']) || \$_SESSION['role'] !== 'user') {
    header(\"Location: login.php\");
    exit;
}

// Dateien anzeigen, die für den Benutzer freigegeben sind
\$stmt = \$pdo->prepare(\"SELECT * FROM files WHERE is_shared = TRUE\");
\$stmt->execute();
\$files = \$stmt->fetchAll();

echo \"<h1>User-Dashboard</h1>\";
echo \"<h2>Freigegebene Dateien:</h2>\";

if (count(\$files) === 0) {
    echo \"<p>Keine freigegebenen Dateien gefunden.</p>\";
} else {
    foreach (\$files as \$file) {
        echo \"<p>{\$file['filename']} (Hochgeladen am: {\$file['uploaded_at']})</p>\";
    }
}
?>
<a href=\"logout.php\">Abmelden</a>",
        'logout.php' => "<?php
session_start();
session_destroy();
header(\"Location: login.php\");
exit;
?>"
    ];

    // Erstellen der Dateien
    foreach ($files as $filename => $content) {
        file_put_contents($filename, $content);
    }

    echo "Alle erforderlichen PHP-Dateien wurden erfolgreich erstellt.<br>";
} catch (PDOException $e) {
    echo "Fehler bei der Installation: " . $e->getMessage();
}
?>
