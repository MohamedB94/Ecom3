<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
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
    <title>Accueil</title>
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

        .notification button {
            background: none;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-left: 10px;
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
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>
    <!-- Contenu principal de la page -->
    <div class="container mt-5">
        <h2 class="text-center">Nos Produits</h2>
        <form id="filterForm" action="index.php" method="GET" class="form-inline">
            <div class="form-group">
                <label for="produits">Catégorie:</label>
                <select name="produits" id="produits" class="form-control">
                    <option value="">Toutes</option>
                    <option value="Ordinateur">Ordinateurs</option>
                    <option value="Composant">Composants</option>
                    <option value="Péripheriques">Périphériques</option>
                </select>
            </div>
            <div class="form-group">
                <label for="prix_min">Prix Min:</label>
                <input type="number" name="prix_min" id="prix_min" class="form-control" placeholder="0">
            </div>
            <div class="form-group">
                <label for="prix_max">Prix Max:</label>
                <input type="number" name="prix_max" id="prix_max" class="form-control" placeholder="1000">
            </div>
            <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>
        <div class="row" id="results">
            <?php
            // Définir le fuseau horaire par défaut à Paris
            date_default_timezone_set('Europe/Paris');

            // Inclure le fichier de configuration
            require_once 'config.php';

            // Connexion à la base de données
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            if ($conn->connect_error) {
                die("Connexion échouée : " . $conn->connect_error);
            }

            // Récupérer les filtres
            $produits = isset($_GET['produits']) ? $_GET['produits'] : '';
            $prix_min = isset($_GET['prix_min']) ? $_GET['prix_min'] : 0;
            $prix_max = isset($_GET['prix_max']) ? $_GET['prix_max'] : 10000;
            $search = isset($_GET['search']) ? $_GET['search'] : '';

            // Préparer la requête SQL pour obtenir les produits
            $sql = "SELECT * FROM modele WHERE 1=1";
            if ($produits) {
                $sql .= " AND Produits = '" . $conn->real_escape_string($produits) . "'";
            }
            if ($prix_min) {
                $sql .= " AND prix >= " . (int)$prix_min;
            }
            if ($prix_max) {
                $sql .= " AND prix <= " . (int)$prix_max;
            }
            if ($search) {
                $sql .= " AND (Nom LIKE '%" . $conn->real_escape_string($search) . "%' OR Fabricant LIKE '%" . $conn->real_escape_string($search) . "%' OR Description LIKE '%" . $conn->real_escape_string($search) . "%')";
            }

            $result = $conn->query($sql);

            // Afficher les résultats
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    echo "<div class='col-md-4'>";
                    echo "<div class='card mb-4 shadow-sm'>";
                    echo "<img src='images/" . $row['Image'] . "' class='card-img-top' alt='" . $row['Nom'] . "'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>" . $row['Fabricant'] . " " . $row['Nom'] . "</h5>";
                    echo "<p class='card-text'>" . $row['Description'] . "</p>";
                    echo "<p class='card-text'>Prix: " . $row['Prix'] . " €</p>"; // Afficher le prix du produit
                    echo "<div class='d-flex flex-column'>";
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
                    if (isset($_SESSION['prenom']) && isset($_SESSION['nom'])) {
                        echo "<a href='redirect_to_stripe.php' class='btn btn-primary'>Acheter</a>";
                    } else {
                        echo "<button class='btn btn-primary acheter-btn' onclick='verifierConnexion()'>Acheter</button>";
                    }
                    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
                        echo "<button class='btn btn-warning' onclick='editProduct(" . json_encode($row) . ")'>Modifier</button>";
                        echo "<button class='btn btn-danger' onclick='deleteProduct(" . $row['id_modele'] . ")'>Supprimer</button>";
                    }
                     if (isset($_SESSION['user_id'])) {
                        echo '<form action="ajouter_avis.php" method="post" class="avis_form>"';
                        echo '<input type="hidden" name="id_modele" value="' . htmlspecialchars( $row['id_modele']) . '">';
                        echo '<input type="hidden" name="nom" value="' . htmlspecialchars( $_SESSION['user_id']) . '">';

                        echo '<label> Commentaire </label>';
                        echo '<textarea name="commentaire" required></textarea>';

                        echo '<input type="hidden" name="note" id="note_' . htmlspecialchars( $row['id_modele']) .'" value="0">';

                        echo '<div class="star-rating" data-product="' . htmlspecialchars( $row['id_modele']) . '">';
                        for ($i= 1; $i <= 5; $i++) {
                          echo '<span class="star" data-value="' . $i . '">⭐</span>';
                        }
                        echo '</div>';

                        echo "<button type='submit'>Envoyer l'avis</button>";
                        echo '</form>';
                     } else {
                       echo "<p><a href='connexion.php'>Connectez-vous</a> pour laisser un avis</p>";
                     }
                    // Initialize PDO connection
                    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $stmt = $pdo->prepare('SELECT * FROM avis WHERE id_modele = ? order by date_ajout desc');
                    $stmt->execute([$row['id_modele']]);
                    $avis = $stmt->fetchAll();
                    echo '<div class="avis-section">';
                    foreach ($avis as $a) {
                        echo '<div class="avis">';
                        echo '<strong>' . htmlspecialchars($a['nom']) . '</strong>';
                        echo '<span>' . str_repeat('⭐', $a['note']) . '</span>';
                        echo '<p>' . nl2br(htmlspecialchars($a['commentaire'])) . '</p>';
                        echo '<small>' . $a['date_ajout'] . '</small>';
                        echo '</div>';
                    }
                    echo '</div>';



                    echo "<button class='favorite-btn' data-product-id='" . $row['id_modele'] . "'>";
                    echo "<i class='fa fa-heart " . (in_array($row['id_modele'], $_SESSION['favorites'] ?? []) ? 'favorited' : '') . "'></i>";
                    echo "</button>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }

            } else {
                echo "<p class='text-center'>Aucun produit trouvé</p>";
            }
            $conn->close();
            ?>
        </div>
    </div>
    <!-- Formulaire pour modifier un produit -->
    <form id="productForm" action="admin.php" method="post" style="display: none;">
        <input type="hidden" name="id" id="id">
        <input type="hidden" name="action" id="action" value="modifier">
        <label for="produits">Produits:</label>
        <input type="text" id="produits" name="produits" required>
        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom" required>
        <label for="fabricant">Fabricant:</label>
        <input type="text" id="fabricant" name="fabricant" required>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        <label for="prix">Prix:</label>
        <input type="number" id="prix" name="prix" step="0.01" required>
        <label for="image">Image (URL):</label>
        <input type="text" id="image" name="image" required>
        <input type="submit" value="Modifier le produit">
    </form>
    
    <?php if (isset($_SESSION['notification'])): ?>
        <div id="notification" class="notification">
            <span id="notification-message"><?= $_SESSION['notification'] ?></span>
            <button onclick="closeNotification()">X</button>
        </div>
        <script>
            function closeNotification() {
                document.getElementById('notification').style.display = 'none';
            }

            setTimeout(() => {
                closeNotification();
            }, 3000); // 3 secondes
        </script>
        <?php unset($_SESSION['notification']); ?>
    <?php endif; ?>
    <script>
        $(document).ready(function() {
            // Initialiser le compteur de panier
            updateCartCount();

            $('.add-to-cart-btn').click(function() {
                var form = $(this).closest('.add-to-cart-form');
                var product = form.data('product');
                var price = form.data('price');
                var quantity = form.find('select[name="quantite"]').val();
                var imagePath = form.find('input[name="image_path"]').val();
                addToCart(product, price, parseInt(quantity), imagePath);
            });

            $('.remove-from-cart-btn').click(function() {
                var product = $(this).data('product');
                var quantity = $(this).data('quantity');
                removeFromCart(product, quantity);
            });

            $('.favorite-btn').click(function() {
                var button = $(this);
                var productId = button.data('product-id');

                // Send an AJAX request to update favorites
                $.post('update_favorites.php', { product_id: productId }, function(response) {
                    if (response.success) {
                        // Toggle the heart color
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
                updateCartCount(data.count); // Mettre à jour le compteur du panier
            });
        }

        function removeFromCart(product, quantity) {
            $.get('panier.php', {action: 'supprimer', produit: product, quantite: quantity}, function(response) {
                var data = JSON.parse(response);
                updateCartCount(data.count); // Mettre à jour le compteur du panier
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

        function editProduct(product) {
            document.getElementById('id').value = product.id_modele;
            document.getElementById('produits').value = product.Produits;
            document.getElementById('nom').value = product.Nom;
            document.getElementById('fabricant').value = product.Fabricant;
            document.getElementById('description').value = product.Description;
            document.getElementById('prix').value = product.Prix;
            document.getElementById('image').value = product.Image;
            document.getElementById('action').value = 'modifier';
            document.getElementById('productForm').scrollIntoView();
        }

        function deleteProduct(id) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce produit ?")) {
                $.post('admin.php', {id: id, action: 'supprimer'}, function(response) {
                    location.reload();
                });
            }
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
</body>
</html>