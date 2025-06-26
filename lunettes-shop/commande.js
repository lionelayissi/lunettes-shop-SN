async function genererPDF(id) {
    const { jsPDF } = window.jspdf;
    const element = document.getElementById('facture-' + id);
    const pdf = new jsPDF();

    await pdf.html(element, {
        callback: function (doc) {
            doc.save(`facture_${id}.pdf`);
        },
        margin: [10, 10, 10, 10],
        autoPaging: 'text',
        x: 10,
        y: 10,
        width: 180
    });
}

function confirmerCommande(idCommande) {
    if (confirm("Confirmer cette commande ?")) {
        fetch('confirmer_commande.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id=' + encodeURIComponent(idCommande)
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            location.reload(); // recharge la page pour mettre à jour le statut
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert("Une erreur est survenue.");
        });
    }
}


function imprimerFacture(id) {
    const contenu = document.getElementById(id).innerHTML;
    const fenetre = window.open('', '', 'height=800,width=800');
    fenetre.document.write('<html><head><title>Facture</title>');
    fenetre.document.write('<link rel="stylesheet" href="assets/css/commandes.css">');
    fenetre.document.write('</head><body>');
    fenetre.document.write(contenu);
    fenetre.document.write('</body></html>');
    fenetre.document.close();
    fenetre.focus();
    fenetre.print();
    fenetre.close();
}
