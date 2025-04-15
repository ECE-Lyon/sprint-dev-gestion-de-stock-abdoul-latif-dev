<?php
require_once 'config.php';

if (!estConnecte()) {
    $_SESSION['error'] = "Vous devez être connecté pour accéder aux stocks.";
    header('Location: connexion.php');
    exit;
}
$variable = filter_input(INPUT_GET, 'quelquechose', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
$recherche = filter_input(INPUT_GET, 'recherche', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
$tri = filter_input(INPUT_GET, 'tri', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH) ?: 'nom';
$ordre = filter_input(INPUT_GET, 'ordre', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH) ?: 'asc';


$produit = filter_input(INPUT_POST, 'produit', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
$quantite = filter_input(INPUT_POST, 'quantite', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);




$message = '';
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'ajouter_produit') {
        $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
        $quantite = filter_input(INPUT_POST, 'quantite', FILTER_VALIDATE_INT);
        $categorie_id = filter_input(INPUT_POST, 'categorie_id', FILTER_VALIDATE_INT);

        if ($nom && $quantite !== false && $categorie_id) {
            $stmt = $pdo->prepare("INSERT INTO produits (nom, quantite, categorie_id) VALUES (?, ?, ?)");
            if ($stmt->execute([$nom, $quantite, $categorie_id])) {
                $message = "Produit ajouté avec succès";
            } else {
                $erreur = "Erreur lors de l'ajout du produit";
            }
        }
    }
    elseif ($_POST['action'] === 'modifier_quantite' && aPermission(1)) {
        $produit_id = filter_input(INPUT_POST, 'produit_id', FILTER_VALIDATE_INT);
        $nouvelle_quantite = filter_input(INPUT_POST, 'nouvelle_quantite', FILTER_VALIDATE_INT);

        if ($produit_id && $nouvelle_quantite !== false) {
            $stmt = $pdo->prepare("SELECT quantite FROM produits WHERE id = ?");
            $stmt->execute([$produit_id]);
            $ancienne_quantite = $stmt->fetchColumn();

            $stmt = $pdo->prepare("UPDATE produits SET quantite = ? WHERE id = ?");
            if ($stmt->execute([$nouvelle_quantite, $produit_id])) {
                $message = "Quantité mise à jour";
            }
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll();

$sql = "
    SELECT p.*, c.nom as categorie_nom 
    FROM produits p 
    JOIN categories c ON p.categorie_id = c.id 
    WHERE 1=1
";

if ($recherche) {
    $sql .= " AND (p.nom LIKE :recherche OR c.nom LIKE :recherche)";
}

$sql .= " ORDER BY " . ($tri == 'categorie_nom' ? 'c.nom' : 'p.'.$tri) . " $ordre";

$stmt = $pdo->prepare($sql);

if ($recherche) {
    $stmt->bindValue(':recherche', "%$recherche%", PDO::PARAM_STR);
}

$stmt->execute();
$produits = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Stocks - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($erreur): ?>
            <div class="alert alert-danger"><?php echo $erreur; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="recherche" 
                                       value="<?php echo htmlspecialchars($recherche ?? ''); ?>" 
                                       placeholder="Rechercher un produit...">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="tri">
                                    <option value="nom" <?php echo $tri == 'nom' ? 'selected' : ''; ?>>Trier par nom</option>
                                    <option value="categorie_nom" <?php echo $tri == 'categorie_nom' ? 'selected' : ''; ?>>Trier par catégorie</option>
                                    <option value="quantite" <?php echo $tri == 'quantite' ? 'selected' : ''; ?>>Trier par quantité</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="ordre">
                                    <option value="asc" <?php echo $ordre == 'asc' ? 'selected' : ''; ?>>Ordre croissant</option>
                                    <option value="desc" <?php echo $ordre == 'desc' ? 'selected' : ''; ?>>Ordre décroissant</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php if (peutModifierStock()): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Ajouter un produit</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="ajouter_produit">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom du produit</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                            <div class="mb-3">
                                <label for="quantite" class="form-label">Quantité</label>
                                <input type="number" class="form-control" id="quantite" name="quantite" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label for="categorie_id" class="form-label">Catégorie</label>
                                <select class="form-select" id="categorie_id" name="categorie_id" required>
                                    <?php foreach ($categories as $categorie): ?>
                                        <option value="<?php echo $categorie['id']; ?>">
                                            <?php echo htmlspecialchars($categorie['nom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">État des stocks</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Catégorie</th>
                                        <th>Quantité</th>
                                        <?php if (peutModifierStock()): ?>
                                            <th>Actions</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($produits as $produit): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                                            <td><?php echo htmlspecialchars($produit['categorie_nom']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $produit['quantite'] > 10 ? 'success' : ($produit['quantite'] > 5 ? 'warning' : 'danger'); ?>">
                                                    <?php echo $produit['quantite']; ?>
                                                </span>
                                            </td>
                                            <?php if (peutModifierStock()): ?>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modifierQuantite" 
                                                            data-produit-id="<?php echo $produit['id']; ?>"
                                                            data-produit-nom="<?php echo htmlspecialchars($produit['nom']); ?>"
                                                            data-produit-quantite="<?php echo $produit['quantite']; ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (aPermission(1)): ?>
    <div class="modal fade" id="modifierQuantite" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la quantité</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="modifier_quantite">
                        <input type="hidden" name="produit_id" id="modal_produit_id">
                        <p id="modal_produit_nom"></p>
                        <div class="mb-3">
                            <label for="nouvelle_quantite" class="form-label">Nouvelle quantité</label>
                            <input type="number" class="form-control" id="nouvelle_quantite" name="nouvelle_quantite" min="0" required>
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
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modifierQuantiteModal = document.getElementById('modifierQuantite');
            if (modifierQuantiteModal) {
                modifierQuantiteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const produitId = button.getAttribute('data-produit-id');
                    const produitNom = button.getAttribute('data-produit-nom');
                    const produitQuantite = button.getAttribute('data-produit-quantite');

                    document.getElementById('modal_produit_id').value = produitId;
                    document.getElementById('modal_produit_nom').textContent = `Produit : ${produitNom}`;
                    document.getElementById('nouvelle_quantite').value = produitQuantite;
                });
            }
        });
    </script>
</body>
</html>
