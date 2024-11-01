<?php
header('Content-Type: application/json');

$targetDir = "blueprint/"; // Dossier de destination
$targetFile = $targetDir . basename($_FILES["file"]["name"]);
$uploadOk = 1;
$message = "";

// Vérifiez si le fichier est une image ou un document
$fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["file"]["tmp_name"]);
    if ($check !== false) {
        $message = "Le fichier est un fichier valide.";
        $uploadOk = 1;
    } else {
        $message = "Le fichier n'est pas un fichier valide.";
        $uploadOk = 0;
    }
}

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

// Autoriser certains formats de fichier
if ($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "gif" && $fileType != "pdf") {
    $message = "Désolé, seuls les fichiers JPG, JPEG, PNG, GIF et PDF sont autorisés.";
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
