function addToCart(product, quantity) {
    $.get('panier.php', {action: 'ajouter', produit: product, quantite: quantity, ajax: 1}, function() {
        // Mettre à jour le compteur dans le panier
        var currentCount = parseInt($('#cart-count').text());
        $('#cart-count').text(currentCount + quantity);
    });
}

$('.remove-from-cart-btn').click(function() {
    var produit = $(this).data('product');
    $.get('panier.php', {action: 'supprimer', produit: produit}, function(response) {
        var data = JSON.parse(response);
        $('#cart-count').text(data.count);
        updateCartItems(data.panier);
    });
});
