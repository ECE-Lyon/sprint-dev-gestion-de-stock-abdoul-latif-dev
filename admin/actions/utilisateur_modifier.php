<?php
require_once '../../config.php';

if (!aPermission(3)) {
    header('Location: ../../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $niveau = filter_input(INPUT_POST, 'niveau_permission', FILTER_VALIDATE_INT);
    $password = $_POST['password'] ?? '';

    if ($user_id && $nom && $prenom && $email && $niveau !== false) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetchColumn() == 0) {
            if ($password) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, mot_de_passe = ?, niveau_permission = ? WHERE id = ?");
                $success = $stmt->execute([$nom, $prenom, $email, $hashed_password, $niveau, $user_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, niveau_permission = ? WHERE id = ?");
                $success = $stmt->execute([$nom, $prenom, $email, $niveau, $user_id]);
            }

            if ($success) {
                $_SESSION['admin_message'] = "Utilisateur mis à jour avec succès";
            } else {
                $_SESSION['admin_error'] = "Erreur lors de la mise à jour de l'utilisateur";
            }
        } else {
            $_SESSION['admin_error'] = "Cet email est déjà utilisé par un autre utilisateur";
        }
    } else {
        $_SESSION['admin_error'] = "Données invalides";
    }
}

header('Location: ../index.php');
exit;
