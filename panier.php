<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Démarrer la session
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
    } elseif ($action == 'compter') {
        echo json_encode(array('count' => count($_SESSION['panier'])));
        exit;
    } elseif ($action == 'vider') {
        $_SESSION['panier'] = array();
        echo json_encode(array('count' => 0, 'panier' => $_SESSION['panier']));
        exit;
    }

    // Encodez la réponse en JSON
    echo json_encode(array('count' => count($_SESSION['panier']), 'panier' => $_SESSION['panier']));
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Inclusion des feuilles de style Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Inclusion de votre fichier CSS -->
    <link rel="stylesheet" href="styles.css">
    <!-- Inclusion des scripts jQuery et Bootstrap pour la fonctionnalité interactive -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="scripts.js"></script>
    <!-- Métadonnées de la page -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Titre de la page -->
    <title>Panier</title>
</head>
<body>
    <header class="bg-primary text-white p-3 d-flex justify-content-between align-items-center">
        <!-- Navigation principale -->
        <nav class="nav">
            <ul class="nav">
                <li class="nav-item"><a class="nav-link text-white" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="ordinateurs.php">Ordinateurs</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="composants.php">Composants</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="peripheriques.php">Périphériques</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="gaming.php">Gaming</a></li>
            </ul>
        </nav>
        <!-- Zone de recherche et actions utilisateur -->
        <div class="d-flex align-items-center">
            <!-- Formulaire de recherche -->
            <form id="searchForm" action="recherche.php" method="GET" class="form-inline">
                <input type="text" class="form-control mr-2" name="query" placeholder="Rechercher...">
            </form>
            <!-- Actions pour l'utilisateur connecté ou non -->
            <div class="user-actions d-flex align-items-center">
                <?php
                if (isset($_SESSION['prenom']) && isset($_SESSION['nom'])) {
                    echo '<a href="deconnexion.php" class="btn btn-danger mr-2">Déconnexion</a>';
                } else {
                    echo '<a href="Connexion.html" class="btn btn-primary mr-2">Connexion</a>';
                    echo '<a href="Inscription.html" class="btn btn-secondary mr-2">Inscription</a>';
                }
                ?>
                <!-- Lien vers le panier d'achat -->
                <a href="panier.php" class="btn btn-warning">Panier <span id="cart-count"><?= isset($_SESSION['panier']) ? count($_SESSION['panier']) : 0 ?></span></a>
            </div>
        </div>
    </header>
    <!-- Contenu principal de la page -->
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
                        echo "$produit - {$details['prix']} € x{$details['quantite']} = $prix_total_produit €";
                        echo "<div class='d-flex'>";
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
            $('.remove-from-cart-btn').click(function() {
                var product = $(this).data('product');
                var quantity = $(this).closest('div').find('select[name="quantite"]').val();
                removeFromCart(product, quantity);
            });
            $('#vider-panier-btn').click(function() {
                $.get('panier.php', {action: 'vider'}, function(response) {
                    var data = JSON.parse(response);
                    $('#cart-count').text(data.count);
                    updateCartItems(data.panier);
                });
            });
        });

        function removeFromCart(product, quantity) {
            $.get('panier.php', {action: 'supprimer', produit: product, quantite: quantity}, function(response) {
                var data = JSON.parse(response);
                $('#cart-count').text(data.count);
                updateCartItems(data.panier);
            });
        }

        function updateCartItems(panier) {
            var cartItems = $('#cart-items');
            cartItems.empty();
            var total = 0;
            if (Object.keys(panier).length === 0) {
                cartItems.append("<p>Votre panier est vide.</p>");
                $('#acheter-btn').attr('disabled', true);
            } else {
                $.each(panier, function(produit, details) {
                    if (typeof details === 'object') {
                        var prix_total_produit = details.prix * details.quantite;
                        total += prix_total_produit;
                        var itemHtml = "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                        itemHtml += produit + " - " + details.prix + " € x" + details.quantite + " = " + prix_total_produit + " €";
                        itemHtml += "<div class='d-flex'>";
                        itemHtml += "<button class='btn btn-danger btn-sm remove-from-cart-btn' data-product='" + produit + "'>Supprimer</button>";
                        itemHtml += "</div>";
                        itemHtml += "</li>";
                        cartItems.append(itemHtml);
                    }
                });
                $('#cart-total').text(total + " €");
            }
        }

        function verifierConnexion() {
            <?php if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom'])): ?>
                if (confirm("Veuillez vous connecter pour acheter. Voulez-vous être redirigé vers la page de connexion ?")) {
                    window.location.href = 'Connexion.html';
                }
            <?php else: ?>
                // Rediriger vers la page de confirmation d'achat
                window.location.href = 'achat.php';
            <?php endif; ?>
        }

        function updateCartCount() {
            $.get('panier.php', {action: 'compter'}, function(response) {
                var data = JSON.parse(response);
                $('#cart-count').text(data.count);
            });
        }

        function addToCart(product, price, quantity) {
            $.get('panier.php', {action: 'ajouter', produit: product, prix: price, quantite: quantity}, function(response) {
                var data = JSON.parse(response);
                $('#cart-count').text(data.count); // Met à jour le compteur
            });
        }

        document.getElementById('buyButton').addEventListener('click', function() {
            // Vérifiez si l'utilisateur est connecté
            <?php if (isset($_SESSION['nom']) && isset($_SESSION['prenom'])): ?>
                // Redirigez vers Stripe si l'utilisateur est connecté
                window.location.href = 'https://buy.stripe.com/test_00g29f0bGe8ffYs3cc';
            <?php else: ?>
                // Affichez un message d'alerte si l'utilisateur n'est pas connecté
                alert('Veuillez vous connecter pour acheter.');
                window.location.href = 'Connexion.html';
            <?php endif; ?>
        });
    </script>
</body>
</html>
