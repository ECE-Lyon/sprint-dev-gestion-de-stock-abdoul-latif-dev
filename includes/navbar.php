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
                <?php if (estSuperviseur()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="historique.php">Historique</a>
                </li>
                <?php endif; ?>
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
                <li class="nav-item">
                    <a class="nav-link" href="inscription.php">Inscription</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
