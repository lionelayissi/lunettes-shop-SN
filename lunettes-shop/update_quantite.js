document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.quantite-input').forEach(input => {
    input.addEventListener('change', () => {
      const id = input.getAttribute('data-id');
      const quantite = input.value;

      fetch('panier_backend.php?action=update', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${id}&quantite=${quantite}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          location.reload(); // Recharge pour mettre à jour les totaux
        } else {
          alert('Erreur : ' + data.message);
        }
      })
      .catch(() => {
        alert('Erreur réseau.');
      });
    });
  });
});
