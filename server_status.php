<?php
header('Content-Type: application/json');

// Exécuter le script shell et récupérer uniquement la réponse du echo
$status = exec("sh /home/sfserver/script/startup.sh 2>&1", $output, $return_var);

// Retourner uniquement le statut
echo json_encode([
    'status' => trim($status), // On utilise trim pour enlever les espaces inutiles
    'return_var' => $return_var
]);
?> 