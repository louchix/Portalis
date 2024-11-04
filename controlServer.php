<?php
// En-têtes CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

function logMessage($message, &$logOutput) {
    // Convertir le message en UTF-8
    $message = mb_convert_encoding($message, 'UTF-8');
    
    // Enregistrer le message dans les fichiers de log
    error_log($message, 3, '/var/log/controlServer.log');
    $logOutput .= $message . "\n";
    error_log($message, 3, '/home/sfserver/script/execution.log');
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
        // Lire le fichier status.txt pour vérifier l'état du serveur
        $statusFile = '/home/sfserver/status.txt';
        if (file_exists($statusFile)) {
            $status = file_get_contents($statusFile);
            logMessage("Contenu du fichier status.txt: $status", $logOutput);
            // Vérification de l'état du serveur
            if (strpos($status, 'Serveur ON') !== false) {
                echo "Serveur ON";
            } elseif (strpos($status, 'Serveur OFF') !== false) {
                echo "Serveur OFF";
            } else {
                echo "État du serveur inconnu: $status\n" . $logOutput;
            }
        } else {
            logMessage("Le fichier status.txt n'existe pas.", $logOutput);
            echo "Erreur: le fichier status.txt n'existe pas.\n" . $logOutput;
        }
    } elseif (in_array($action, ['start', 'stop', 'restart'])) {
        // Commande pour contrôler le serveur avec les scripts start.sh, stop.sh et restart.sh
        $scriptPath = "/home/sfserver/script/{$action}.sh"; // Chemin vers le script
        $command = "sh $scriptPath "; // Exécuter le script
        logMessage("Exécution de la commande: $command", $logOutput);
        $output = shell_exec($command);
        logMessage("Résultat de l'action: $output", $logOutput);
        echo "Serveur $action avec succès.\n" . $logOutput;
    }
}
?>