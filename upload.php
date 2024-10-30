<?php
header('Content-Type: application/json'); // Déclarer le type de contenu JSON

// Dossier où stocker les blueprints
$target_dir = "/home/sfserver/.config/Epic/FactoryGame/Saved/SaveGames/blueprints/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true); // Créer le répertoire si nécessaire
}

// Variables pour la réponse JSON
$response = array();

// Vérifier si un fichier a été téléchargé
if (!isset($_FILES['file'])) {
    $response['error'] = 'Aucun fichier téléchargé.';
    echo json_encode($response);
    exit;
}

// Récupérer les informations du fichier
$file = $_FILES['file'];
$filename = basename($file['name']);
$target_file = $target_dir . $filename;
$fileExtension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Types de fichiers valides
$valid_extensions = array("sbp", "sbpcfg");

// Vérifier si le fichier a une extension valide
if (!in_array($fileExtension, $valid_extensions)) {
    $response['error'] = 'Format de fichier non valide. Seuls les fichiers SBP et SBPCFG sont acceptés.';
    echo json_encode($response);
    exit;
}

// Vérifier la taille du fichier (par exemple, 10 Go maximum)
if ($file['size'] > 10000000000) {
    $response['error'] = 'Le fichier est trop volumineux. Taille maximale : 10 Go.';
    echo json_encode($response);
    exit;
}

// Essayer de déplacer le fichier téléchargé vers le dossier cible
if (move_uploaded_file($file['tmp_name'], $target_file)) {
    error_log("Blueprint uploadé avec succès : " . $target_file);
    $response['success'] = true;
    $response['message'] = 'Fichier uploadé avec succès';
    $response['location'] = $target_file;
} else {
    // Amélioration du débogage
    $upload_error = $_FILES['file']['error'];
    error_log("Code erreur upload : " . $upload_error);
    error_log("Informations fichier : " . print_r($_FILES['file'], true));
    error_log("Permissions dossier cible : " . substr(sprintf('%o', fileperms($target_dir)), -4));
    
    $error_message = match($upload_error) {
        UPLOAD_ERR_INI_SIZE => "Le fichier dépasse la taille maximale autorisée par PHP.ini",
        UPLOAD_ERR_FORM_SIZE => "Le fichier dépasse la taille maximale autorisée par le formulaire",
        UPLOAD_ERR_PARTIAL => "Le fichier n'a été que partiellement uploadé",
        UPLOAD_ERR_NO_FILE => "Aucun fichier n'a été uploadé",
        UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant",
        UPLOAD_ERR_CANT_WRITE => "Échec de l'écriture du fichier sur le disque",
        UPLOAD_ERR_EXTENSION => "Une extension PHP a arrêté l'upload",
        0 => "Erreur lors du déplacement du fichier : " . error_get_last()['message'] ?? 'Raison inconnue',
        default => "Erreur inconnue (code: $upload_error)"
    };
    
    error_log("Erreur upload : " . $error_message);
    error_log("Chemin temporaire : " . $file['tmp_name']);
    error_log("Chemin cible : " . $target_file);
    
    $response['success'] = false;
    $response['error'] = $error_message;
    $response['debug'] = [
        'tmp_name' => $file['tmp_name'],
        'target_file' => $target_file,
        'error_code' => $upload_error,
        'file_exists_tmp' => file_exists($file['tmp_name']) ? 'oui' : 'non',
        'target_dir_writable' => is_writable($target_dir) ? 'oui' : 'non'
    ];
}

// Envoyer la réponse JSON
echo json_encode($response);
?>
