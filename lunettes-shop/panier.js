document.addEventListener('DOMContentLoaded', () => {
  const produitsList = document.getElementById('produits-list');

  produitsList.addEventListener('click', (e) => {
    if (e.target.classList.contains('add-to-cart-btn')) {
      const id = e.target.getAttribute('data-id');

      fetch('ajouter_panier.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${id}&quantite=1`
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          alert('Produit ajouté au panier !');
        } else {
          alert('Erreur : ' + data.message);
        }
      })
      .catch(() => {
        alert('Erreur réseau.');
      });
    }
  });
});
