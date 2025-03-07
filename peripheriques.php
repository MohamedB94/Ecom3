<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Démarrer la session
session_start();

// Inclure le fichier de configuration
require_once 'config.php';

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Inclure le header
include 'header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Périphériques</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .favorite-btn {
            background: none;
            border: none;
            cursor: pointer;
        }

        .favorite-btn .fa-heart {
            color: grey;
            font-size: 24px;
        }

        .favorite-btn .fa-heart.favorited {
            color: red;
        }

        .avis-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .avis-form textarea {
            width: 90%;
            min-height: 80px;
            margin-top: 5px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: none;
            font-size: 14px;
        }

        .star-rating {
            display: flex;
            justify-content: center;
            gap: 5px;
            cursor: pointer;
            margin: 10px 0;
        }

        .star {
            font-size: 24px;
            color: #ccc;
            transition: color 0.2s;
        }

        .star:hover,
        .star.selected {
            color: gold;
        }

        .product-card {
            width: auto;
            height: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Périphériques</h1>
        
        <div class="row">
            <?php
            // Requête pour récupérer tous les périphériques
            $sql = "SELECT * FROM modele WHERE Produits = 'Péripheriques'";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='col-md-4'>";
                    echo "<div class='card mb-4 shadow-sm product-card'>";
                    echo "<a href='produit_detail.php?id=" . $row['id_modele'] . "'>";
                    echo "<img src='images/" . $row['Image'] . "' class='card-img-top' alt='" . $row['Nom'] . "'>";
                    echo "</a>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>" . $row['Fabricant'] . " " . $row['Nom'] . "</h5>";
                    echo "<p class='card-text'>" . $row['Description'] . "</p>";
                    echo "<p class='card-text'>Prix: " . $row['Prix'] . " €</p>";
                    echo "<div class='d-flex flex-column'>";
                    
                    // Formulaire d'ajout au panier
                    echo "<form class='add-to-cart-form' data-product='" . $row['Nom'] . "' data-price='" . $row['Prix'] . "'>";
                    echo "<input type='hidden' name='action' value='ajouter'>";
                    echo "<input type='hidden' name='produit' value='" . $row['Nom'] . "'>";
                    echo "<input type='hidden' name='prix' value='" . $row['Prix'] . "'>";
                    echo "<input type='hidden' name='image_path' value='images/" . $row['Image'] . "'>";
                    echo "<div class='d-flex mb-2'>";
                    echo "<select name='quantite' class='form-control mr-2'>";
                    for ($i = 1; $i <= 5; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    echo "</select>";
                    echo "<button type='button' class='btn btn-primary add-to-cart-btn'>Ajouter au panier</button>";
                    echo "</div>";
                    echo "</form>";

                    // Bouton Acheter
                    if (isset($_SESSION['prenom']) && isset($_SESSION['nom'])) {
                        echo "<a href='redirect_to_stripe.php' class='btn btn-primary'>Acheter</a>";
                    } else {
                        echo "<button class='btn btn-primary acheter-btn' onclick='verifierConnexion()'>Acheter</button>";
                    }

                    // Boutons Admin
                    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
                        echo "<button class='btn btn-warning' onclick='editProduct(" . json_encode($row) . ")'>Modifier</button>";
                        echo "<button class='btn btn-danger' onclick='deleteProduct(" . $row['id_modele'] . ")'>Supprimer</button>";
                    }

                    // Système d'avis
                    if (isset($_SESSION['user_id'])) {
                        echo '<form action="ajouter_avis.php" method="post" class="avis-form">';
                        echo '<input type="hidden" name="product_title" value="' . htmlspecialchars($row['Nom']) . '">';
                        echo '<input type="hidden" name="nom" value="' . htmlspecialchars($_SESSION['prenom']) . '">';
                        echo '<input type="hidden" name="id_modele" value="' . htmlspecialchars($row['id_modele']) . '">';
                        echo '<label>Commentaire</label>';
                        echo '<textarea name="commentaire" required></textarea>';
                        echo '<input type="hidden" name="note" id="note_' . htmlspecialchars($row['id_modele']) . '" value="0">';
                        echo '<div class="star-rating" data-product="' . htmlspecialchars($row['id_modele']) . '">';
                        for ($i = 1; $i <= 5; $i++) {
                            echo '<span class="star" data-value="' . $i . '">★</span>';
                        }
                        echo '</div>';
                        echo "<button type='submit'>Envoyer l'avis</button>";
                        echo '</form>';
                    } else {
                        echo "<p><a href='connexion.php'>Connectez-vous</a> pour laisser un avis</p>";
                    }

                    // Affichage des avis
                    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $stmt = $pdo->prepare('SELECT * FROM avis WHERE id_modele = ? ORDER BY date_ajout DESC');
                    $stmt->execute([$row['id_modele']]);
                    $avis = $stmt->fetchAll();
                    echo '<div class="avis-section">';
                    foreach ($avis as $a) {
                        echo '<div class="avis">';
                        echo '<strong>' . htmlspecialchars($a['nom']) . '</strong>';
                        echo '<span>' . str_repeat('⭐', $a['note']) . '</span>';
                        echo '<p>' . nl2br(htmlspecialchars($a['commentaire'])) . '</p>';
                        $date = strtotime($a['date_ajout']);
                        if ($date && $date > 0) {
                            $formattedDate = date('d-m-Y', $date);
                        } else {
                            $formattedDate = 'Date invalide';
                        }
                        echo '<small>' . $formattedDate . '</small>';
                        echo '</div>';
                    }
                    echo '</div>';

                    // Bouton Favoris
                    echo "<button class='favorite-btn' data-product-id='" . $row['id_modele'] . "'>";
                    echo "<i class='fa fa-heart " . (in_array($row['id_modele'], $_SESSION['favorites'] ?? []) ? 'favorited' : '') . "'></i>";
                    echo "</button>";

                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo '<div class="col-12"><p class="text-center">Aucun périphérique disponible actuellement.</p></div>';
            }
            ?>
        </div>
    </div>

    <!-- Scripts JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            updateCartCount();

            $('.add-to-cart-btn').click(function() {
                var form = $(this).closest('.add-to-cart-form');
                var product = form.data('product');
                var price = form.data('price');
                var quantity = form.find('select[name="quantite"]').val();
                var imagePath = form.find('input[name="image_path"]').val();
                addToCart(product, price, parseInt(quantity), imagePath);
            });

            $('.favorite-btn').click(function() {
                var button = $(this);
                var productId = button.data('product-id');
                $.post('update_favorites.php', { product_id: productId }, function(response) {
                    if (response.success) {
                        button.find('i').toggleClass('favorited');
                    } else {
                        alert('Erreur lors de la mise à jour des favoris.');
                    }
                }, 'json');
            });
        });

        function addToCart(product, price, quantity, imagePath) {
            $.get('panier.php', {action: 'ajouter', produit: product, prix: price, quantite: quantity, image_path: imagePath}, function(response) {
                var data = JSON.parse(response);
                updateCartCount(data.count);
            });
        }

        function updateCartCount(count) {
            $('#cart-count').text(count);
        }

        function verifierConnexion() {
            <?php if (isset($_SESSION['nom']) && isset($_SESSION['prenom'])): ?>
                alert('Vous êtes déjà connecté.');
            <?php else: ?>
                alert('Veuillez vous connecter pour acheter.');
                window.location.href = 'Connexion.html';
            <?php endif; ?>
        }

        document.addEventListener("DOMContentLoaded", function(){
            document.querySelectorAll('.star-rating').forEach(function(rating){
                const stars = rating.querySelectorAll(".star");
                const product = rating.getAttribute('data-product');
                const noteInput = document.getElementById('note_' + product);
            
                stars.forEach(star => {
                    star.addEventListener("click", function(){
                        let value = this.getAttribute("data-value");
                        noteInput.value = value;
                        stars.forEach(s=> s.classList.remove("selected"));
                        for (let i = 0; i < value; i++) {
                            stars[i].classList.add("selected");
                        }
                    });
                });
            });
        });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?> 