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
$alerte = isset($_GET['alerte']) && $_GET['alerte'] == 1; // Filtre pour les produits en alerte

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
        $raison = filter_input(INPUT_POST, 'raison', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'Ajout initial';

        if ($nom && $quantite !== false && $categorie_id) {
            try {
                $pdo->beginTransaction();
                
                // Ajout du produit
                $stmt = $pdo->prepare("INSERT INTO produits (nom, quantite, categorie_id) VALUES (?, ?, ?)");
                if ($stmt->execute([$nom, $quantite, $categorie_id])) {
                    $produit_id = $pdo->lastInsertId();
                    
                    // Enregistrement dans l'historique
                    $stmt = $pdo->prepare("INSERT INTO historique_stock (produit_id, utilisateur_id, ancienne_quantite, nouvelle_quantite, raison) 
                                         VALUES (?, ?, 0, ?, ?)");
                    $stmt->execute([
                        $produit_id,
                        $_SESSION['user_id'],
                        $quantite,
                        $raison
                    ]);
                    
                    $pdo->commit();
                    $message = "Produit ajouté avec succès";
                } else {
                    throw new Exception("Erreur lors de l'ajout du produit");
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                $erreur = "Erreur lors de l'ajout du produit: " . $e->getMessage();
            }
        } else {
            $erreur = "Veuillez remplir tous les champs correctement";
        }
    }
    elseif ($_POST['action'] === 'modifier_quantite' && aPermission(1)) {
        $produit_id = filter_input(INPUT_POST, 'produit_id', FILTER_VALIDATE_INT);
        $nouvelle_quantite = filter_input(INPUT_POST, 'nouvelle_quantite', FILTER_VALIDATE_INT);
        $raison = filter_input(INPUT_POST, 'raison', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'Modification manuelle';

        if ($produit_id && $nouvelle_quantite !== false) {
            try {
                $pdo->beginTransaction();
                
                // Récupération de l'ancienne quantité
                $stmt = $pdo->prepare("SELECT quantite FROM produits WHERE id = ? FOR UPDATE");
                $stmt->execute([$produit_id]);
                $ancienne_quantite = $stmt->fetchColumn();

                if ($ancienne_quantite === false) {
                    throw new Exception("Produit introuvable");
                }

                // Mise à jour de la quantité
                $stmt = $pdo->prepare("UPDATE produits SET quantite = ? WHERE id = ?");
                if ($stmt->execute([$nouvelle_quantite, $produit_id])) {
                    // Enregistrement dans l'historique
                    $stmt = $pdo->prepare("INSERT INTO historique_stock (produit_id, utilisateur_id, ancienne_quantite, nouvelle_quantite, raison) 
                                         VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $produit_id,
                        $_SESSION['user_id'],
                        $ancienne_quantite,
                        $nouvelle_quantite,
                        $raison
                    ]);
                    
                    $pdo->commit();
                    $message = "Quantité mise à jour avec succès";
                } else {
                    throw new Exception("Erreur lors de la mise à jour de la quantité");
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                $erreur = "Erreur lors de la mise à jour de la quantité: " . $e->getMessage();
            }
        } else {
            $erreur = "Données invalides pour la mise à jour";
        }
    }
}

// Récupération des catégories
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll();

// Construction de la requête SQL
$query = "SELECT p.*, c.nom as categorie_nom 
          FROM produits p 
          LEFT JOIN categories c ON p.categorie_id = c.id 
          WHERE 1=1";

$params = [];

// Filtre de recherche
if ($recherche) {
    $query .= " AND (p.nom LIKE ? OR c.nom LIKE ?)";
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
}

// Filtre pour les produits en alerte
if (isset($_GET['alerte']) && $_GET['alerte'] == 1) {
    $query .= " AND p.quantite <= p.seuil_alerte";
    $page_title = 'Produits en alerte de stock';
}

// Ajout du tri
$query .= " ORDER BY " . ($tri == 'categorie_nom' ? 'c.nom' : 'p.'.$tri) . " $ordre";

// Exécution de la requête
$stmt = $pdo->prepare($query);
$stmt->execute($params);
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
                        <div class="mb-3">
                            <label for="raison" class="form-label">Raison de la modification</label>
                            <input type="text" class="form-control" id="raison" name="raison" placeholder="Ex: Réapprovisionnement, Vente, etc." required>
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
