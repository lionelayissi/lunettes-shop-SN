<?php
session_start();
require_once 'includes/connexion.php';

$panier = $_SESSION['panier'] ?? [];
$produits = [];
$total = 0;

$action = $_GET['action'] ?? '';

// Gestion mise à jour quantité via AJAX
if ($action === 'update') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    $id = isset($input['id']) ? intval($input['id']) : null;
    $quantite = isset($input['quantite']) ? intval($input['quantite']) : 1;

    if (!$id || $quantite < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Données invalides']);
        exit;
    } else {
        $_SESSION['panier'][$id] = $quantite;
        echo json_encode(['status' => 'success']);
        exit;
    }
}

if (!empty($panier)) {
    $ids = array_map('intval', array_keys($panier));
    if (count($ids) > 0) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT * FROM produits WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($produits as $key => $produit) {
            $id = $produit['id'];
            $quantite = $panier[$id];
            $produits[$key]['quantite'] = $quantite;
            $produits[$key]['total'] = $quantite * $produit['prix'];
            $total += $produits[$key]['total'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Mon Panier</title>
    <link rel="stylesheet" href="assets/css/panier.css" />
    <style>
        /* Styles simples pour le tableau et boutons */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        #btn-retour {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #3498db;
            color: white;
            border: none;
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: background 0.3s ease;
            z-index: 1000;
        }
        #btn-retour:hover {
            background: #2980b9;
        }
        h1 { 
            text-align: center; 
            margin-bottom: 20px; 
            margin-top: 60px; /* pour éviter le bouton retour */
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px; 
        }
        th, td { 
            padding: 10px; 
            border-bottom: 1px solid #ddd; 
            text-align: center; 
        }
        img { 
            max-width: 80px; 
            height: auto; 
        }
        input.quantite-input { 
            width: 60px; 
            padding: 5px; 
            text-align: center; 
        }
        .btn-supprimer { 
            color: red; 
            text-decoration: none; 
            font-size: 18px; 
        }
        .total { 
            font-weight: bold; 
            font-size: 1.2em; 
            text-align: right; 
            margin-top: 10px; 
        }
        .actions { 
            display: flex; 
            justify-content: space-between; 
            margin-top: 20px; 
        }
        .btn { 
            padding: 10px 20px; 
            background: #3498db; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            text-decoration: none; 
            cursor: pointer; 
            transition: background 0.3s ease; 
        }
        .btn:hover { 
            background: #2980b9; 
        }
        .btn-valider { 
            background: #27ae60; 
        }
        .btn-valider:hover { 
            background: #1e8449; 
        }
    </style>
</head>
<body>

<button id="btn-retour" onclick="history.back()">← Retour</button>

  <video autoplay muted loop id="bg-video" playsinline>
    <source src="assets/videos/video4.mp4" type="video/mp4" />
    Votre navigateur ne supporte pas la vidéo HTML5.
  </video>

<div class="container panier">
    <h1>🛒 Mon Panier</h1>
    <?php if (empty($produits)): ?>
        <p>Votre panier est vide.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Nom</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produits as $produit): ?>
                <tr>
                    <td><img src="assets/images/<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" /></td>
                    <td><?= htmlspecialchars($produit['nom']) ?></td>
                    <td class="prix-td" data-prix="<?= $produit['prix'] ?>">
                        <?= number_format($produit['prix'], 0, ',', ' ') ?> FCFA
                    </td>
                    <td>
                        <input type="number" min="1" class="quantite-input" data-id="<?= $produit['id'] ?>" value="<?= $produit['quantite'] ?>" />
                    </td>
                    <td><?= number_format($produit['total'], 0, ',', ' ') ?> FCFA</td>
                    <td><a href="supprimer_du_panier.php?id=<?= $produit['id'] ?>" class="btn-supprimer" title="Supprimer">❌</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="total">Total : <?= number_format($total, 0, ',', ' ') ?> FCFA</div>

        <div class="actions">
            <a href="client_dashboard.php" class="btn">Continuer mes achats</a>
            <a href="checkout.php" class="btn btn-valider" onclick="return confirmerAchat();">Valider l'achat</a>
        </div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.quantite-input').forEach(input => {
  input.addEventListener('change', function () {
    const id = this.dataset.id;
    let quantite = parseInt(this.value);
    if (isNaN(quantite) || quantite < 1) {
      quantite = 1;
      this.value = quantite;
    }

    fetch('panier.php?action=update', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id, quantite: quantite })
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        const row = this.closest('tr');
        const prixUnitaire = parseFloat(row.querySelector('.prix-td').dataset.prix);
        const totalLigne = quantite * prixUnitaire;
        row.querySelector('td:nth-child(5)').textContent = totalLigne.toLocaleString('fr-FR') + ' FCFA';

        let totalGlobal = 0;
        document.querySelectorAll('tbody tr').forEach(tr => {
          const prix = parseFloat(tr.querySelector('.prix-td').dataset.prix);
          const qte = parseInt(tr.querySelector('.quantite-input').value);
          totalGlobal += prix * qte;
        });
        document.querySelector('.total').textContent = 'Total : ' + totalGlobal.toLocaleString('fr-FR') + ' FCFA';
      } else {
        alert('Erreur lors de la mise à jour du panier : ' + data.message);
      }
    })
    .catch(() => alert('Erreur réseau.'));
  });
});
</script>

<script>
function confirmerAchat() {
    return confirm("Confirmer l'achat et valider la commande ?");
}
</script>

</body>
</html>
