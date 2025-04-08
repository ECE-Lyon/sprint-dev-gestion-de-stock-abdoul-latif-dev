<?php
require_once '../../config.php';

if (!aPermission(3)) {
    header('Location: ../../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

    if ($user_id && $user_id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
        if ($stmt->execute([$user_id])) {
            $_SESSION['admin_message'] = "Utilisateur supprimé avec succès";
        } else {
            $_SESSION['admin_error'] = "Erreur lors de la suppression de l'utilisateur";
        }
    } else {
        $_SESSION['admin_error'] = "Impossible de supprimer cet utilisateur";
    }
}

header('Location: ../index.php');
exit;
