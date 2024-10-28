<?php
header('Content-Type: application/json');

// Définir le dossier d'upload
$uploadDir = '/home/sfserver/.config/Epic/FactoryGame/Saved/SaveGames/blueprints';
$allowedExtensions = ['sbp', 'sbpcfg'];
$response = ['success' => false, 'message' => ''];

// Vérifiez si des fichiers ont été envoyés
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
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Nettoyer le nom de fichier pour éviter les caractères spéciaux
    $cleanedFileName = cleanFileName($fileName);

    // Vérifier l'extension
    if (in_array($fileExt, $allowedExtensions)) {
        $destination = "$uploadDir/$cleanedFileName";

        // Vérifiez si le fichier existe déjà
        if (file_exists($destination)) {
            $response['message'] = "Le fichier $cleanedFileName existe déjà.";
            echo json_encode($response);
            exit;
        }

        // Tenter de déplacer le fichier téléchargé
        if (move_uploaded_file($tmpFilePath, $destination)) {
            $response['success'] = true;
            $response['message'] = "Upload réussi pour le fichier $cleanedFileName.";
        } else {
            // Ajout d'un message d'erreur plus détaillé
            $response['message'] = "Erreur lors de l'upload du fichier $cleanedFileName. Vérifiez les permissions et la validité du fichier.";
            echo json_encode($response);
            exit;
        }
    } else {
        $response['message'] = "Extension non autorisée pour le fichier $fileName.";
        echo json_encode($response);
        exit;
    }
}

echo json_encode($response);
?>
