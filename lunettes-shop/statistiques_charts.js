document.addEventListener('DOMContentLoaded', () => {
    fetch('get_stats_data.php')
        .then(res => res.json())
        .then(data => {
            // 1. Chiffre d’affaires
            new Chart(document.getElementById('caChart'), {
                type: 'line',
                data: {
                    labels: data.ca_par_mois.labels,
                    datasets: [{
                        label: 'Chiffre d’affaires (€)',
                        data: data.ca_par_mois.values,
                        backgroundColor: 'rgba(15, 52, 96, 0.2)',
                        borderColor: '#0f3460',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                }
            });

            // 2. Répartition statuts
            new Chart(document.getElementById('statutChart'), {
                type: 'pie',
                data: {
                    labels: data.commandes_par_statut.labels,
                    datasets: [{
                        label: 'Commandes',
                        data: data.commandes_par_statut.values,
                        backgroundColor: ['#0f3460', '#e94560', '#00b894']
                    }]
                }
            });

            // 3. Produits les plus vendus
            new Chart(document.getElementById('produitsChart'), {
                type: 'bar',
                data: {
                    labels: data.top_produits.labels,
                    datasets: [{
                        label: 'Quantité vendue',
                        data: data.top_produits.values,
                        backgroundColor: '#1a1a2e'
                    }]
                }
            });

            // 4. Top fournisseurs
            new Chart(document.getElementById('fournisseursChart'), {
                type: 'bar',
                data: {
                    labels: data.top_fournisseurs.labels,
                    datasets: [{
                        label: 'Chiffre d’affaires (€)',
                        data: data.top_fournisseurs.values,
                        backgroundColor: '#0f3460'
                    }]
                },
                options: {
                    indexAxis: 'y'
                }
            });
        });
});
