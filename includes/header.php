<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        .alert-stock {
            border-left: 5px solid #dc3545;
            border-radius: 0;
            margin-bottom: 0;
        }
        .alert-stock .bi {
            font-size: 1.5rem;
            margin-right: 10px;
        }
    </style>
</head>
<<<<<<< HEAD
<body>
=======
<body>
    <?php if (estConnecte() && ($nbAlertes = getNombreProduitsEnAlerte()) > 0): ?>
    <div class="alert alert-warning alert-dismissible fade show alert-stock" role="alert">
        <div class="container">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>
                    <strong>Attention !</strong> Il y a <?php echo $nbAlertes; ?> produit(s) dont le stock est faible.
                    <a href="stocks.php?alerte=1" class="alert-link">Voir les produits concernés</a>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    </div>
    <?php endif; ?>
>>>>>>> ceea169c776271af8ae07673547d20b5db9b81c5
