<?php
session_start();
require 'config.php'; // Include the configuration file for database credentials
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Update with your CSS path -->
</head>
<body>
    <header class="bg-primary text-white p-3 d-flex justify-content-between align-items-center">
        <nav class="nav">
            <ul class="nav">
                <li class="nav-item"><a class="nav-link text-white" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="ordinateurs.php">Ordinateurs</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="composants.php">Composants</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="peripheriques.php">Périphériques</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="gaming.php">Gaming</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="favorites.php">Mes Favoris</a></li>
            </ul>
        </nav>
    </header>

    <div class="container mt-4">
        <h1>Mes Favoris</h1>
        <div class="row">
            <?php
            if (isset($_SESSION['favorites']) && !empty($_SESSION['favorites'])) {
                // Fetch favorite products from the database
                $favorites = $_SESSION['favorites'];
                $placeholders = implode(',', array_fill(0, count($favorites), '?'));
                try {
                    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $stmt = $pdo->prepare("SELECT * FROM modele WHERE id_modele IN ($placeholders)");
                    $stmt->execute($favorites);
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($products as $product) {
                        echo '<div class="col-md-4">';
                        echo '<div class="card mb-4">';
                        echo '<img src="images/' . $product['Image'] . '" class="card-img-top" alt="' . $product['Nom'] . '">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . $product['Nom'] . '</h5>';
                        echo '<p class="card-text">' . $product['Description'] . '</p>';
                        echo '<p class="card-text"><strong>' . $product['Prix'] . ' €</strong></p>';
                        echo '<button class="favorite-btn btn btn-outline-danger" data-product-id="' . $product['id_modele'] . '">';
                        echo '<i class="fa fa-heart favorited"></i>';
                        echo '</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } catch (PDOException $e) {
                    echo 'Erreur de connexion : ' . $e->getMessage();
                }
            } else {
                echo '<p>Vous n\'avez aucun produit dans vos favoris.</p>';
            }
            ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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