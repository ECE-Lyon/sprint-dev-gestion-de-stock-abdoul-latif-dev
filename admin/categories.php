<?php
require_once '../config.php';

if (!aPermission(3)) {
    header('Location: ../index.php');
    exit;
}

$page_title = 'Administration - Gestion Catégories'; // Définir le titre de la page

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

include '../includes/header.php';
include '../includes/navbar.php'; // Assurez-vous que cette ligne est présente
?>

<div class="main-container">
    <!-- Menu de navigation d'administration -->
    <div class="admin-nav mb-4 animated-element">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Gestion des Utilisateurs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="categories.php">Gestion des Catégories</a>
            </li>
        </ul>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success animate__animated animate__fadeIn"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <div class="alert alert-danger animate__animated animate__fadeIn"><?php echo $erreur; ?></div>
    <?php endif; ?>

    <div class="animated-card animate__animated animate__fadeIn">
        <div class="card-header bg-primary text-white p-3">
            <h4 class="mb-0 animated-title text-white">Gestion des Catégories</h4>
        </div>
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 animated-element delay-1">
                <p class="lead mb-0">Gérez les catégories de produits</p>
                <button type="button" class="btn btn-primary btn-animated" data-bs-toggle="modal" data-bs-target="#ajouterCategorie">
                    <span><i class="bi bi-plus-circle"></i> Nouvelle Catégorie</span>
                    <div class="spinner-border spinner-border-sm loading-spinner" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </button>
            </div>

            <div class="table-responsive animated-element delay-2">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Produits</th>
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
                                    <button type="button" class="btn btn-sm btn-primary me-1 btn-animated" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modifierCategorie"
                                            data-id="<?php echo $categorie['id']; ?>"
                                            data-nom="<?php echo htmlspecialchars($categorie['nom']); ?>"
                                            data-description="<?php echo htmlspecialchars($categorie['description'] ?? ''); ?>">
                                        <span><i class="bi bi-pencil"></i></span>
                                        <div class="spinner-border spinner-border-sm loading-spinner" role="status">
                                            <span class="visually-hidden">Chargement...</span>
                                        </div>
                                    </button>
                                    <?php if ($categorie['nb_produits'] == 0): ?>
                                        <button type="button" class="btn btn-sm btn-danger btn-animated"
                                                data-bs-toggle="modal"
                                                data-bs-target="#supprimerCategorie"
                                                data-id="<?php echo $categorie['id']; ?>"
                                                data-nom="<?php echo htmlspecialchars($categorie['nom']); ?>">
                                            <span><i class="bi bi-trash"></i></span>
                                            <div class="spinner-border spinner-border-sm loading-spinner" role="status">
                                                <span class="visually-hidden">Chargement...</span>
                                            </div>
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

    <!-- Modals (conservez le code existant des modals) -->
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
                    <input type="hidden" name="categorie_id" id="edit_categorie_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="edit_nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
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
                    <input type="hidden" name="categorie_id" id="delete_categorie_id">
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer la catégorie <strong class="categorie-name"></strong> ?</p>
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
</div>

<?php include '../includes/footer.php'; ?>