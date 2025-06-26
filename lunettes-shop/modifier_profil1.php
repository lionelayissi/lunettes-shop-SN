<?php
session_start();
require_once 'includes/connexion.php';

$user_id = $_SESSION['user_id'];

$erreurs = [];
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $mot_de_passe_confirm = $_POST['mot_de_passe_confirm'];

    // Validation simple
    if (empty($nom) || empty($email)) {
        $erreurs[] = "Le nom et l'email sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'email n'est pas valide.";
    }

    // Vérifier le mot de passe si rempli
    if (!empty($mot_de_passe) || !empty($mot_de_passe_confirm)) {
        if ($mot_de_passe !== $mot_de_passe_confirm) {
            $erreurs[] = "Les mots de passe ne correspondent pas.";
        } elseif (strlen($mot_de_passe) < 6) {
            $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères.";
        }
    }

    if (empty($erreurs)) {
        if (!empty($mot_de_passe)) {
            // Mettre à jour mot_de_passe + nom + email
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET nom = ?, email = ?, mot_de_passe = ? WHERE id = ?");
            $stmt->execute([$nom, $email, $mot_de_passe_hash, $user_id]);
        } else {
            // Mettre à jour nom + email seulement
            $stmt = $pdo->prepare("UPDATE users SET nom = ?, email = ? WHERE id = ?");
            $stmt->execute([$nom, $email, $user_id]);
        }
        $_SESSION['user_nom'] = $nom;
        $succes = true;
    }
}

// Récupérer les données utilisateur actuelles
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="profil.css">
    <title>Modifier Profil</title>
    <style>
        /* Style du bouton retour fixé en haut à gauche */
        #btn-retour {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #3498db;
            color: white;
            border: none;
            padding: 8px 14px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            transition: background-color 0.3s ease;
            z-index: 1000;
            text-decoration: none;
            display: inline-block;
        }
        #btn-retour:hover {
            background: #2980b9;
        }

        /* Pour éviter que le formulaire soit masqué derrière le bouton */
        form.profil-form {
            margin-top: 70px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>

<!-- Bouton retour fixé -->
<button id="btn-retour" onclick="history.back()">← Retour</button>

<form method="post" class="profil-form">
    <?php if ($erreurs): ?>
        <div class="error-box">
            <ul>
            <?php foreach ($erreurs as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($succes): ?>
        <div class="success-box">
            Profil mis à jour avec succès !
        </div>
    <?php endif; ?>

    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>

    <label for="email">Email :</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label for="mot_de_passe">Nouveau mot de passe :</label>
    <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Laissez vide pour garder l'actuel">

    <label for="mot_de_passe_confirm">Confirmer le mot de passe :</label>
    <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" placeholder="Confirmez le nouveau mot de passe">

    <button type="submit">Mettre à jour</button>
    <a href="client_dashboard.php" class="btn retour-btn">Retour au tableau de bord</a>
</form>

</body>
</html>
