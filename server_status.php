<?php
header('Content-Type: application/json');

// Exécuter le script shell et récupérer uniquement la réponse du echo
$status = exec("sh /home/sfserver/script/startup.sh 2>&1", $output, $return_var);

// Vérifiez si le script a échoué
if ($return_var !== 0) {
    // En cas d'erreur, retourner le message d'erreur
    echo json_encode([
        'status' => 'Erreur lors de l\'exécution du script',
        'error' => implode("\n", $output), // Retourner les messages d'erreur
        'return_var' => $return_var
    ]);
    exit;
}

// Retourner uniquement le statut
echo json_encode([
    'status' => trim($status), // On utilise trim pour enlever les espaces inutiles
    'return_var' => $return_var
]);
?> 