<?php
header('Content-Type: application/json');

$output = [];
$status = 'OFF'; // Valeur par défaut

// Exécuter le script shell pour vérifier le statut du serveur
exec("/home/sfserver/script/startup.sh", $output, $return_var);

// Vérifier le statut dans la sortie
if (isset($output[0])) {
    if (strpos($output[0], "Serveur ON") !== false) {
        $status = 'ON';
    } elseif (strpos($output[0], "Serveur OFF") !== false) {
        $status = 'OFF';
    }
}

// Retourner le statut, l'output et le code de retour en JSON
echo json_encode([
    'status' => $status,
    'output' => $output,
    'return_var' => $return_var
]);
?> 