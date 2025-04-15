<?php
require_once '../../config.php';

if (!aPermission(3)) {
    header('Location: ../../index.php');
    exit;
}

$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $niveau = filter_input(INPUT_POST, 'niveau_permission', FILTER_VALIDATE_INT);
    $password = $_POST['password'] ?? '';

    if (strlen(trim($nom)) < 2 || strlen(trim($prenom)) < 2) {
        $erreurs[] = "Nom et prénom doivent contenir au moins 2 caractères.";
    }

    if (!$email) {
        $erreurs[] = "Email invalide.";
    }

    if (strlen($password) < 6) {
        $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

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

    if (!empty($erreurs)) {
        $_SESSION['admin_error'] = implode("<br>", $erreurs);
    }
}

header('Location: ../index.php');
exit;
