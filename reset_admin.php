<?php
require_once 'config.php';

$message = '';
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier la réponse à la question de sécurité
    $reponse_securite = isset($_POST['reponse_securite']) ? trim($_POST['reponse_securite']) : '';
    
    if (strtolower($reponse_securite) === 'sidi') {
        // Si la réponse est correcte, vérifier les mots de passe
        $nouveau_mot_de_passe = isset($_POST['nouveau_mot_de_passe']) ? $_POST['nouveau_mot_de_passe'] : '';
        $confirmer_mot_de_passe = isset($_POST['confirmer_mot_de_passe']) ? $_POST['confirmer_mot_de_passe'] : '';
        
        if (strlen($nouveau_mot_de_passe) < 6) {
            $erreur = "Le mot de passe doit contenir au moins 6 caractères.";
        } elseif ($nouveau_mot_de_passe !== $confirmer_mot_de_passe) {
            $erreur = "Les mots de passe ne correspondent pas.";
        } else {
            // Hasher le nouveau mot de passe
            $hashed_password = password_hash($nouveau_mot_de_passe, PASSWORD_DEFAULT);
            
            // Mettre à jour le mot de passe dans la base de données
            $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE email = 'admin@restaurant.fr'");
            if ($stmt->execute([$hashed_password])) {
                $message = "Le mot de passe administrateur a été mis à jour avec succès.<br>
                            Vous pouvez maintenant vous connecter avec :<br>
                            Email: admin@restaurant.fr<br>
                            Mot de passe: votre nouveau mot de passe";
            } else {
                $erreur = "Erreur lors de la mise à jour du mot de passe.";
            }
        }
    } else {
        $erreur = "La réponse à la question de sécurité est incorrecte.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe administrateur - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h1 class="h3 mb-4 text-center">Réinitialisation du mot de passe administrateur</h1>
                        
                        <?php if ($erreur): ?>
                            <div class="alert alert-danger"><?php echo $erreur; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-success"><?php echo $message; ?></div>
                            <div class="text-center mt-3">
                                <a href="connexion.php" class="btn btn-primary">Aller à la page de connexion</a>
                            </div>
                        <?php else: ?>
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="reponse_securite" class="form-label">Question de sécurité : Qui a développé ce site ?</label>
                                    <input type="text" class="form-control" id="reponse_securite" name="reponse_securite" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nouveau_mot_de_passe" class="form-label">Nouveau mot de passe</label>
                                    <input type="password" class="form-control" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" required>
                                    <small class="text-muted">Le mot de passe doit contenir au moins 6 caractères.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="confirmer_mot_de_passe" class="form-label">Confirmer le mot de passe</label>
                                    <input type="password" class="form-control" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Réinitialiser le mot de passe</button>
                                </div>
                            </form>
                        <?php endif; ?>
                        
                        <p class="mt-3 text-center">
                            <a href="connexion.php">Retour à la connexion</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>