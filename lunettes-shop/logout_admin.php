<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Déconnexion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #222;
            color: white;
            display: flex;
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
        button {
            margin-top: 20px;
            padding: 12px 24px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            background-color: #0af;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #06c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Vous êtes déconnecté.</h1>
        <button onclick="history.back()">⬅️ Retour</button>
    </div>
</body>
</html>
