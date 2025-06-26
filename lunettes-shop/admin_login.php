<?php
session_start();

require_once 'includes/connexion.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email'], $_POST['mot_de_passe'])) {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $query = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $query->execute([$email]);
    $admin = $query->fetch();

    if ($admin && password_verify($mot_de_passe, $admin['mot_de_passe'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nom'] = $admin['nom'];
        header("Location: dashboard_admin.php");
        exit();
    } else {
        $message = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Connexion Admin</title>
    <style>
        /* Reset */
        * {
            box-sizing: border-box;
        }
        body, html {
            margin: 0; padding: 0; height: 100%; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #111;
            color: #eee;
            overflow-x: hidden;
        }
        #background-video {
            position: fixed;
            right: 0; bottom: 0;
            min-width: 100%; min-height: 100%;
            z-index: -1;
            object-fit: cover;
            filter: brightness(0.5);
        }
        .login-container {
            position: relative;
            max-width: 400px;
            margin: 8vh auto;
            background-color: rgba(0,0,0,0.75);
            padding: 30px 35px;
            border-radius: 10px;
            box-shadow: 0 0 20px #000;
            text-align: center;
        }
        h2 {
            margin-bottom: 25px;
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: 1px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input[type="email"],
        input[type="password"] {
            padding: 12px 15px;
            border-radius: 6px;
            border: none;
            font-size: 1rem;
            outline: none;
        }
        input[type="email"]:focus,
        input[type="password"]:focus {
            box-shadow: 0 0 5px #3f51b5;
        }
        button[type="submit"] {
            margin-top: 10px;
            padding: 12px 0;
            border: none;
            border-radius: 6px;
            background-color: #3f51b5;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button[type="submit"]:hover {
            background-color: #303f9f;
        }
        .error {
            background-color: #b00020;
            color: #fff;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: 600;
        }
        button.retour {
            margin-top: 20px;
            background: none;
            border: none;
            color: #bbb;
            font-size: 0.9rem;
            cursor: pointer;
            user-select: none;
            transition: color 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        button.retour:hover {
            color: #fff;
            text-decoration: underline;
        }
        @media (max-width: 480px) {
            .login-container {
                margin: 5vh 10px;
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>

<video autoplay muted loop id="background-video" playsinline>
    <source src="assets/videos/video5.mp4" type="video/mp4" />
    Votre navigateur ne supporte pas les vidéos HTML5.
</video>

<div class="login-container">  
    <h2>Connexion Administrateur</h2>
    <?php if ($message): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <input type="email" name="email" placeholder="Email" required autocomplete="username" />
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required autocomplete="current-password" />
        <button type="submit">Se connecter</button>
    </form>
    <button type="button" class="retour" onclick="history.back()">← Retour</button>
</div>
</body>
</html>
