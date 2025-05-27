$(document).ready(function() {
    // Initialisation des modals pour les utilisateurs
    $('#modifierUtilisateur').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var userId = button.data('user-id');
        var nom = button.data('user-nom');
        var prenom = button.data('user-prenom');
        var email = button.data('user-email');
        var niveau = button.data('user-niveau');
        
        var modal = $(this);
        modal.find('#edit_user_id').val(userId);
        modal.find('#edit_nom').val(nom);
        modal.find('#edit_prenom').val(prenom);
        modal.find('#edit_email').val(email);
        modal.find('#edit_niveau').val(niveau);
    });
    
    $('#supprimerUtilisateur').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var userId = button.data('user-id');
        var userName = button.data('user-nom');
        
        var modal = $(this);
        modal.find('#delete_user_id').val(userId);
        modal.find('.user-name').text(userName);
    });
    
    // Initialisation des modals pour les catégories
    $('#modifierCategorie').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nom = button.data('nom');
        var description = button.data('description');
        
        var modal = $(this);
        modal.find('#edit_categorie_id').val(id);
        modal.find('#edit_nom').val(nom);
        modal.find('#edit_description').val(description);
    });
    
    $('#supprimerCategorie').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nom = button.data('nom');
        
        var modal = $(this);
        modal.find('#delete_categorie_id').val(id);
        modal.find('.categorie-name').text(nom);
    });
});