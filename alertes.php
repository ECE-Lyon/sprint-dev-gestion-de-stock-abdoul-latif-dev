<?php
require_once 'config.php';

// Vérifier que l'utilisateur est connecté
if (!estConnecte()) {
    $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page.";
    header('Location: connexion.php');
    exit;
}

$page_title = 'Alertes de stock';

// Récupérer les produits en alerte (quantité inférieure ou égale à 5 par exemple)
$seuil_alerte = 5;
$query = "SELECT p.*, c.nom as categorie_nom 
          FROM produits p 
          LEFT JOIN categories c ON p.categorie_id = c.id 
          WHERE p.quantite <= ? 
          ORDER BY p.quantite ASC";
$stmt = $pdo->prepare($query);
$stmt->execute([$seuil_alerte]);
$produits_alerte = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="main-container">
    <div class="animated-card animate__animated animate__fadeIn">
        <div class="card-header bg-danger text-white p-3">
            <h4 class="mb-0 text-white">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Alertes de stock
            </h4>
        </div>
        <div class="card-body p-4">
            <?php if (empty($produits_alerte)): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Aucune alerte pour le moment. Tous les produits sont en stock suffisant.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Produit</th>
                                <th>Catégorie</th>
                                <th class="text-center">Stock actuel</th>
                                <th class="text-center">Seuil d'alerte</th>
                                <th class="text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produits_alerte as $produit): ?>
                                <tr class="align-middle">
                                    <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($produit['categorie_nom'] ?? 'Non catégorisé'); ?></td>
                                    <td class="text-center fw-bold"><?php echo $produit['quantite']; ?></td>
                                    <td class="text-center"><?php echo $seuil_alerte; ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                            Stock critique
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
