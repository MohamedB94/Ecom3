<?php
session_start();

// Initialiser le panier s'il n'existe pas
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = array();
}

// Gérer les actions du panier
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    $produit = $_GET['produit'];
    $quantite = isset($_GET['quantite']) ? $_GET['quantite'] : 1;

    if ($action == 'ajouter') {
        $prix = $_GET['prix'];
        if (isset($_SESSION['panier'][$produit])) {
            $_SESSION['panier'][$produit]['quantite'] += $quantite;
        } else {
            $_SESSION['panier'][$produit] = array('prix' => $prix, 'quantite' => $quantite);
        }
    } elseif ($action == 'supprimer') {
        if (isset($_SESSION['panier'][$produit])) {
            $_SESSION['panier'][$produit]['quantite'] -= $quantite;
            if ($_SESSION['panier'][$produit]['quantite'] <= 0) {
                unset($_SESSION['panier'][$produit]);
            }
        }
    } elseif ($action == 'vider') {
        $_SESSION['panier'] = array();
    } elseif ($action == 'supprimer_tout') {
        if (isset($_SESSION['panier'][$produit])) {
            unset($_SESSION['panier'][$produit]);
        }
    }

    // Retourner le nombre d'articles dans le panier
    echo json_encode(array('count' => array_sum(array_column($_SESSION['panier'], 'quantite')), 'panier' => $_SESSION['panier']));
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="panier.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="scripts.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h1 class="text-center">Votre Panier</h1>
        <ul class="list-group" id="cart-items">
            <?php
            $total = 0;
            // Afficher les produits dans le panier ou un message si le panier est vide
            if (empty($_SESSION['panier'])) {
                echo "<p>Votre panier est vide.</p>"; // Message si le panier est vide
            } else {
                // Boucler sur chaque produit du panier pour l'affichage
                foreach ($_SESSION['panier'] as $produit => $details) {
                    if (is_array($details)) {
                        $prix_total_produit = $details['prix'] * $details['quantite'];
                        $total += $prix_total_produit;
                        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                        
                        // Vérifiez si l'image existe avant de l'afficher
                        if (isset($details['image']) && !empty($details['image'])) {
                            echo "<img src='images/{$details['image']}' alt='{$produit}' style='width: 50px; height: 50px; margin-right: 10px;'>";
                        }
                        
                        echo "$produit - {$details['prix']} € x{$details['quantite']} = $prix_total_produit €";
                        echo "<div class='d-flex'>";
                        echo "<button class='btn btn-secondary btn-sm decrease-quantity-btn' data-product='$produit'>-</button>";
                        echo "<button class='btn btn-secondary btn-sm increase-quantity-btn' data-product='$produit'>+</button>";
                        echo "<button class='btn btn-danger btn-sm remove-from-cart-btn' data-product='$produit'>Supprimer</button>";
                        echo "</div>";
                        echo "</li>";
                    }
                }
                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                echo "<strong>Total</strong>";
                echo "<strong id='cart-total'>$total €</strong>";
                echo "</li>";
            }
            ?>
        </ul>
        <div class="d-flex justify-content-between mt-4">
            <button id="vider-panier-btn" class="btn btn-danger">Vider le panier</button>
            <button id="buyButton" class="btn btn-primary">Acheter</button>
            <a href="index.php" class="btn btn-secondary">Continuer vos achats</a>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // Charger les éléments du panier au démarrage
            $.get('panier.php', {action: 'charger'}, function(response) {
                var data = JSON.parse(response);
                updateCartItems(data.panier);
            });

            attachEventHandlers();
        });

        function attachEventHandlers() {
            $('.increase-quantity-btn').off('click').on('click', function() {
                var product = $(this).data('product');
                $.get('panier.php', {action: 'ajouter', produit: product, quantite: 1}, function(response) {
                    var data = JSON.parse(response);
                    updateCartItems(data.panier);
                });
            });

            $('.decrease-quantity-btn').off('click').on('click', function() {
                var product = $(this).data('product');
                $.get('panier.php', {action: 'supprimer', produit: product, quantite: 1}, function(response) {
                    var data = JSON.parse(response);
                    updateCartItems(data.panier);
                });
            });

            $('.remove-from-cart-btn').off('click').on('click', function() {
                var product = $(this).data('product');
                $.get('panier.php', {action: 'supprimer_tout', produit: product}, function(response) {
                    var data = JSON.parse(response);
                    updateCartItems(data.panier);
                });
            });

            $('#vider-panier-btn').off('click').on('click', function() {
                $.get('panier.php', {action: 'vider'}, function(response) {
                    var data = JSON.parse(response);
                    updateCartItems(data.panier);
                });
            });
        }

        function updateCartItems(panier) {
            var cartItems = $('#cart-items');
            cartItems.empty();
            var total = 0;
            if (Object.keys(panier).length === 0) {
                cartItems.append("<p>Votre panier est vide.</p>");
            } else {
                $.each(panier, function(produit, details) {
                    if (typeof details === 'object') {
                        var prix_total_produit = details.prix * details.quantite;
                        total += prix_total_produit;
                        var itemHtml = "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                        if (details.image) {
                            itemHtml += "<img src='images/" + details.image + "' alt='" + produit + "' style='width: 50px; height: 50px; margin-right: 10px;'>";
                        }
                        itemHtml += produit + " - " + details.prix + " € x" + details.quantite + " = " + prix_total_produit.toFixed(2) + " €";
                        itemHtml += "<div class='d-flex'>";
                        itemHtml += "<button class='btn btn-secondary btn-sm decrease-quantity-btn' data-product='" + produit + "'>-</button>";
                        itemHtml += "<button class='btn btn-secondary btn-sm increase-quantity-btn' data-product='" + produit + "'>+</button>";
                        itemHtml += "<button class='btn btn-danger btn-sm remove-from-cart-btn' data-product='" + produit + "'>Supprimer</button>";
                        itemHtml += "</div>";
                        itemHtml += "</li>";
                        cartItems.append(itemHtml);
                    }
                });
                $('#cart-total').text(total.toFixed(2) + " €");
            }

            attachEventHandlers(); // Réattacher les événements
        }
    </script>
</body>
</html>
