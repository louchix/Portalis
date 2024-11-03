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
    $uploadDir = "/home/sfserver/.config/Epic/FactoryGame/Saved/SaveGames/blueprints/uWu Factory/"; // Remplacez par le chemin de votre répertoire de destination
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

    // Déplacer le fichier vers le répertoire de destination
    $destinationFile = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['file']['tmp_name'], $destinationFile)) {
        logMessage("Fichier déplacé vers $destinationFile", $logOutput);
        echo "Fichier uploadé avec succès.\n" . $logOutput;
    } else {
        logMessage("Erreur lors du déplacement du fichier.", $logOutput);
        echo "Erreur lors de l'upload du fichier.\n" . $logOutput;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    logMessage("Requête GET reçue pour l'action: $action", $logOutput);
    if ($action === 'status') {
        // Commande pour vérifier l'état du serveur
        $command = "sudo -u sfserver /home/sfserver/script/startup.sh 2>&1";
        logMessage("Exécution de la commande: $command", $logOutput);
        $status = shell_exec($command);
        $lines = explode("\n", trim($status));
        $status = end($lines); // Récupère la dernière ligne
        logMessage("État du serveur récupéré: $status", $logOutput);
        echo $status; // Retourne uniquement la dernière ligne
    } elseif (in_array($action, ['start', 'stop', 'restart'])) {
        // Commande pour contrôler le serveur
        $command = "sudo -u sfserver /home/sfserver/sfserver $action 2>&1";
        logMessage("Exécution de la commande: $command", $logOutput);
        $output = shell_exec($command);
        logMessage("Résultat de l'action: $output", $logOutput);
        echo "Serveur $action avec succès.\n" . $logOutput;
    }
}
?>