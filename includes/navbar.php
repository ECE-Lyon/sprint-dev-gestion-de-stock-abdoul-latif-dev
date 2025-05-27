<nav class="navbar navbar-expand-lg navbar-dark bg-dark animate__animated animate__fadeInDown">
    <div class="container">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>/index.php"><?php echo SITE_NAME; ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/stocks.php">Stocks</a>
                </li>
                <?php if (aPermission(1)): ?>
                <li class="nav-item">
<<<<<<< HEAD
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/historique.php">
                        <i class="bi bi-clock-history me-1"></i>Historique
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-warning" href="<?php echo BASE_URL; ?>/alertes.php">
                        <i class="bi bi-bell-fill me-1"></i>Alertes
                        <?php
                        // Vérifier s'il y a des alertes
                        $seuil_alerte = 5;
                        $stmt = $pdo->query("SELECT COUNT(*) as nb_alertes FROM produits WHERE quantite <= $seuil_alerte");
                        $alertes = $stmt->fetch();
                        if ($alertes['nb_alertes'] > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $alertes['nb_alertes']; ?>
                            </span>
                        <?php endif; ?>
                    </a>
=======
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/historique.php">Historique</a>
>>>>>>> ceea169c776271af8ae07673547d20b5db9b81c5
                </li>
                <?php endif; ?>
                <?php if (aPermission(3)): ?>
                <!-- Vérifiez que ce lien est correct -->
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo str_replace('/admin', '', dirname($_SERVER['PHP_SELF'])); ?>/admin/">Administration</a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (estConnecte()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/profil.php">Mon Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/deconnexion.php">Déconnexion</a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/connexion.php">Connexion</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
