<?php
require_once '../config.php';

// Simuler un délai de chargement
sleep(1);

// Retourner des statistiques fictives pour la page d'accueil
$stats = [
    'total_produits' => 150,
    'categories' => 5,
    'produits_alerte' => 3,
    'derniere_mise_a_jour' => date('Y-m-d H:i:s')
];

// Retourner en JSON
header('Content-Type: application/json');
echo json_encode($stats);