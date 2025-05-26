<?php
// Configuration locale
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'gestion_stock_restaurant');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('BASE_URL', 'http://localhost/tousmesprojet/sprint-dev-gestion-de-stock-abdoul-latif-dev');
}
// Configuration en ligne
else {
    define('DB_HOST', 'sql105.infinityfree.com');
    define('DB_NAME', 'if0_38848645_identifians');
    define('DB_USER', 'if0_38848645');
    define('DB_PASS', 'Rakiaaliosarki');
    define('BASE_URL', 'http://gestion-stock.wuaze.com'); // URL corrigée sans backticks ni espaces
}

define('SITE_NAME', 'Gestion des Stocks Restaurant');

define('PERMISSION_LECTURE', 0);     
define('PERMISSION_STOCK', 1);       
define('PERMISSION_SUPERVISEUR', 2);  
define('PERMISSION_ADMIN', 3);        

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

session_start();

function estConnecte() {
    return isset($_SESSION['user_id']);
}

function aPermission($niveau_requis) {
    return estConnecte() && $_SESSION['niveau_permission'] >= $niveau_requis;
}

function estSuperviseur() {
    return aPermission(PERMISSION_SUPERVISEUR);
}

function peutModifierStock() {
    return aPermission(PERMISSION_STOCK);
}

function verifierPermission($niveau_requis) {
    if (!aPermission($niveau_requis)) {
        $_SESSION['error'] = "Vous n'avez pas les permissions nécessaires pour accéder à cette page.";
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

/**
 * Récupère la liste des produits dont la quantité est inférieure ou égale au seuil d'alerte
 * @param int $seuil Seuil d'alerte (par défaut 5)
 * @return array Liste des produits en alerte
 */
function getProduitsEnAlerte($seuil = 5) {
    global $pdo;
    try {
        $query = $pdo->prepare("SELECT id, nom, quantite, seuil_alerte FROM produits WHERE quantite <= ? ORDER BY quantite ASC");
        $query->execute([$seuil]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des produits en alerte : " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère le nombre de produits dont la quantité est inférieure ou égale au seuil d'alerte
 * @param int $seuil Seuil d'alerte (par défaut 5)
 * @return int Nombre de produits en alerte
 */
function getNombreProduitsEnAlerte($seuil = 5) {
    global $pdo;
    try {
        $query = $pdo->prepare("SELECT COUNT(*) as total FROM produits WHERE quantite <= ?");
        $query->execute([$seuil]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    } catch (PDOException $e) {
        error_log("Erreur lors du comptage des produits en alerte : " . $e->getMessage());
        return 0;
    }
}
?>