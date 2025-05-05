<?php
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie !";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
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
?>
