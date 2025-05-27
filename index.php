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
</div>

<?php include 'includes/footer.php'; ?>
