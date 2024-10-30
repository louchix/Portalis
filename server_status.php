<?php
header('Content-Type: application/json');

$output = [];
$status = 'OFF'; // Valeur par défaut

// Exécuter le script shell
exec("cd /home/sfserver && ./sfserver details > startup.txt && grep -q 'STARTED' startup.txt", $output, $return_var);

// Vérifier le statut
if ($return_var === 0) {
    $status = 'ON';
} else {
    $status = 'OFF';
}

// Retourner le statut en JSON
echo json_encode(['status' => $status]);
?> 