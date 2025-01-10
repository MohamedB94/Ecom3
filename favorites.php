<?php
session_start();
require 'config.php'; // Include the configuration file for database credentials

function getFavoriteProducts($favorites) {
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $placeholders = implode(',', array_fill(0, count($favorites), '?'));
        $stmt = $pdo->prepare("SELECT * FROM modele WHERE id_modele IN ($placeholders)");
        $stmt->execute($favorites);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return [];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris</title>
    <link href="node_modules/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Update with your CSS path -->
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-4">
        <h1>Mes Favoris</h1>
        <div class="row">
            <?php
            if (isset($_SESSION['favorites']) && !empty($_SESSION['favorites'])) {
                $products = getFavoriteProducts($_SESSION['favorites']);
                if (!empty($products)) {
                    foreach ($products as $product) {
                        echo '<div class="col-md-4">';
                        echo '<div class="card mb-4">';
                        echo '<img src="images/' . htmlspecialchars($product['Image']) . '" class="card-img-top" alt="' . htmlspecialchars($product['Nom']) . '">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . htmlspecialchars($product['Nom']) . '</h5>';
                        echo '<p class="card-text">' . htmlspecialchars($product['Description']) . '</p>';
                        echo '<p class="card-text"><strong>' . htmlspecialchars($product['Prix']) . ' €</strong></p>';
                        echo '<button class="favorite-btn btn btn-outline-danger" data-product-id="' . htmlspecialchars($product['id_modele']) . '">';
                        echo '<i class="fa fa-heart favorited"></i>';
                        echo '</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Erreur lors de la récupération des produits favoris.</p>';
                }
            } else {
                echo '<p>Vous n\'avez aucun produit dans vos favoris.</p>';
            }
            ?>
        </div>
    </div>

    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.favorite-btn').click(function() {
                var button = $(this);
                var productId = button.data('product-id');

                // Send an AJAX request to update favorites
                $.post('update_favorites.php', { product_id: productId }, function(response) {
                    if (response.success) {
                        // Toggle the heart color
                        button.find('i').toggleClass('favorited');
                        // Optionally, remove the product from the list if unfavorited
                        if (!response.favorited) {
                            button.closest('.col-md-4').remove();
                        }
                    } else {
                        alert('Erreur lors de la mise à jour des favoris.');
                    }
                }, 'json');
            });
        });
    </script>
</body>
</html>