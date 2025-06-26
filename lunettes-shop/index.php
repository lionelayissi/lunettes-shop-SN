<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/style.css">

<?php
session_start();
require_once 'includes/connexion.php';

$message = '';
?>

<div class="hero-section">
  <video autoplay muted loop class="background-video">
    <source src="assets/videos/lunettes.mp4" type="video/mp4">
    Votre navigateur ne supporte pas les vidéos HTML5.
  </video>

  <!-- Titre animé -->
  <div class="overlay">
    <h1 class="animated-title">SHABS OPTIQUE 237</h1>
    <a href="admin_login.php" class="admin-btn">Admin</a>
  </div>
</div>

<!-- Section Produits -->
<section class="produits-section">
  <h2>Nos Produits en vedette</h2>

  <div class="produits-container" id="produits-list">
    <!-- Les produits s'affichent ici dynamiquement -->
  </div>

  <div class="voir-plus-container">
    <a href="login_user.php" class="voir-plus-btn">Voir plus</a>
  </div>
</section>

<!-- Bouton Retour -->
<div style="text-align:center; margin: 30px 0;">
  <button onclick="history.back()" 
          style="background-color:#444; color:#fff; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold; transition: background-color 0.3s ease;">
    ⬅️ Retour
  </button>
</div>

<script>
$(document).ready(function(){
    $.ajax({
        url: "includes/get_produits.php",  // Le script PHP qui récupère les produits
        method: "GET",
        dataType: "json",
        success: function(data) {
            let html = '';
            data.forEach(function(p){
                html += `
                    <div class="produit-card">
                        <img src="assets/images/${p.image}" alt="${p.nom}">
                        <h3 class="produit-nom">${p.nom}</h3>
                        <p class="produit-marque">Marque : ${p.marque}</p>
                        <p class="produit-description">${p.description}</p>
                        <p class="produit-prix">${p.prix} FCFA</p>
                        <p class="produit-categorie">${p.categorie}</p>
                    </div>`;
            });
            $('#produits-list').html(html); // On injecte le HTML ici
        },
        error: function() {
            $('#produits-list').html('<p>Erreur lors du chargement des produits.</p>');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
