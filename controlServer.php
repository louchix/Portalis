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
    $ftpServer = "axiiom.org"; // Remplacez par l'adresse de votre serveur FTP
    $ftpPort = 21; // Port FTP par défaut
    $ftpUsername = "sfserver"; // Remplacez par votre nom d'utilisateur FTP
    $ftpPassword = "!Zaya12131213"; // Remplacez par votre mot de passe FTP
    $uploadDir = ".config/Epic/FactoryGame/Saved/SaveGames/blueprints/uWu Factory"; // Chemin sur le serveur FTP

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

    // Connexion au serveur FTP
    $ftpConn = ftp_connect($ftpServer, $ftpPort);
    if (!$ftpConn) {
        logMessage("Could not connect to $ftpServer on port $ftpPort", $logOutput);
        echo "Could not connect to $ftpServer on port $ftpPort\n" . $logOutput;
        exit;
    }
    $login = ftp_login($ftpConn, $ftpUsername, $ftpPassword);

    if (!$login) {
        logMessage("Connexion FTP échouée.", $logOutput);
        echo "Erreur de connexion FTP.\n" . $logOutput;
        exit;
    }

    logMessage("Connexion FTP réussie.", $logOutput);

    // Téléchargement du fichier
    $destinationFile = $uploadDir . $fileName;
    if (ftp_put($ftpConn, $destinationFile, $_FILES['file']['tmp_name'], FTP_BINARY)) {
        logMessage("Fichier uploadé vers $destinationFile", $logOutput);
        echo "Fichier uploadé avec succès.\n" . $logOutput;
    } else {
        logMessage("Erreur lors de l'upload FTP.", $logOutput);
        echo "Erreur lors de l'upload FTP.\n" . $logOutput;
    }

    // Fermer la connexion FTP
    ftp_close($ftpConn);
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
