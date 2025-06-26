<?php
session_start();

// Supprime toutes les variables de session
$_SESSION = [];

// Détruit la session
session_destroy();

// Supprime le cookie de session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Déconnexion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #222;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: rgba(0,0,0,0.7);
            padding: 30px;
            border-radius: 12px;
            text-align: center;
        }
        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background: #0af;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        a.button:hover {
            background: #06c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Vous êtes déconnecté.</h1>
        <p>Merci d'être passé !</p>
        <a href="fournisseur_login.php" class="button">Retour à la page de connexion</a>
    </div>
</body>
</html>
