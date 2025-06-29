# lunettes-shop-SN
Application de vente de lunettes et de gestion de categories pour la session normale
Lunettes-Shop - Stratégie de gestion des branches Git
1. Présentation
Ce dépôt contient le code source de mon site de vente de lunettes. Pour organiser le développement et assurer la qualité du code, j’utilise une stratégie de branches basée sur Gitflow.

2. Structure des branches
Branche	Description
main	Branche stable contenant la version finale, prête pour la production.
dev	Branche de développement intégrant toutes les fonctionnalités terminées mais non encore stables.
test/frontend	Branche dédiée aux tests visuels et fonctionnels de l’interface utilisateur (CSS, JS, HTML).
feature/*	Branches temporaires créées pour développer chaque nouvelle fonctionnalité spécifique.
3. Workflow et étapes suivies
Initialisation

Création du dépôt local avec tous les fichiers du site.

Premier commit sur la branche main.

Création des branches principales

Création des branches dev et test/frontend à partir de main.

Développement des fonctionnalités

Pour chaque nouvelle fonctionnalité (ex : gestion du panier, authentification), création d’une branche feature/nom-fonctionnalite à partir de dev.

Travail et commits réguliers sur la branche feature.

Intégration et tests

Fusion des branches feature/* dans dev une fois la fonctionnalité terminée.

Fusion de dev dans test/frontend pour valider l’interface et les comportements côté client.

Après validation, fusion de test/frontend dans dev.

Mise en production

Quand dev est stable et testé, fusion dans main.

La branche main contient la version finale prête à être déployée.

4. Commandes Git principales utilisées
Initialisation et premières branches
bash
git init
git add .
git commit -m "Premier commit : ajout de tous les fichiers"
git branch dev
git branch test/frontend
Création d’une branche feature
bash
git checkout dev
git checkout -b feature/nom-fonctionnalite
# Travailler sur la fonctionnalité
git add .
git commit -m "Ajout de la fonctionnalité nom-fonctionnalite"
Fusion d’une feature dans dev
bash
git checkout dev
git merge feature/nom-fonctionnalite
git branch -d feature/nom-fonctionnalite
Tester sur test/frontend
bash
git checkout test/frontend
git merge dev
# Tester localement, puis si OK :
git checkout dev
git merge test/frontend
Fusion finale dans main
bash
git checkout main
git merge dev
git push origin main
Pousser toutes les branches sur GitHub
bash
git remote add origin https://github.com/lionelayissi/lunettes-shop.git
git push -u origin main
git push origin dev
git push origin test/frontend
git push origin feature/nom-fonctionnalite
5. Remarques
Les branches feature/* permettent de travailler sur des fonctionnalités isolées sans impacter la branche principale.

La branche test/frontend est dédiée à la validation visuelle et fonctionnelle avant intégration finale.

La branche main est toujours propre et stable, prête pour la mise en production.

Cette organisation facilite la collaboration, la gestion des versions et la maintenance du site.
