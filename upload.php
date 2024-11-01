<?php
header('Content-Type: application/json');

$targetDir = "blueprint/"; // Dossier de destination
$targetFile = $targetDir . basename($_FILES["file"]["name"]);
$uploadOk = 1;
$message = "";

// Vérifiez si le fichier est un fichier valide
$fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

// Vérifiez si le fichier existe déjà
if (file_exists($targetFile)) {
    $message = "Désolé, le fichier existe déjà.";
    $uploadOk = 0;
}

// Vérifiez la taille du fichier
if ($_FILES["file"]["size"] > 500000) { // Limite de 500 Ko
    $message = "Désolé, votre fichier est trop gros.";
    $uploadOk = 0;
}

// Autoriser uniquement les formats de fichier .sbp et .sbpcfg
if ($fileType != "sbp" && $fileType != "sbpcfg") {
    $message = "Désolé, seuls les fichiers SBP et SBPCFG sont autorisés.";
    $uploadOk = 0;
}

// Vérifiez si $uploadOk est défini sur 0 par une erreur
if ($uploadOk == 0) {
    echo json_encode(['success' => false, 'message' => $message]);
} else {
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        echo json_encode(['success' => true, 'message' => "Le fichier ". htmlspecialchars(basename($_FILES["file"]["name"])). " a été uploadé."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Désolé, une erreur est survenue lors de l'upload de votre fichier."]);
    }
}
?>
