document.addEventListener('DOMContentLoaded', function() {
    // Sélectionne le formulaire de recherche
    const searchForm = document.querySelector('form');
    const searchInput = document.querySelector('input[name="query"]');
    const resultsContainer = document.querySelector('#results');

    // Ajoute un écouteur d'événement pour empêcher la soumission standard du formulaire
    searchForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Empêche la soumission du formulaire
    });

    // Ajoute un écouteur d'événement pour gérer les entrées dans le champ de recherche
    searchInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Empêche la soumission standard du formulaire
            const query = searchInput.value; // Récupère la valeur actuelle de l'input

            // Vérifie si la requête n'est pas vide
            if (query.length > 0) {
                fetch(`recherche.php?query=${query}&ajax=1`)
                    .then(response => response.text()) // Convertit la réponse en texte
                    .then(data => {
                        resultsContainer.innerHTML = data; // Affiche les résultats dans le conteneur
                        history.pushState(null, '', `recherche.php?query=${query}`);
                    })
                    .catch(error => console.error('Error:', error)); // Gère les erreurs éventuelles
            } else {
                fetch('produits.php')
                    .then(response => response.text()) // Convertit la réponse en texte
                    .then(data => {
                        resultsContainer.innerHTML = data; // Affiche les produits de l'index dans le conteneur
                        searchInput.value = '';
                        history.pushState(null, '', 'recherche.php');
                    })
                    .catch(error => console.error('Error:', error)); // Gère les erreurs éventuelles
            }
        }
    });
});
