$(document).ready(function() {
    // Afficher les éléments animés après chargement
    setTimeout(function() {
        $('.animated-element').addClass('show');
    }, 100);
    
    // Animation des boutons avec spinner
    $('.btn-animated').each(function() {
        // Stocker le texte original dans un attribut data
        const $btn = $(this);
        const $btnText = $btn.find('span');
        $btn.data('original-text', $btnText.text());
    });
    
    $('.btn-animated').click(function() {
        const $btn = $(this);
        const $spinner = $btn.find('.loading-spinner');
        
        // Correction de la condition pour inclure les boutons de type submit
        if ($spinner.length) {
            const $btnText = $btn.find('span');
            
            // Utiliser le texte stocké dans l'attribut data
            const originalText = $btn.data('original-text');
            
            // Afficher le spinner
            $btnText.text('Chargement...');
            $spinner.show();
            
            // Restaurer le texte original après 3 secondes si aucune redirection n'a lieu
            setTimeout(function() {
                if ($spinner.is(':visible')) {
                    $btnText.text(originalText);
                    $spinner.hide();
                }
            }, 3000);
        }
    });
    
    // Animation des cartes au survol
    $('.animated-card').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );
    
    // Animation des alertes
    $('.alert').addClass('animate__animated animate__fadeIn');
    
    // Fermeture automatique des alertes après 5 secondes
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});