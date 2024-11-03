<?php
// En-têtes CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

function logMessage($message, &$logOutput) {
    error_log($message, 3, '/var/log/controlServer.log');
    $logOutput .= $message . "\n";
}

$logOutput = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = "/home/sfserver/.config/Epic/FactoryGame/Saved/SaveGames/blueprints/uWu\ Factory/";
    $fileName = basename($_FILES['file']['name']);
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['sbp', 'sbpcfg'];

    logMessage("Tentative d'upload de fichier: $fileName", $logOutput);

    // Vérification de l'extension du fichier
    if (!in_array($fileExtension, $allowedExtensions)) {
        logMessage("Extension de fichier non autorisée: $fileExtension", $logOutput);
        echo "Seuls les fichiers .sbp et .sbpcfg sont autorisés.\n" . $logOutput;
        exit;
    }

    $destinationFile = $uploadDir . $fileName;

    // Déplacer le fichier directement vers le répertoire de destination
    if (move_uploaded_file($_FILES['file']['tmp_name'], $destinationFile)) {
        logMessage("Fichier déplacé vers $destinationFile", $logOutput);

        // Utiliser sudo pour changer le propriétaire du fichier
        $command = "sudo chown sfserver:sfserver '$destinationFile'";
        logMessage("Exécution de la commande: $command", $logOutput);
        $output = shell_exec($command);
        logMessage("Sortie de la commande: $output", $logOutput);

        if ($output === null) {
            logMessage("Erreur lors du changement de propriétaire du fichier.", $logOutput);
            echo "Erreur lors du changement de propriétaire du fichier.\n" . $logOutput;
        } else {
            logMessage("Fichier uploadé et propriétaire changé avec succès.", $logOutput);
            echo "Fichier uploadé et propriétaire changé avec succès.\n" . $logOutput;
        }
    } else {
        logMessage("Erreur lors du déplacement du fichier.", $logOutput);
        echo "Erreur lors du déplacement du fichier.\n" . $logOutput;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    if (in_array($action, ['start', 'stop', 'restart'])) {
        logMessage("Exécution de l'action serveur: $action", $logOutput);
        $output = shell_exec("cd /home/sfserver && ./sfserver $action");
        logMessage("Résultat de l'action: $output", $logOutput);
        echo "Serveur $action avec succès.\n" . $logOutput;
    }
}
?>
