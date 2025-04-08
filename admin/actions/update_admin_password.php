<?php
require_once '../../config.php';

$password = 'Admin123!';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE email = 'admin@restaurant.fr'");
if ($stmt->execute([$hashed_password])) {
    echo "Mot de passe administrateur mis à jour avec succès.<br>";
    echo "Email: admin@restaurant.fr<br>";
    echo "Mot de passe: Admin123!";
} else {
    echo "Erreur lors de la mise à jour du mot de passe.";
}
?>
