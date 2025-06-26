// ========== FILTRAGE LIVE ==========
const searchInput = document.getElementById('searchInput');
const usersTable = document.getElementById('usersTable');
const rows = usersTable.querySelectorAll('tbody tr');

searchInput.addEventListener('input', () => {
    const filter = searchInput.value.toLowerCase();
    rows.forEach(row => {
        const nom = row.querySelector('.user-name').textContent.toLowerCase();
        const email = row.cells[1].textContent.toLowerCase();
        if (nom.includes(filter) || email.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// ========== TRI SIMPLIFIÉ ==========
const getCellValue = (tr, idx) => tr.children[idx].textContent || tr.children[idx].innerText;
const comparer = (idx, asc) => (a, b) => ((v1, v2) =>
    v1 !== '' && v2 !== '' && !isNaN(Date.parse(v1)) && !isNaN(Date.parse(v2)) 
        ? new Date(v1) - new Date(v2)
        : v1.toString().localeCompare(v2)
)(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

document.querySelectorAll('th.sortable').forEach(th => {
    th.addEventListener('click', () => {
        const table = th.closest('table');
        const tbody = table.querySelector('tbody');
        Array.from(tbody.querySelectorAll('tr'))
            .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
            .forEach(tr => tbody.appendChild(tr));
    });
});

// ========== MODAL ACHATS UTILISATEUR ==========
const modal = document.getElementById('modal');
const modalBody = document.getElementById('modalBody');
const modalClose = document.getElementById('modalClose');

function fetchPurchases(userId) {
    modalBody.innerHTML = '<p>Chargement...</p>';
    fetch(`fetch_purchases.php?user_id=${userId}`)
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                modalBody.innerHTML = '<p>Aucun achat pour cet utilisateur.</p>';
                return;
            }
            let html = `<table>
                <thead><tr><th>Produit</th><th>Quantité</th><th>Prix Unitaire</th><th>Total</th></tr></thead><tbody>`;
            let totalSum = 0;
            data.forEach(item => {
                const total = item.quantite * item.prix_unitaire;
                totalSum += total;
                html += `<tr>
                    <td>${item.produit_nom}</td>
                    <td>${item.quantite}</td>
                    <td>${item.prix_unitaire.toFixed(2)} €</td>
                    <td>${total.toFixed(2)} €</td>
                </tr>`;
            });
            html += `</tbody></table>
                <h3>Total payé : ${totalSum.toFixed(2)} €</h3>`;
            modalBody.innerHTML = html;
        })
        .catch(() => {
            modalBody.innerHTML = '<p>Erreur lors du chargement des achats.</p>';
        });
}

// Ouvrir modal au clic sur nom utilisateur
document.querySelectorAll('.user-name').forEach(el => {
    el.addEventListener('click', () => {
        const userId = el.closest('tr').dataset.userid;
        fetchPurchases(userId);
        modal.hidden = false;
        modal.focus();
    });
});

// Fermer modal
modalClose.addEventListener('click', () => {
    modal.hidden = true;
});

modal.addEventListener('click', e => {
    if (e.target === modal) modal.hidden = true;
});

// Gestion clavier modal (Escape)
document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && !modal.hidden) {
        modal.hidden = true;
    }
});
