<?php
header('Content-Type: application/json'); // Déclarer le type de contenu JSON

// Dossier où stocker les blueprints
$target_dir = "/home/sfserver/.config/Epic/FactoryGame/Saved/SaveGames/blueprints/'uWu Factory'";
if (!file_exists($target_dir)) {
    // Utiliser sudo pour créer le répertoire en tant que sfserver
    exec('sudo -u sfserver mkdir -p ' . escapeshellarg($target_dir));
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
error_log("Tentative de déplacement du fichier de " . $file['tmp_name'] . " vers " . $target_file);

if (move_uploaded_file($file['tmp_name'], $target_file)) {
    // Changer le propriétaire du fichier après l'upload
    exec('sudo chown sfserver:sfserver ' . escapeshellarg($target_file));
    error_log("Blueprint uploadé avec succès : " . $target_file);
    $response['success'] = true;
    $response['message'] = 'Fichier uploadé avec succès';
    $response['location'] = $target_file;
} else {
    // Enregistre l'erreur dans le journal d'erreurs
    error_log("Erreur lors du téléchargement du blueprint : " . print_r(error_get_last(), true));
    $response['error'] = 'Une erreur s\'est produite lors du téléchargement du fichier. ' . print_r(error_get_last(), true);
}

// Envoyer la réponse JSON
echo json_encode($response);
?>
