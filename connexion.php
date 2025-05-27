<?php
require_once 'config.php';

// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$erreur = '';
$succes = '';
$page_title = 'Connexion';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            $utilisateur = $stmt->fetch();

            if (!$utilisateur) {
                $erreur = 'Utilisateur non trouvé : ' . $email;
            } elseif (!password_verify($password, $utilisateur['mot_de_passe'])) {
                $erreur = 'Mot de passe incorrect pour : ' . $email;
            } else {
                // Connexion réussie
                $_SESSION['user_id'] = $utilisateur['id'];
                $_SESSION['nom'] = $utilisateur['nom'];
                $_SESSION['prenom'] = $utilisateur['prenom'];
                $_SESSION['niveau_permission'] = $utilisateur['niveau_permission'];
                
                // Redirection
                header('Location: index.php');
                exit;
            }
        } catch (PDOException $e) {
            // Journaliser l'erreur et afficher un message générique
            error_log('Erreur de connexion: ' . $e->getMessage());
            $erreur = 'Une erreur est survenue lors de la connexion. Veuillez réessayer.';
        }
    } else {
        $erreur = 'Veuillez remplir tous les champs';
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="main-container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="animated-card animate__animated animate__fadeIn">
                <div class="card-header bg-primary text-white p-3">
                    <h4 class="mb-0 animated-title text-white">Connexion</h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($erreur): ?>
                        <div class="alert alert-danger animate__animated animate__fadeIn"><?php echo $erreur; ?></div>
                    <?php endif; ?>
                    <?php if ($succes): ?>
                        <div class="alert alert-success animate__animated animate__fadeIn"><?php echo $succes; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3 animated-element delay-1">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                            </div>
                        </div>
                        <div class="mb-4 animated-element delay-2">
                            <label for="password" class="form-label">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="animated-element delay-3">
                            <button type="submit" class="btn btn-primary btn-animated">
                                <span>Se connecter</span>
                                <div class="spinner-border spinner-border-sm loading-spinner" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>