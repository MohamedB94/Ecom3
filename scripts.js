function addToCart(product, price, quantity) {
    $.get('panier.php', {action: 'ajouter', produit: product, prix: price, quantite: quantity}, function(response) {
        var data = JSON.parse(response);
        updateCartCount(data.count); // Mettre à jour le compteur du panier
    });
}

function updateCartCount(count) {
    $('#cart-count').text(count);
}

$('.add-to-cart-btn').click(function() {
    var form = $(this).closest('.add-to-cart-form');
    var product = form.data('product');
    var price = form.data('price');
    var quantity = form.find('select[name="quantite"]').val();
    addToCart(product, price, parseInt(quantity));
});

$('.remove-from-cart-btn').click(function() {
    var produit = $(this).data('product');
    $.get('panier.php', {action: 'supprimer', produit: produit}, function(response) {
        var data = JSON.parse(response);
        updateCartCount(data.count);
        updateCartItems(data.panier);
    });
});
