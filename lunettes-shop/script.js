document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('search-input');
  const suggestionsBox = document.getElementById('suggestions-box');
  const produitsList = document.getElementById('produits-list');
  const micBtn = document.getElementById('mic-btn');

  // Fonction pour échapper les caractères spéciaux HTML
  function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  // Afficher les produits
  function afficherProduits(produits) {
    if (!produits || produits.length === 0) {
      produitsList.innerHTML = '<p class="no-results">Aucun produit trouvé.</p>';
      return;
    }

    produitsList.innerHTML = produits.map(p => `
      <div class="produit-card">
        <img src="assets/images/${escapeHtml(p.image || 'default.jpg')}" alt="${escapeHtml(p.nom)}">
        <h3>${escapeHtml(p.nom)}</h3>
        <p class="marque"><strong>Marque:</strong> ${escapeHtml(p.marque)}</p>
        <p class="description">${escapeHtml(p.description)}</p>
        <p class="prix"><strong>Prix:</strong> ${escapeHtml(p.prix.toString())} FCFA</p>
        <p class="produit-categorie">Catégorie : ${escapeHtml(p.categorie)}</p>
        <button class="add-to-cart-btn" data-id="${escapeHtml(p.id.toString())}">Ajouter au panier</button>
      </div>
    `).join('');
  }

  // Charger tous les produits
  function chargerTousProduits() {
    produitsList.innerHTML = '<div class="loading-spinner">Chargement...</div>';
    
    fetch('includes/get_produits.php')
      .then(res => {
        if (!res.ok) throw new Error('Erreur réseau');
        return res.json();
      })
      .then(data => afficherProduits(data))
      .catch(() => {
        produitsList.innerHTML = '<p class="error-msg">Erreur lors du chargement des produits.</p>';
      });
  }

  // Synthèse vocale
  function speak(text) {
    if (!window.speechSynthesis) {
      console.warn('Synthèse vocale non supportée');
      return;
    }
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'fr-FR';
    window.speechSynthesis.speak(utterance);
  }

  // Traitement des commandes vocales
  function traiterCommandeVocale(command) {
    if (!command || command.trim() === "") {
      speak("Je n'ai pas compris votre demande. Pouvez-vous répéter ?");
      return;
    }

    command = command.toLowerCase().trim();
    let searchQuery = command;

    // Commandes spéciales
    if (command.includes("lunettes de soleil") || command.includes("solaires")) {
      searchQuery = "lunettes de soleil";
    } else if (command.includes("lunettes de vue") || command.includes("vue")) {
      searchQuery = "lunettes de vue";
    } else if (command.includes("aide") || command.includes("assistant")) {
      speak("Je peux rechercher des lunettes de soleil, des lunettes de vue, ou effectuer une recherche générale.");
      produitsList.innerHTML = `
        <div class="assistant-help">
          <h3>🤖 Assistance vocale</h3>
          <p>Exemples de commandes :</p>
          <ul>
            <li>"Montre-moi les lunettes de soleil"</li>
            <li>"Recherche des lunettes de vue"</li>
            <li>"Trouve des lunettes Ray-Ban"</li>
          </ul>
        </div>
      `;
      return;
    }

    // Recherche dans la base de données
    fetch(`includes/search_vocal.php?q=${encodeURIComponent(searchQuery)}`)
      .then(res => {
        if (!res.ok) throw new Error('Erreur serveur');
        return res.json();
      })
      .then(data => {
        if (data.error) throw new Error(data.error);
        
        if (data.length === 0) {
          speak(`Aucun résultat pour "${command}". Essayez avec d'autres termes.`);
          produitsList.innerHTML = `<p class="no-results">Aucun résultat pour "${escapeHtml(command)}"</p>`;
          return;
        }

        const resultsCount = data.length;
        speak(`J'ai trouvé ${resultsCount} produit${resultsCount > 1 ? 's' : ''}. Voici les résultats.`);
        afficherProduits(data);
      })
      .catch(error => {
        console.error('Erreur:', error);
        speak("Désolé, une erreur est survenue. Veuillez réessayer.");
        produitsList.innerHTML = `<p class="error-msg">Erreur: ${escapeHtml(error.message)}</p>`;
      });
  }

  // Recherche textuelle
  function lancerRecherche(query) {
    if (query.length < 2) {
      suggestionsBox.style.display = 'none';
      chargerTousProduits();
      return;
    }

    fetch(`includes/search_suggestions.php?q=${encodeURIComponent(query)}`)
      .then(res => {
        if (!res.ok) throw new Error('Erreur réseau');
        return res.json();
      })
      .then(data => {
        if (!data || data.length === 0) {
          suggestionsBox.style.display = 'none';
        } else {
          suggestionsBox.innerHTML = data.map(item => `
            <div class="suggestion-item">${escapeHtml(item.nom)}</div>
          `).join('');
          suggestionsBox.style.display = 'block';

          // Gestion du clic sur les suggestions
          document.querySelectorAll('.suggestion-item').forEach(el => {
            el.addEventListener('click', () => {
              searchInput.value = el.textContent;
              suggestionsBox.style.display = 'none';
              fetch(`includes/search_suggestions.php?q=${encodeURIComponent(el.textContent)}`)
                .then(res => res.json())
                .then(data => afficherProduits(data));
            });
          });
        }
        afficherProduits(data);
      })
      .catch(() => {
        suggestionsBox.style.display = 'none';
      });
  }

  // Événements
  searchInput.addEventListener('input', () => {
    const query = searchInput.value.trim();
    lancerRecherche(query);
  });

  document.addEventListener('click', (e) => {
    if (!document.getElementById('search-container').contains(e.target)) {
      suggestionsBox.style.display = 'none';
    }
  });

  // Reconnaissance vocale
  if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    const recognition = new SpeechRecognition();
    recognition.lang = 'fr-FR';
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;

    micBtn.addEventListener('click', () => {
      recognition.start();
      micBtn.disabled = true;
      micBtn.innerHTML = '<span class="pulse-animation">🎤</span> Écoute...';
    });

    recognition.onresult = (event) => {
      const command = event.results[0][0].transcript;
      searchInput.value = command;
      micBtn.disabled = false;
      micBtn.innerHTML = '🎙️';
      traiterCommandeVocale(command);
    };

    recognition.onerror = (event) => {
      micBtn.disabled = false;
      micBtn.innerHTML = '🎙️';
      switch(event.error) {
        case 'no-speech':
          speak("Je n'ai rien entendu. Voulez-vous réessayer ?");
          break;
        case 'audio-capture':
          speak("Aucun microphone détecté. Vérifiez vos permissions.");
          break;
        default:
          speak("Erreur de reconnaissance vocale. Veuillez réessayer.");
      }
    };

    recognition.onend = () => {
      if (micBtn.disabled) {
        micBtn.disabled = false;
        micBtn.innerHTML = '🎙️';
      }
    };
  } else {
    micBtn.style.display = 'none';
    console.warn("Reconnaissance vocale non supportée");
  }

  // Initialisation
  chargerTousProduits();
});