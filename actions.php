<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['action'])) {
        die('Keine Aktion angegeben.');
    }

    $action = $_POST['action'];

    if ($action === 'stop_share') {
        $fileId = $_POST['file_id'];
        $stmt = $pdo->prepare("UPDATE file_permissions SET is_active = FALSE WHERE file_id = ? AND user_email = ?");
        try {
            $stmt->execute([$fileId, $_SESSION['email']]);
            header("Location: user_dashboard.php?success=Freigabe erfolgreich gestoppt.");
        } catch (Exception $e) {
            header("Location: user_dashboard.php?error=Fehler beim Stoppen der Freigabe.");
        }
        exit();
    }

    if ($action === 'preview') {
        $fileId = $_POST['file_id'];
        header("Location: preview.php?file_id=" . $fileId);
        exit();
    }
}
?>
