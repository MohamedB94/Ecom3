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

        .avis-form{
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .avis-form textarea{
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
            transition: color 0.2;
        }

        .star:hover,
        .star.selected {
            color:gold
        }

        .product-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-card .card-img-top {
            height: 200px;
            object-fit: contain;
            padding: 15px;
        }

        .product-card .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-card .card-title {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .product-card .card-text {
            font-size: 0.9rem;
        }

        .col-md-4 {
            margin-bottom: 30px;
        }

        .card {
            height: 100%;
        }

        .admin-actions {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .modal-body .form-group {
            margin-bottom: 1rem;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>
    <!-- Contenu principal de la page -->
    <div class="container mt-5">
        <div class="admin-header">
            <h2 class="text-center">Nos Produits</h2>
            <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
                <a href="admin_dashboard.php" class="btn btn-success">Ajouter un nouveau produit</a>
            <?php endif; ?>
        </div>
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
                    echo "<div class='card mb-4 shadow-sm product-card'>"; // Ajoutez la classe 'product-card' ici
                    echo "<a href='produit_detail.php?id=" . $row['id_modele'] . "'>";
                    echo "<img src='images/" . $row['Image'] . "' class='card-img-top' alt='" . $row['Nom'] . "'>";
                    echo "</a>";
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
                    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
                        <div class="admin-actions">
                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal<?php echo $row['id_modele']; ?>">
                                Modifier
                            </button>
                            <button type="button" class="btn btn-danger" onclick="confirmerSuppression(<?php echo $row['id_modele']; ?>)">
                                Supprimer
                            </button>
                        </div>

                        <!-- Modal de modification -->
                        <div class="modal fade" id="editModal<?php echo $row['id_modele']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $row['id_modele']; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel<?php echo $row['id_modele']; ?>">Modifier le produit</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="admin.php" method="POST" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="action" value="modifier">
                                            <input type="hidden" name="id" value="<?php echo $row['id_modele']; ?>">
                                            <input type="hidden" name="image_actuelle" value="<?php echo $row['Image']; ?>">
                                            
                                            <div class="form-group">
                                                <label for="nom<?php echo $row['id_modele']; ?>">Nom du produit</label>
                                                <input type="text" class="form-control" id="nom<?php echo $row['id_modele']; ?>" name="nom" value="<?php echo htmlspecialchars($row['Nom']); ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="fabricant<?php echo $row['id_modele']; ?>">Fabricant</label>
                                                <input type="text" class="form-control" id="fabricant<?php echo $row['id_modele']; ?>" name="fabricant" value="<?php echo htmlspecialchars($row['Fabricant']); ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="description<?php echo $row['id_modele']; ?>">Description</label>
                                                <textarea class="form-control" id="description<?php echo $row['id_modele']; ?>" name="description" rows="3" required><?php echo htmlspecialchars($row['Description']); ?></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="prix<?php echo $row['id_modele']; ?>">Prix</label>
                                                <input type="number" class="form-control" id="prix<?php echo $row['id_modele']; ?>" name="prix" step="0.01" value="<?php echo $row['Prix']; ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="produits<?php echo $row['id_modele']; ?>">Catégorie</label>
                                                <select class="form-control" id="produits<?php echo $row['id_modele']; ?>" name="produits" required>
                                                    <option value="Ordinateur" <?php echo $row['Produits'] == 'Ordinateur' ? 'selected' : ''; ?>>Ordinateur</option>
                                                    <option value="Composant" <?php echo $row['Produits'] == 'Composant' ? 'selected' : ''; ?>>Composant</option>
                                                    <option value="Péripheriques" <?php echo $row['Produits'] == 'Péripheriques' ? 'selected' : ''; ?>>Périphériques</option>
                                                    <option value="Gaming" <?php echo $row['Produits'] == 'Gaming' ? 'selected' : ''; ?>>Gaming</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Image actuelle</label>
                                                <img src="images/<?php echo $row['Image']; ?>" alt="Image actuelle" style="max-width: 100px; display: block; margin-bottom: 10px;">
                                                <label for="nouvelle_image<?php echo $row['id_modele']; ?>">Nouvelle image (optionnel)</label>
                                                <input type="file" class="form-control-file" id="nouvelle_image<?php echo $row['id_modele']; ?>" name="nouvelle_image" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])) {
                        echo '<form action="ajouter_avis.php" method="post" class="avis-form">';
                        echo '<input type="hidden" name="product_title" value="' . htmlspecialchars($row['Nom']) . '">';
                        echo '<input type="hidden" name="nom" value="' . htmlspecialchars($_SESSION['prenom']) . '">';
                        echo '<input type="hidden" name="id_modele" value="' . htmlspecialchars($row['id_modele']) . '">'; // Add this line
                        echo '<label> Commentaire </label>';
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
                    // Initialize PDO connection
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
                        // Format the date correctly
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

        function confirmerSuppression(id) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce produit ?")) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = 'admin.php';
                
                var actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'supprimer';
                
                var idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = id;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

    </script>
    <?php include 'footer.php'?>
</body>
</html>