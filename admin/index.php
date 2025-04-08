<?php
require_once '../config.php';

if (!aPermission(3)) {
    header('Location: ../index.php');
    exit;
}

$utilisateurs = $pdo->query("SELECT * FROM utilisateurs ORDER BY nom, prenom")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php"><?php echo SITE_NAME; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Gestion Utilisateurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">Gestion Catégories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../stocks.php">Retour aux stocks</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestion des Utilisateurs</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajouterUtilisateur">
                <i class="bi bi-plus-circle"></i> Nouvel Utilisateur
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Niveau</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($utilisateurs as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $user['niveau_permission'] == 3 ? 'danger' : 
                                                ($user['niveau_permission'] == 2 ? 'warning' : 
                                                ($user['niveau_permission'] == 1 ? 'info' : 'secondary')); 
                                        ?>">
                                            Niveau <?php echo $user['niveau_permission']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary me-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modifierUtilisateur"
                                                data-user-id="<?php echo $user['id']; ?>"
                                                data-user-nom="<?php echo htmlspecialchars($user['nom']); ?>"
                                                data-user-prenom="<?php echo htmlspecialchars($user['prenom']); ?>"
                                                data-user-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                data-user-niveau="<?php echo $user['niveau_permission']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#supprimerUtilisateur"
                                                    data-user-id="<?php echo $user['id']; ?>"
                                                    data-user-nom="<?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ajouterUtilisateur" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="actions/utilisateur_ajouter.php" method="POST">
                    <div class="modal-body">
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
                            <label for="niveau" class="form-label">Niveau de permission</label>
                            <select class="form-select" id="niveau" name="niveau_permission" required>
                                <option value="0">Niveau 0 - Consultation</option>
                                <option value="1">Niveau 1 - Modification stocks</option>
                                <option value="2">Niveau 2 - Supervision</option>
                                <option value="3">Niveau 3 - Administration</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modifierUtilisateur" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier l'utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="actions/utilisateur_modifier.php" method="POST">
                    <input type="hidden" name="user_id" id="modifier_user_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modifier_nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="modifier_nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="modifier_prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="modifier_prenom" name="prenom" required>
                        </div>
                        <div class="mb-3">
                            <label for="modifier_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="modifier_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="modifier_niveau" class="form-label">Niveau de permission</label>
                            <select class="form-select" id="modifier_niveau" name="niveau_permission" required>
                                <option value="0">Niveau 0 - Consultation</option>
                                <option value="1">Niveau 1 - Modification stocks</option>
                                <option value="2">Niveau 2 - Supervision</option>
                                <option value="3">Niveau 3 - Administration</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="modifier_password" class="form-label">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="modifier_password" name="password" 
                                   placeholder="Laissez vide pour ne pas changer">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="supprimerUtilisateur" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="actions/utilisateur_supprimer.php" method="POST">
                    <input type="hidden" name="user_id" id="supprimer_user_id">
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="supprimer_user_nom"></strong> ?</p>
                        <p class="text-danger">Cette action est irréversible.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modifierModal = document.getElementById('modifierUtilisateur');
            if (modifierModal) {
                modifierModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    document.getElementById('modifier_user_id').value = button.getAttribute('data-user-id');
                    document.getElementById('modifier_nom').value = button.getAttribute('data-user-nom');
                    document.getElementById('modifier_prenom').value = button.getAttribute('data-user-prenom');
                    document.getElementById('modifier_email').value = button.getAttribute('data-user-email');
                    document.getElementById('modifier_niveau').value = button.getAttribute('data-user-niveau');
                });
            }

            const supprimerModal = document.getElementById('supprimerUtilisateur');
            if (supprimerModal) {
                supprimerModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    document.getElementById('supprimer_user_id').value = button.getAttribute('data-user-id');
                    document.getElementById('supprimer_user_nom').textContent = button.getAttribute('data-user-nom');
                });
            }
        });
    </script>
</body>
</html>
