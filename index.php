<?php
require_once 'config.php';

// Si l'utilisateur n'est pas connecté, rediriger vers la page d'accueil
if (!estConnecte()) {
    header('Location: welcome.php');
    exit;
}

$page_title = 'Accueil'; // Définir le titre de la page

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="main-container">
    <div class="animated-card animate__animated animate__fadeIn">
        <div class="card-header bg-primary text-white p-3">
            <h4 class="mb-0 animated-title text-white">Bienvenue sur <?php echo SITE_NAME; ?></h4>
        </div>
        <div class="card-body p-4">
            <p class="lead animated-element delay-1">Système de gestion des stocks pour votre restaurant.</p>
            
            <div class="row mt-4">
                <div class="col-md-4 animated-element delay-2">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <h5>Gestion des stocks</h5>
                            <p class="mb-0">Suivez vos produits en temps réel</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 animated-element delay-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div>
                            <h5>Statistiques</h5>
                            <p class="mb-0">Analysez vos données</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 animated-element delay-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <h5>Gestion utilisateurs</h5>
                            <p class="mb-0">Gérez les accès et permissions</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 animated-element delay-5">
                <a href="stocks.php" class="btn btn-primary btn-animated">
                    <span>Accéder aux stocks</span>
                    <div class="spinner-border spinner-border-sm loading-spinner" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php"><?php echo SITE_NAME; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="stocks.php">Stocks</a>
                    </li>
                    <?php if (aPermission(3)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/">Administration</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (estConnecte()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="profil.php">Mon Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="deconnexion.php">Déconnexion</a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="connexion.php">Connexion</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="jumbotron">
            <h1 class="display-4">Bienvenue sur <?php echo SITE_NAME; ?></h1>
            <p class="lead">Système de gestion des stocks pour votre restaurant.</p>
            <?php if (!estConnecte()): ?>
            <hr class="my-4">
            <p>Connectez-vous pour accéder à la gestion des stocks.</p>
            <a class="btn btn-primary btn-lg" href="connexion.php" role="button">Se connecter</a>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
