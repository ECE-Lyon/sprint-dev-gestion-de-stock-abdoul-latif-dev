$(document).ready(function() {
    // Initialisation du modal pour modifier la quantité
    $('#modifierQuantiteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nom = button.data('nom');
        var quantite = button.data('quantite');
        
        var modal = $(this);
        modal.find('#produit_id').val(id);
        modal.find('#produit_nom').text(nom);
        modal.find('#nouvelle_quantite').val(quantite);
    });
});