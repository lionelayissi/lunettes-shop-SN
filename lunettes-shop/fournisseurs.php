<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

require_once 'includes/connexion.php';

// Ajouter un fournisseur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $entreprise = $_POST['entreprise'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];

    $stmt = $pdo->prepare("INSERT INTO fournisseurs (nom, email, mot_de_passe, entreprise, telephone, adresse) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $email, $mot_de_passe, $entreprise, $telephone, $adresse]);
    header("Location: fournisseurs.php");
    exit();
}

// Supprimer un fournisseur
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $pdo->prepare("DELETE FROM fournisseurs WHERE id = ?")->execute([$id]);
    header("Location: fournisseurs.php");
    exit();
}

// Récupérer les données du fournisseur à modifier
$fournisseur_edition = null;
if (isset($_GET['modifier'])) {
    $id = $_GET['modifier'];
    $stmt = $pdo->prepare("SELECT * FROM fournisseurs WHERE id = ?");
    $stmt->execute([$id]);
    $fournisseur_edition = $stmt->fetch();
}

// Modifier un fournisseur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_fournisseur'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $entreprise = $_POST['entreprise'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];

    if (!empty($_POST['mot_de_passe'])) {
        $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE fournisseurs SET nom=?, email=?, mot_de_passe=?, entreprise=?, telephone=?, adresse=? WHERE id=?");
        $stmt->execute([$nom, $email, $mot_de_passe, $entreprise, $telephone, $adresse, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE fournisseurs SET nom=?, email=?, entreprise=?, telephone=?, adresse=? WHERE id=?");
        $stmt->execute([$nom, $email, $entreprise, $telephone, $adresse, $id]);
    }

    header("Location: fournisseurs.php");
    exit();
}

// Récupération des fournisseurs
$fournisseurs = $pdo->query("SELECT * FROM fournisseurs ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Fournisseurs</title>
    <link rel="stylesheet" href="fournisseurs.css">
</head>
<body>

<video autoplay muted loop id="background-video">
    <source src="assets/videos/video5.mp4" type="video/mp4">
    Votre navigateur ne prend pas en charge les vidéos HTML5.
</video>

<div class="overlay">
    <div class="contenu">
        <!-- Bouton Retour -->
        <button 
            onclick="history.back()" 
            style="padding: 8px 12px; margin-bottom: 15px; cursor: pointer; border-radius: 4px; border: none; background-color: #2c3e50; color: white;">
            ← Retour
        </button>

        <h2>👤 Gérer les Fournisseurs</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Entreprise</th>
                    <th>Téléphone</th>
                    <th>Adresse</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fournisseurs as $f): ?>
                    <tr>
                        <td><?= $f['id'] ?></td>
                        <td><?= htmlspecialchars($f['nom']) ?></td>
                        <td><?= htmlspecialchars($f['email']) ?></td>
                        <td><?= htmlspecialchars($f['entreprise']) ?></td>
                        <td><?= htmlspecialchars($f['telephone']) ?></td>
                        <td><?= htmlspecialchars($f['adresse']) ?></td>
                        <td><?= $f['date_creation'] ?></td>
                        <td>
                           <a class="btn-supprimer" href="?supprimer=<?= $f['id'] ?>" onclick="return confirm('Supprimer ce fournisseur ?')">🗑️</a>
                           <a class="btn-modifier" href="?modifier=<?= $f['id'] ?>">✏️</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="form-ajout">
           <form method="post">
    <?php if ($fournisseur_edition): ?>
        <h3>✏️ Modifier le fournisseur #<?= $fournisseur_edition['id'] ?></h3>
        <input type="hidden" name="id" value="<?= $fournisseur_edition['id'] ?>">
        <input type="text" name="nom" placeholder="Nom" value="<?= htmlspecialchars($fournisseur_edition['nom']) ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($fournisseur_edition['email']) ?>" required>
        <input type="password" name="mot_de_passe" placeholder="Nouveau mot de passe (laisser vide pour ne pas changer)">
        <input type="text" name="entreprise" placeholder="Entreprise" value="<?= htmlspecialchars($fournisseur_edition['entreprise']) ?>" required>
        <input type="text" name="telephone" placeholder="Téléphone" value="<?= htmlspecialchars($fournisseur_edition['telephone']) ?>">
        <textarea name="adresse" placeholder="Adresse" rows="3"><?= htmlspecialchars($fournisseur_edition['adresse']) ?></textarea>
        <button type="submit" name="modifier_fournisseur">Enregistrer les modifications</button>
        <a href="fournisseurs.php" style="margin-left: 10px;">Annuler</a>
    <?php else: ?>
        <h3>➕ Ajouter un fournisseur</h3>
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <input type="text" name="entreprise" placeholder="Entreprise" required>
        <input type="text" name="telephone" placeholder="Téléphone">
        <textarea name="adresse" placeholder="Adresse" rows="3"></textarea>
        <button type="submit" name="ajouter">Ajouter</button>
    <?php endif; ?>
</form>
        </div>
    </div>
</div>

</body>
</html>
