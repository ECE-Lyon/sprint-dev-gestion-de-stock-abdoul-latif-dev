<?php
require_once 'config.php';

if (!estConnecte()) {
    $_SESSION['error'] = "Vous devez être connecté pour accéder aux stocks.";
    header('Location: connexion.php');
    exit;
}

$page_title = 'Gestion des Stocks'; // Définir le titre de la page

$variable = filter_input(INPUT_GET, 'quelquechose', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
$recherche = filter_input(INPUT_GET, 'recherche', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
$tri = filter_input(INPUT_GET, 'tri', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH) ?: 'nom';
$ordre = filter_input(INPUT_GET, 'ordre', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH) ?: 'asc';

$produit = filter_input(INPUT_POST, 'produit', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
$quantite = filter_input(INPUT_POST, 'quantite', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

$message = '';
$erreur = '';

// Traitement des actions POST
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

// Récupération des données
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

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="main-container">
    <?php if ($message): ?>
        <div class="alert alert-success animate__animated animate__fadeIn"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <div class="alert alert-danger animate__animated animate__fadeIn"><?php echo $erreur; ?></div>
    <?php endif; ?>

    <div class="animated-card animate__animated animate__fadeIn">
        <div class="card-header bg-primary text-white p-3">
            <h4 class="mb-0 animated-title text-white">Gestion des Stocks</h4>
        </div>
        <div class="card-body p-4">
            <!-- Formulaire de recherche -->
            <div class="row mb-4 animated-element delay-1">
                <div class="col-md-6">
                    <form action="" method="GET" class="d-flex">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="recherche" class="form-control" placeholder="Rechercher..." value="<?php echo htmlspecialchars($recherche ?? ''); ?>">
                            <button type="submit" class="btn btn-primary">Rechercher</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-md-end">
                    <button type="button" class="btn btn-success btn-animated" data-bs-toggle="modal" data-bs-target="#ajouterProduitModal">
                        <span><i class="bi bi-plus-circle"></i> Ajouter un produit</span>
                        <div class="spinner-border spinner-border-sm loading-spinner" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Tableau des produits -->
            <div class="table-responsive animated-element delay-2">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>
                                <a href="?tri=nom&ordre=<?php echo $tri == 'nom' && $ordre == 'asc' ? 'desc' : 'asc'; ?>&recherche=<?php echo urlencode($recherche ?? ''); ?>">
                                    Nom <?php echo $tri == 'nom' ? ($ordre == 'asc' ? '↑' : '↓') : ''; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?tri=categorie_nom&ordre=<?php echo $tri == 'categorie_nom' && $ordre == 'asc' ? 'desc' : 'asc'; ?>&recherche=<?php echo urlencode($recherche ?? ''); ?>">
                                    Catégorie <?php echo $tri == 'categorie_nom' ? ($ordre == 'asc' ? '↑' : '↓') : ''; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?tri=quantite&ordre=<?php echo $tri == 'quantite' && $ordre == 'asc' ? 'desc' : 'asc'; ?>&recherche=<?php echo urlencode($recherche ?? ''); ?>">
                                    Quantité <?php echo $tri == 'quantite' ? ($ordre == 'asc' ? '↑' : '↓') : ''; ?>
                                </a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produits as $produit): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                            <td><?php echo htmlspecialchars($produit['categorie_nom']); ?></td>
                            <td><?php echo htmlspecialchars($produit['quantite']); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary btn-animated" data-bs-toggle="modal" data-bs-target="#modifierQuantiteModal" data-id="<?php echo $produit['id']; ?>" data-nom="<?php echo htmlspecialchars($produit['nom']); ?>" data-quantite="<?php echo $produit['quantite']; ?>">
                                    <span><i class="bi bi-pencil"></i></span>
                                    <div class="spinner-border spinner-border-sm loading-spinner" role="status">
                                        <span class="visually-hidden">Chargement...</span>
                                    </div>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($produits)): ?>
                        <tr>
                            <td colspan="4" class="text-center">Aucun produit trouvé</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modals pour ajouter et modifier (conservez le code existant) -->
    <!-- Modal Ajouter Produit -->
    <div class="modal fade" id="ajouterProduitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un produit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
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
                                <option value="">Choisir une catégorie</option>
                                <?php foreach ($categories as $categorie): ?>
                                <option value="<?php echo $categorie['id']; ?>"><?php echo htmlspecialchars($categorie['nom']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary btn-animated">
                            <span>Enregistrer</span>
                            <div class="spinner-border spinner-border-sm loading-spinner" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Modifier Quantité -->
    <div class="modal fade" id="modifierQuantiteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la quantité</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="modifier_quantite">
                        <input type="hidden" id="produit_id" name="produit_id">
                        <p>Produit: <span id="produit_nom"></span></p>
                        <div class="mb-3">
                            <label for="nouvelle_quantite" class="form-label">Nouvelle quantité</label>
                            <input type="number" class="form-control" id="nouvelle_quantite" name="nouvelle_quantite" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary btn-animated">
                            <span>Enregistrer</span>
                            <div class="spinner-border spinner-border-sm loading-spinner" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
