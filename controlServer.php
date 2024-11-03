<?php
function logMessage($message) {
    error_log($message, 3, '/var/log/controlServer.log');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = '/home/sfserver/.config/Epic/FactoryGame/Saved/SaveGames/blueprints/uWu Factory/';
    $fileName = basename($_FILES['file']['name']);
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['sbp', 'sbpcfg'];

    logMessage("Tentative d'upload de fichier: $fileName\n");

    // Vérification de l'extension du fichier
    if (!in_array($fileExtension, $allowedExtensions)) {
        logMessage("Extension de fichier non autorisée: $fileExtension\n");
        echo "Seuls les fichiers .sbp et .sbpcfg sont autorisés.";
        exit;
    }

    $tempFile = "/tmp/$fileName";

    // Déplacer le fichier vers un emplacement temporaire
    if (move_uploaded_file($_FILES['file']['tmp_name'], $tempFile)) {
        logMessage("Fichier déplacé vers $tempFile\n");

        // Utiliser sudo pour déplacer le fichier avec les droits de sfserver
        $command = escapeshellcmd("sudo -u sfserver mv $tempFile '$uploadDir$fileName'");
        logMessage("Exécution de la commande: $command\n");
        $output = shell_exec($command);
        $returnVar = null;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            logMessage("Erreur lors du déplacement du fichier. Code de retour: $returnVar\n");
            echo "Erreur lors du déplacement du fichier.";
        } else {
            logMessage("Fichier uploadé avec succès.\n");
            echo "Fichier uploadé avec succès.";
        }
    } else {
        logMessage("Erreur lors de l'upload du fichier temporaire.\n");
        echo "Erreur lors de l'upload.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    if (in_array($action, ['start', 'stop', 'restart'])) {
        logMessage("Exécution de l'action serveur: $action\n");
        $output = shell_exec("cd /home/sfserver && ./sfserver $action");
        logMessage("Résultat de l'action: $output\n");
        echo "Serveur $action avec succès.";
    }
}
?>
