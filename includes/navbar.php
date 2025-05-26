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
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/historique.php">Historique</a>
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
