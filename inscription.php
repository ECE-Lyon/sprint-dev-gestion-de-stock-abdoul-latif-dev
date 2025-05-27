<?php
require_once 'config.php';

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($nom && $prenom && $email && $password && $password_confirm) {
        if ($password === $password_confirm) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, niveau_permission) VALUES (?, ?, ?, ?, 0)");
                if ($stmt->execute([$nom, $prenom, $email, $hashed_password])) {
                    $succes = 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.';
                } else {
                    $erreur = 'Erreur lors de la création du compte';
                }
            } else {
                $erreur = 'Cet email est déjà utilisé';
            }
        } else {
            $erreur = 'Les mots de passe ne correspondent pas';
        }
    } else {
        $erreur = 'Veuillez remplir tous les champs';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Inscription</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($erreur): ?>
                            <div class="alert alert-danger"><?php echo $erreur; ?></div>
                        <?php endif; ?>
                        <?php if ($succes): ?>
                            <div class="alert alert-success"><?php echo $succes; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                            </div>
                            <button type="submit" class="btn btn-primary">S'inscrire</button>
                        </form>
                        <p class="mt-3">
                            Déjà inscrit ? <a href="connexion.php">Connectez-vous ici</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
