<?php
require_once '../../config.php';

if (!aPermission(3)) {
    header('Location: ../../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $niveau = filter_input(INPUT_POST, 'niveau_permission', FILTER_VALIDATE_INT);
    $password = $_POST['password'] ?? '';

    if ($nom && $prenom && $email && $niveau !== false && $password) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() == 0) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, niveau_permission) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$nom, $prenom, $email, $hashed_password, $niveau])) {
                $_SESSION['admin_message'] = "Utilisateur ajouté avec succès";
            } else {
                $_SESSION['admin_error'] = "Erreur lors de l'ajout de l'utilisateur";
            }
        } else {
            $_SESSION['admin_error'] = "Cet email est déjà utilisé";
        }
    } else {
        $_SESSION['admin_error'] = "Tous les champs sont requis";
    }
}

header('Location: ../index.php');
exit;
