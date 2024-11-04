<?php
// En-têtes CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

function logMessage($message, &$logOutput) {

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
                echo json_encode(['status' => 'ON']);
            } elseif (strpos($status, 'Serveur OFF') !== false) {
                echo json_encode(['status' => 'OFF']);
            } else {
                echo json_encode(['status' => 'UNKNOWN', 'message' => $status]);
            }
        } else {
            logMessage("Le fichier status.txt n'existe pas.", $logOutput);
            echo json_encode(['error' => 'Le fichier status.txt n\'existe pas.']);
        }
    } elseif (in_array($action, ['stop'])) {
        // Commande pour contrôler le serveur avec sudo service
        $command = "sudo service sfserver $action && sleep 5 &&sudo service sfserver start"; // Exécuter la commande service
        logMessage("Exécution de la commande: $command", $logOutput);
        $output = shell_exec($command);
        logMessage("Résultat de l'action: $output", $logOutput);
        echo 'Serveur redémarré avec succès.';
    } elseif ($action === 'list_saves') {
        $saveDir = '/home/sfserver/.config/Epic/FactoryGame/Saved/SaveGames/server';
        if (is_dir($saveDir)) {
            $files = array_diff(scandir($saveDir), array('..', '.')); // Liste des fichiers
            echo json_encode(['files' => $files]);
        } else {
            echo json_encode(['error' => 'Le répertoire de sauvegarde n\'existe pas.']);
        }
    } elseif ($action === 'download' && isset($_GET['file'])) {
        $file = basename($_GET['file']); // Sécuriser le nom du fichier
        $filePath = "/home/sfserver/.config/Epic/FactoryGame/Saved/SaveGames/server/$file";

        if (file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            echo json_encode(['error' => 'Le fichier n\'existe pas.']);
        }
    }
}
?>