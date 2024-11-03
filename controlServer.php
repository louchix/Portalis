<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = '/home/sfserver/.config/Epic/FactoryGame/Saved/SaveGames/blueprints/uWu\ Factory/';
    $fileName = basename($_FILES['file']['name']);
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['sbp', 'sbpcfg'];

    // Vérification de l'extension du fichier
    if (!in_array($fileExtension, $allowedExtensions)) {
        echo "Seuls les fichiers .sbp et .sbpcfg sont autorisés.";
        exit;
    }

    $uploadFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        echo "Fichier uploadé avec succès.";
    } else {
        echo "Erreur lors de l'upload.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    if (in_array($action, ['start', 'stop', 'restart'])) {
        // Remplacez par la commande réelle pour contrôler le serveur
        shell_exec("cd /home/sfserver && ./sfserver $action");
        echo "Serveur $action avec succès.";
    }
}
?>
