<?php
require_once 'config.php';

if (!estConnecte()) {
    header('Location: connexion.php');
    exit;
}

$message = '';
$erreur = '';
$erreurs = array();

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$utilisateur = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $ancien_mdp = $_POST['ancien_mdp'] ?? '';
    $nouveau_mdp = $_POST['nouveau_mdp'] ?? '';
    $confirmer_mdp = $_POST['confirmer_mdp'] ?? '';

    if (strlen(trim($nom)) < 2 || strlen(trim($prenom)) < 2) {
        $erreurs[] = "Nom et prénom doivent contenir au moins 2 caractères.";
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $erreurs[] = "Email invalide.";
    }

    if ($nouveau_mdp && strlen($nouveau_mdp) < 6) {
        $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    if (!empty($erreurs)) {
        $_SESSION['admin_error'] = implode("<br>", $erreurs);
    } else {
        if ($nom && $prenom && $email) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ? AND id != ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                if ($ancien_mdp && $nouveau_mdp && $confirmer_mdp) {
                    if (password_verify($ancien_mdp, $utilisateur['mot_de_passe'])) {
                        if ($nouveau_mdp === $confirmer_mdp) {
                            $hashed_password = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, mot_de_passe = ? WHERE id = ?");
                            if ($stmt->execute([$nom, $prenom, $email, $hashed_password, $_SESSION['user_id']])) {
                                $message = "Profil mis à jour avec succès (avec nouveau mot de passe)";
                                $_SESSION['nom'] = $nom;
                                $_SESSION['prenom'] = $prenom;
                            }
                        } else {
                            $erreur = "Les nouveaux mots de passe ne correspondent pas";
                        }
                    } else {
                        $erreur = "Ancien mot de passe incorrect";
                    }
                } else {
                    $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ? WHERE id = ?");
                    if ($stmt->execute([$nom, $prenom, $email, $_SESSION['user_id']])) {
                        $message = "Profil mis à jour avec succès";
                        $_SESSION['nom'] = $nom;
                        $_SESSION['prenom'] = $prenom;
                    }
                }
            } else {
                $erreur = "Cet email est déjà utilisé par un autre utilisateur";
            }
        } else {
            $erreur = "Veuillez remplir tous les champs obligatoires";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Mon Profil</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-success"><?php echo $message; ?></div>
                        <?php endif; ?>
                        <?php if ($erreur): ?>
                            <div class="alert alert-danger"><?php echo $erreur; ?></div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['admin_error'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nom" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" 
                                           value="<?php echo htmlspecialchars($utilisateur['nom']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" 
                                           value="<?php echo htmlspecialchars($utilisateur['prenom']); ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($utilisateur['email']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="niveau_permission" class="form-label">Niveau de permission</label>
                                <input type="text" class="form-control" id="niveau_permission" 
                                       value="<?php echo $utilisateur['niveau_permission']; ?>" readonly>
                            </div>

                            <hr>
                            <h5>Changer le mot de passe</h5>
                            <p class="text-muted small">Laissez vide si vous ne souhaitez pas changer de mot de passe</p>

                            <div class="mb-3">
                                <label for="ancien_mdp" class="form-label">Ancien mot de passe</label>
                                <input type="password" class="form-control" id="ancien_mdp" name="ancien_mdp">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nouveau_mdp" class="form-label">Nouveau mot de passe</label>
                                    <input type="password" class="form-control" id="nouveau_mdp" name="nouveau_mdp">
                                </div>
                                <div class="col-md-6">
                                    <label for="confirmer_mdp" class="form-label">Confirmer le nouveau mot de passe</label>
                                    <input type="password" class="form-control" id="confirmer_mdp" name="confirmer_mdp">
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
