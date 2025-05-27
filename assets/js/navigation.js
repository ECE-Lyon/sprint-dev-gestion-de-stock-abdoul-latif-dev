document.addEventListener('DOMContentLoaded', function() {
    // Gérer les clics sur les liens de navigation
    document.addEventListener('click', function(e) {
        // Vérifier si c'est un lien interne qui n'a pas la classe 'no-ajax'
        const link = e.target.closest('a');
        if (link && link.href && 
            link.href.startsWith(window.location.origin) && 
            !link.classList.contains('no-ajax') &&
            link.target !== '_blank' &&
            !link.hasAttribute('download') &&
            link.getAttribute('href') !== '#' &&
            link.getAttribute('href') !== '') {
            
            e.preventDefault();
            
            // Afficher un indicateur de chargement
            const mainContent = document.querySelector('main');
            if (mainContent) {
                mainContent.innerHTML = `
                    <div class="d-flex justify-content-center align-items-center" style="height: 300px;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>`;
            }
            
            // Charger le contenu de la page
            fetch(link.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.text();
            })
            .then(html => {
                // Parser la réponse HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Mettre à jour le titre de la page
                if (doc.title) {
                    document.title = doc.title;
                }
                
                // Mettre à jour le contenu principal
                const newContent = doc.querySelector('main') || doc.querySelector('.main-container');
                if (newContent && mainContent) {
                    mainContent.innerHTML = newContent.innerHTML;
                } else {
                    // Si on ne trouve pas de contenu principal, recharger la page
                    window.location.href = link.href;
                }
                
                // Faire défiler vers le haut de la page
                window.scrollTo(0, 0);
                
                // Mettre à jour l'URL sans recharger la page
                window.history.pushState({}, '', link.href);
            })
            .catch(error => {
                console.error('Erreur:', error);
                // En cas d'erreur, recharger la page normalement
                window.location.href = link.href;
            });
        }
    });
    
    // Gérer les boutons précédent/suivant du navigateur
    window.addEventListener('popstate', function() {
        window.location.reload();
    });
});
