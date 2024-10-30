<?php
header('Content-Type: application/json');

$output = [];
$status = 'OFF'; // Valeur par défaut

// Exécuter le script shell
exec("cd /home/sfserver && ./sfserver details > startup.txt && tail -n 1 startup.txt", $output, $return_var);

// Vérifier le statut
if (isset($output[0]) && strpos($output[0], "Status:") !== false) {
    // Extraire le statut de la ligne
    preg_match('/Status:\s*\[32m(.*?)\[0m/', $output[0], $matches);
    if (isset($matches[1]) && trim($matches[1]) === "STARTED") {
        $status = 'ON';
    } else {
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