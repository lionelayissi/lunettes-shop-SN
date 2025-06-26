<?php
session_start();
require_once 'includes/connexion.php';

if (isset($_POST['modifier_profil'])) {
    $id = $_SESSION['user_id'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $ancien_mdp = $_POST['ancien_mdp'];
    $nouveau_mdp = $_POST['nouveau_mdp'];

    // Récupérer l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if ($user && password_verify($ancien_mdp, $user['mot_de_passe'])) {
        // Si nouveau mot de passe renseigné, on le met à jour, sinon on garde l'ancien
        $nouveau_hash = !empty($nouveau_mdp) ? password_hash($nouveau_mdp, PASSWORD_DEFAULT) : $user['mot_de_passe'];

        $update = $pdo->prepare("UPDATE users SET nom = ?, email = ?, mot_de_passe = ? WHERE id = ?");
        $update->execute([$nom, $email, $nouveau_hash, $id]);

        $_SESSION['user_nom'] = $nom;
        header("Location: ../client_dashboard.php?success=1");
        exit();
    } else {
        header("Location: ../client_dashboard.php?error=1");
        exit();
    }
}
?>
