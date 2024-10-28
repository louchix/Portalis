<?php
header('Content-Type: application/json');

$uploadDir = '/home/sfserver/.config/Epic/FactoryGame/Saved/SaveGames/blueprints';
$allowedExtensions = ['sbp', 'sbpcfg'];
$response = ['success' => false, 'message' => ''];

if (!isset($_FILES['files'])) {
    $response['message'] = 'Aucun fichier reçu';
    echo json_encode($response);
    exit;
}

// Fonction pour nettoyer le nom de fichier
function cleanFileName($filename) {
    // Remplace les espaces et caractères spéciaux par un underscore "_"
    return preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
}

foreach ($_FILES['files']['tmp_name'] as $index => $tmpFilePath) {
    $fileName = basename($_FILES['files']['name'][$index]);
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

    // Nettoyer le nom de fichier pour éviter les caractères spéciaux
    $cleanedFileName = cleanFileName($fileName);

    if (in_array($fileExt, $allowedExtensions)) {
        $destination = "$uploadDir/$cleanedFileName";

        if (move_uploaded_file($tmpFilePath, $destination)) {
            $response['success'] = true;
            $response['message'] = 'Upload réussi pour tous les fichiers !';
        } else {
            $response['message'] = "Erreur lors de l'upload du fichier $cleanedFileName. Vérifiez les permissions.";
            echo json_encode($response);
            exit;
        }
    } else {
        $response['message'] = "Extension non autorisée pour le fichier $fileName";
        echo json_encode($response);
        exit;
    }
}

echo json_encode($response);
?>
