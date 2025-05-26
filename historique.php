<?php
require_once 'config.php';

// Vérifier que l'utilisateur est connecté et a les permissions nécessaires
if (!estConnecte()) {
    $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page.";
    header('Location: connexion.php');
    exit;
}

$page_title = 'Historique des mouvements de stock';

// Récupérer les paramètres de filtrage
$produit_id = filter_input(INPUT_GET, 'produit_id', FILTER_VALIDATE_INT);
$utilisateur_id = filter_input(INPUT_GET, 'utilisateur_id', FILTER_VALIDATE_INT);
$date_debut = filter_input(INPUT_GET, 'date_debut');
$date_fin = filter_input(INPUT_GET, 'date_fin');

// Construction de la requête SQL
$query = "SELECT h.*, p.nom as produit_nom, u.prenom, u.nom as utilisateur_nom 
          FROM historique_stock h
          JOIN produits p ON h.produit_id = p.id
          JOIN utilisateurs u ON h.utilisateur_id = u.id
          WHERE 1=1";

$params = [];

// Filtres
if ($produit_id) {
    $query .= " AND h.produit_id = ?";
    $params[] = $produit_id;
}

if ($utilisateur_id) {
    $query .= " AND h.utilisateur_id = ?";
    $params[] = $utilisateur_id;
}

if ($date_debut) {
    $query .= " AND h.date_modification >= ?";
    $params[] = $date_debut;
}

if ($date_fin) {
    $query .= " AND h.date_modification <= ?";
    $params[] = $date_fin . ' 23:59:59';
}

$query .= " ORDER BY h.date_modification DESC";

// Exécution de la requête
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$historique = $stmt->fetchAll();

// Récupération des listes pour les filtres
$produits = $pdo->query("SELECT id, nom FROM produits ORDER BY nom")->fetchAll();
$utilisateurs = $pdo->query("SELECT id, nom, prenom FROM utilisateurs ORDER BY nom, prenom")->fetchAll();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="main-container">
    <div class="animated-card animate__animated animate__fadeIn">
        <div class="card-header bg-primary text-white p-3">
            <h4 class="mb-0 animated-title text-white">Historique des mouvements de stock</h4>
        </div>
        <div class="card-body p-4">
            <!-- Formulaire de filtrage -->
            <form method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Produit</label>
                        <select name="produit_id" class="form-select">
                            <option value="">Tous les produits</option>
                            <?php foreach ($produits as $produit): ?>
                                <option value="<?php echo $produit['id']; ?>" <?php echo ($produit_id == $produit['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($produit['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Utilisateur</label>
                        <select name="utilisateur_id" class="form-select">
                            <option value="">Tous les utilisateurs</option>
                            <?php foreach ($utilisateurs as $user): ?>
                                <option value="<?php echo $user['id']; ?>" <?php echo ($utilisateur_id == $user['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date début</label>
                        <input type="date" name="date_debut" class="form-control" value="<?php echo htmlspecialchars($date_debut ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date fin</label>
                        <input type="date" name="date_fin" class="form-control" value="<?php echo htmlspecialchars($date_fin ?? ''); ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                        <a href="historique.php" class="btn btn-outline-secondary">Réinitialiser</a>
                    </div>
                </div>
            </form>

            <!-- Tableau d'historique -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Produit</th>
                            <th>Utilisateur</th>
                            <th class="text-end">Ancienne quantité</th>
                            <th class="text-end">Nouvelle quantité</th>
                            <th>Variation</th>
                            <th>Raison</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($historique)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Aucun mouvement trouvé</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($historique as $mouvement): 
                                $variation = $mouvement['nouvelle_quantite'] - $mouvement['ancienne_quantite'];
                                $classe_variation = $variation > 0 ? 'text-success' : ($variation < 0 ? 'text-danger' : '');
                                $icone_variation = $variation > 0 ? 'bi-arrow-up' : ($variation < 0 ? 'bi-arrow-down' : 'bi-dash');
                            ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($mouvement['date_modification'])); ?></td>
                                    <td><?php echo htmlspecialchars($mouvement['produit_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($mouvement['prenom'] . ' ' . $mouvement['utilisateur_nom']); ?></td>
                                    <td class="text-end"><?php echo $mouvement['ancienne_quantite']; ?></td>
                                    <td class="text-end"><?php echo $mouvement['nouvelle_quantite']; ?></td>
                                    <td class="<?php echo $classe_variation; ?>">
                                        <i class="bi <?php echo $icone_variation; ?>"></i>
                                        <?php echo abs($variation); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($mouvement['raison'] ?: '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
