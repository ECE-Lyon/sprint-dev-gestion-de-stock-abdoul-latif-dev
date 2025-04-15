<?php
require_once '../config.php';

if (!aPermission(3)) {
    header('Location: ../index.php');
    exit;
}

$message = '';
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);

        switch ($_POST['action']) {
            case 'ajouter':
                if ($nom) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE nom = ?");
                    $stmt->execute([$nom]);
                    if ($stmt->fetchColumn() > 0) {
                        $erreur = "Cette catégorie existe déjà.";
                        break;
                    }
                    $stmt = $pdo->prepare("INSERT INTO categories (nom, description) VALUES (?, ?)");
                    if ($stmt->execute([$nom, $description])) {
                        $message = "Catégorie ajoutée avec succès";
                    } else {
                        $erreur = "Erreur lors de l'ajout de la catégorie";
                    }
                }
                break;

            case 'modifier':
                $id = filter_input(INPUT_POST, 'categorie_id', FILTER_VALIDATE_INT);
                if ($id && $nom) {
                    $stmt = $pdo->prepare("UPDATE categories SET nom = ?, description = ? WHERE id = ?");
                    if ($stmt->execute([$nom, $description, $id])) {
                        $message = "Catégorie mise à jour avec succès";
                    } else {
                        $erreur = "Erreur lors de la mise à jour de la catégorie";
                    }
                }
                break;

            case 'supprimer':
                $id = filter_input(INPUT_POST, 'categorie_id', FILTER_VALIDATE_INT);
                if ($id) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM produits WHERE categorie_id = ?");
                    $stmt->execute([$id]);
                    if ($stmt->fetchColumn() == 0) {
                        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                        if ($stmt->execute([$id])) {
                            $message = "Catégorie supprimée avec succès";
                        } else {
                            $erreur = "Erreur lors de la suppression de la catégorie";
                        }
                    } else {
                        $erreur = "Impossible de supprimer cette catégorie car elle est utilisée par des produits";
                    }
                }
                break;
        }
    }
}

$categories = $pdo->query("SELECT c.*, COUNT(p.id) as nb_produits 
                          FROM categories c 
                          LEFT JOIN produits p ON c.id = p.categorie_id 
                          GROUP BY c.id 
                          ORDER BY c.nom")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Catégories - <?php echo SITE_NAME; ?></title>
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
                        <a class="nav-link" href="index.php">Gestion Utilisateurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="categories.php">Gestion Catégories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../stocks.php">Retour aux stocks</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($erreur): ?>
            <div class="alert alert-danger"><?php echo $erreur; ?></div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestion des Catégories</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajouterCategorie">
                <i class="bi bi-plus-circle"></i> Nouvelle Catégorie
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Nombre de produits</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $categorie): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($categorie['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($categorie['description'] ?? ''); ?></td>
                                    <td><?php echo $categorie['nb_produits']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary me-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modifierCategorie"
                                                data-categorie-id="<?php echo $categorie['id']; ?>"
                                                data-categorie-nom="<?php echo htmlspecialchars($categorie['nom']); ?>"
                                                data-categorie-description="<?php echo htmlspecialchars($categorie['description'] ?? ''); ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <?php if ($categorie['nb_produits'] == 0): ?>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#supprimerCategorie"
                                                    data-categorie-id="<?php echo $categorie['id']; ?>"
                                                    data-categorie-nom="<?php echo htmlspecialchars($categorie['nom']); ?>">
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

    <div class="modal fade" id="ajouterCategorie" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="ajouter">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
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

    <!-- Modal Modifier Catégorie -->
    <div class="modal fade" id="modifierCategorie" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="modifier">
                    <input type="hidden" name="categorie_id" id="modifier_categorie_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modifier_nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="modifier_nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="modifier_description" class="form-label">Description</label>
                            <textarea class="form-control" id="modifier_description" name="description" rows="3"></textarea>
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

    <div class="modal fade" id="supprimerCategorie" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="supprimer">
                    <input type="hidden" name="categorie_id" id="supprimer_categorie_id">
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer la catégorie <strong id="supprimer_categorie_nom"></strong> ?</p>
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
            const modifierModal = document.getElementById('modifierCategorie');
            if (modifierModal) {
                modifierModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    document.getElementById('modifier_categorie_id').value = button.getAttribute('data-categorie-id');
                    document.getElementById('modifier_nom').value = button.getAttribute('data-categorie-nom');
                    document.getElementById('modifier_description').value = button.getAttribute('data-categorie-description');
                });
            }

            const supprimerModal = document.getElementById('supprimerCategorie');
            if (supprimerModal) {
                supprimerModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    document.getElementById('supprimer_categorie_id').value = button.getAttribute('data-categorie-id');
                    document.getElementById('supprimer_categorie_nom').textContent = button.getAttribute('data-categorie-nom');
                });
            }
        });
    </script>
</body>
</html>
