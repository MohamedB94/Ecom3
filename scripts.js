function addToCart(product, quantity) {
    $.get('panier.php', {action: 'ajouter', produit: product, quantite: quantity, ajax: 1}, function() {
        // Mettre à jour le compteur dans le panier
        var currentCount = parseInt($('#cart-count').text());
        $('#cart-count').text(currentCount + quantity);
    });
}
