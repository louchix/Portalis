<?php
header('Content-Type: application/json');

$output = [];

// Exécuter le script shell pour vérifier le statut du serveur
exec("sh /home/sfserver/script/startup.sh", $output, $return_var);

// Retourner simplement la sortie brute en JSON
echo json_encode([
    'output' => $output,
    'return_var' => $return_var
]);
?> 