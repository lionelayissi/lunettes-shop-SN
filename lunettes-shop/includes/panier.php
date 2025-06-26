<?php
session_start();
header('Content-Type: application/json');

// Initialiser le panier si pas encore existant
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$action = $_GET['action'] ?? '';

// Connexion base de données (ajuste selon ta config)
try {
    $pdo = new PDO('mysql:host=localhost;dbname=ta_base;charset=utf8', 'ton_user', 'ton_mdp', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur base de données']);
    exit;
}

switch ($action) {

    case 'add':
        $id = $_POST['id'] ?? null;
        $quantite = intval($_POST['quantite'] ?? 1);
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID produit manquant']);
            exit;
        }

        // Vérifier que le produit existe en base
        $stmt = $pdo->prepare('SELECT id FROM produits WHERE id = ? AND stock > 0');
        $stmt->execute([$id]);
        $produit = $stmt->fetch();
        if (!$produit) {
            echo json_encode(['status' => 'error', 'message' => 'Produit non trouvé ou en rupture']);
            exit;
        }

        // Ajouter ou incrémenter la quantité dans le panier
        if (isset($_SESSION['panier'][$id])) {
            $_SESSION['panier'][$id] += $quantite;
        } else {
            $_SESSION['panier'][$id] = $quantite;
        }

        echo json_encode(['status' => 'success']);
        break;

    case 'remove':
        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID produit manquant']);
            exit;
        }
        if (isset($_SESSION['panier'][$id])) {
            unset($_SESSION['panier'][$id]);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Produit non dans le panier']);
        }
        break;

    case 'get':
        $panier_detail = [];

        if (empty($_SESSION['panier'])) {
            echo json_encode(['status' => 'success', 'panier' => []]);
            exit;
        }

        // Récupérer les infos produit pour chaque id dans le panier
        $ids = array_keys($_SESSION['panier']);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $pdo->prepare("SELECT id, nom, prix, image FROM produits WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($produits as $p) {
            $qte = $_SESSION['panier'][$p['id']] ?? 0;
            $total = $p['prix'] * $qte;
            $panier_detail[] = [
                'id' => $p['id'],
                'nom' => $p['nom'],
                'prix' => $p['prix'],
                'quantite' => $qte,
                'total' => $total,
                'image' => $p['image']
            ];
        }

        echo json_encode(['status' => 'success', 'panier' => $panier_detail]);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Action inconnue']);
break;
}
