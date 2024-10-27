<?php
session_start();
include 'config.php';

// Sicherstellen, dass nur ein angemeldeter Admin auf die Seite zugreifen kann
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Überprüfen, ob eine Datei hochgeladen wurde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Überprüfen, ob Dateien hochgeladen wurden
    if (isset($_FILES['files']) && count($_FILES['files']['name']) > 0) {
        $files = $_FILES['files'];
        $uploadErrors = [];

        for ($i = 0; $i < count($files['name']); $i++) {
            $file_name = basename($files['name'][$i]);
            $target_file = 'uploads/' . $file_name; // Pfad zur hochgeladenen Datei
            $uploadOk = 1;
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Überprüfen, ob die Datei bereits existiert
            if (file_exists($target_file)) {
                $uploadErrors[] = "Die Datei $file_name existiert bereits.";
                $uploadOk = 0;
            }

            // Überprüfen der Dateigröße (max. 2 MB)
            if ($files['size'][$i] > 2 * 1024 * 1024) {
                $uploadErrors[] = "Die Datei $file_name ist zu groß.";
                $uploadOk = 0;
            }

            // Nur bestimmte Dateiformate erlauben
            $allowedTypes = ['jpg', 'png', 'gif', 'pdf', 'docx', 'xlsx'];
            if (!in_array($fileType, $allowedTypes)) {
                $uploadErrors[] = "Die Datei $file_name hat ein ungültiges Format.";
                $uploadOk = 0;
            }

            // Wenn alles in Ordnung ist, versuche die Datei hochzuladen
            if ($uploadOk === 1) {
                if (move_uploaded_file($files['tmp_name'][$i], $target_file)) {
                    // Datei in der Datenbank speichern
                    $stmt = $pdo->prepare("INSERT INTO files (filename, file_path, uploaded_at) VALUES (?, ?, NOW())");
                    $stmt->execute([$filename, $filePath]);
                    // Dateiname und Dateipfad einfügen
                    $_SESSION['message'] = "Die Datei $file_name wurde erfolgreich hochgeladen.";
                } else {
                    $uploadErrors[] = "Fehler beim Hochladen der Datei $file_name.";
                }
            }
        }


        // Wenn Upload-Fehler aufgetreten sind, in der Session speichern
        if (!empty($uploadErrors)) {
            $_SESSION['message'] = implode('<br>', $uploadErrors);
        }
    } else {
        $_SESSION['message'] = "Keine Dateien zum Hochladen.";
    }

    header("Location: admin_dashboard.php");
    exit;
}
