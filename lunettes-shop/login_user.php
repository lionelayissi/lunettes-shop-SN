<?php
session_start();
require_once 'includes/connexion.php';

$message = '';
$inscriptionMessage = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Connexion
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            header("Location: client_dashboard.php");
            exit();
        } else {
            $message = "Email ou mot de passe incorrect.";
        }
    }

    // Inscription
    if (isset($_POST['register'])) {
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

        // Vérifier si l'email existe déjà
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $inscriptionMessage = "Cet email est déjà utilisé.";
        } else {
            $insert = $pdo->prepare("INSERT INTO users (nom, email, mot_de_passe) VALUES (?, ?, ?)");
            if ($insert->execute([$nom, $email, $mot_de_passe])) {
                $inscriptionMessage = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            } else {
                $inscriptionMessage = "Erreur lors de l'inscription.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Connexion / Inscription - SHABS OPTIQUE 237</title>
    <link rel="stylesheet" href="assets/css/conn_u.css">
    <style>
        .form-toggle {
            margin-top: 10px;
            color: blue;
            cursor: pointer;
        }
        .hidden {
            display: none;
        }
        input, button {
            margin: 8px 0;
            padding: 10px;
            width: 250px;
        }
        /* Style bouton Retour */
        .btn-retour {
            margin-top: 25px;
            background-color: #444;
            border: none;
            padding: 12px 25px;
            color: white;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-retour:hover {
            background-color: #222;
        }
    </style>
</head>
<body>
    <video autoplay muted loop class="bg-video">
        <source src="assets/videos/video4.mp4" type="video/mp4">
        Votre navigateur ne supporte pas la vidéo HTML5.
    </video>
    <div class="login-container">
        <h2>SHABS OPTIQUE 237</h2>

        <!-- Formulaire de Connexion -->
        <div id="login-form">
            <h3>Connexion</h3>
            <?php if ($message): ?><p style="color: red;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
            <form method="POST">
                <input type="email" name="email" placeholder="Email" required><br>
                <input type="password" name="mot_de_passe" placeholder="Mot de passe" required><br>
                <button type="submit" name="login">Se connecter</button>
            </form>
            <p class="form-toggle" onclick="toggleForms()">Créer un nouveau compte</p>
        </div>

        <!-- Formulaire d’Inscription -->
        <div id="register-form" class="hidden">
            <h3>Créer un compte</h3>
            <?php if ($inscriptionMessage): ?><p style="color: green;"><?= htmlspecialchars($inscriptionMessage) ?></p><?php endif; ?>
            <form method="POST">
                <input type="text" name="nom" placeholder="Nom complet" required><br>
                <input type="email" name="email" placeholder="Email" required><br>
                <input type="password" name="mot_de_passe" placeholder="Mot de passe" required><br>
                <button type="submit" name="register">S’inscrire</button>
            </form>
            <p class="form-toggle" onclick="toggleForms()">Déjà un compte ? Se connecter</p>
        </div>

        <!-- Bouton Retour -->
        <button class="btn-retour" onclick="history.back()">⬅️ Retour</button>
    </div>

    <script>
        function toggleForms() {
            document.getElementById('login-form').classList.toggle('hidden');
            document.getElementById('register-form').classList.toggle('hidden');
        }
    </script>
</body>
</html>
