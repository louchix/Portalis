<?php
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// Vérifiez que l'action est valide
if (!in_array($action, ['start', 'stop', 'restart'])) {
    echo json_encode(['status' => 'Action non valide']);
    exit;
}

// Exécuter la commande correspondante
$command = "sudo -u sfserver /home/sfserver/sfserver $action";
$status = exec("$command 2>&1", $output, $return_var);

// Vérifiez si la commande a échoué
if ($return_var !== 0) {
    echo json_encode([
        'status' => 'Erreur lors de l\'exécution de l\'action',
        'error' => implode("\n", $output),
        'return_var' => $return_var
    ]);
    exit;
}

// Retourner le statut
echo json_encode([
    'status' => trim(implode("\n", $output)), // Retourner la sortie de la commande
    'return_var' => $return_var
]);
?> 