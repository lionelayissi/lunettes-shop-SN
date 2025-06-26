document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("filtres");
    const resultats = document.getElementById("resultats-commandes");

    function fetchCommandes() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();

        fetch("fetch_commandes_admin.php?" + params)
            .then(res => res.text())
            .then(html => {
                resultats.innerHTML = html;
            });
    }

    // Appel initial
    fetchCommandes();

    // Mise à jour automatique
    form.querySelectorAll("input").forEach(input => {
        input.addEventListener("input", fetchCommandes);
    });
});
