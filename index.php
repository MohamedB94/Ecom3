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
                    echo '<a href="panier.php" class="btn btn-info mr-2">Panier <span id="cart-count" class="badge badge-light">0</span></a>';

                
                } else {
                    echo '<a href="Connexion.html" class="btn btn-primary mr-2">Connexion</a>';
                    echo '<a href="Inscription.html" class="btn btn-secondary mr-2">Inscription</a>';
                }
                echo '<a href="panier.php" class="btn btn-warning">Panier <span id="cart-count">' . (isset($_SESSION['panier']) ? count($_SESSION['panier']) : 0) . '</span></a>';
                ?>
                <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
                    <a href="admin.php" class="btn btn-success">Ajouter un produit</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <!-- Contenu principal de la page -->
    <div class="container mt-5">
        <h2 class="text-center">Nos Produits</h2>
        <div class="row" id="results">
            <?php
            // Inclure le fichier de configuration
            require_once 'config.php';

            // Connexion à la base de données
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            if ($conn->connect_error) {
                die("Connexion échouée : " . $conn->connect_error);
            }

            // Préparer la requête SQL pour obtenir tous les produits
            $sql = "SELECT * FROM modele";
            $result = $conn->query($sql);

            // Vérifier si des résultats ont été trouvés
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Calculer la moyenne des notes
                    $id_modele = $row['id_modele'];
                    $sql_notes = "SELECT AVG(note) as moyenne FROM notes WHERE id_modele = $id_modele";
                    $result_notes = $conn->query($sql_notes);
                    $moyenne = $result_notes->fetch_assoc()['moyenne'];
                    $moyenne = $moyenne ? round($moyenne, 1) : 'Pas de notes';

                    echo "<div class='col-md-4'>";
                    echo "<div class='card mb-4 shadow-sm'>";
                    echo "<img src='images/" . $row['Image'] . "' class='card-img-top' alt='" . $row['Nom'] . "'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>" . $row['Fabricant'] . " " . $row['Nom'] . "</h5>";
                    echo "<p class='card-text'>" . $row['Description'] . "</p>";
                    echo "<p class='card-text'>Note moyenne: $moyenne étoiles</p>";
                    echo "<div class='d-flex flex-column'>";
                    echo "<form class='add-to-cart-form' data-product='" . $row['Nom'] . "' data-price='" . $row['Prix'] . "'>";
                    echo "<input type='hidden' name='action' value='ajouter'>";
                    echo "<input type='hidden' name='produit' value='" . $row['Nom'] . "'>";
                    echo "<input type='hidden' name='prix' value='" . $row['Prix'] . "'>";
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
                        echo "<a href='achat.php' class='btn btn-primary'>Acheter</a>";
                    } else {
                        echo "<button class='btn btn-primary acheter-btn' onclick='verifierConnexion()'>Acheter</button>";
                    }
                    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
                        echo "<button class='btn btn-warning' onclick='editProduct(" . json_encode($row) . ")'>Modifier</button>";
                        echo "<button class='btn btn-danger' onclick='deleteProduct(" . $row['id_modele'] . ")'>Supprimer</button>";
                    }
                    if (isset($_SESSION['user_id'])) {
                        echo "<button class='btn btn-primary' onclick='openModal(" . $row['id_modele'] . ")'>Noter</button>";
                    } else {
                        echo "<button class='btn btn-primary' onclick='alert(\"Veuillez vous connecter pour noter ce produit.\")'>Noter</button>";
                    }
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
    <!-- Fenêtre modale pour noter un produit -->
    <div id="noteModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Quelle note mettez-vous ?</h2>
            <form id="noteForm" action="noter_produit.php" method="POST">
                <input type="hidden" name="produit_id" id="modalProduitId">
                <label for="modalNote">Note :</label>
                <select name="note" id="modalNote" required>
                    <option value="1">1 étoile</option>
                    <option value="2">2 étoiles</option>
                    <option value="3">3 étoiles</option>
                    <option value="4">4 étoiles</option>
                    <option value="5">5 étoiles</option>
                </select>
                <button type="submit">Soumettre</button>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // Initialiser le compteur de panier
            updateCartCount();

            $('.add-to-cart-btn').click(function() {
                var form = $(this).closest('.add-to-cart-form');
                var product = form.data('product');
                var price = form.data('price');
                var quantity = form.find('select[name="quantite"]').val();
                addToCart(product, price, parseInt(quantity));
            });
        });

        function addToCart(product, price, quantity) {
            $.get('panier.php', {action: 'ajouter', produit: product, prix: price, quantite: quantity}, function(response) {
                var data = JSON.parse(response);
                updateCartCount();
            });
        }

        function updateCartCount() {
            $.get('panier.php', {action: 'compter'}, function(response) {
                var data = JSON.parse(response);
                $('#cart-count').text(data.count);
            });
        }

        function verifierConnexion() {
            if (confirm("Veuillez vous connecter pour acheter. Voulez-vous être redirigé vers la page de connexion ?")) {
                window.location.href = 'Connexion.html';
            }
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

        function openModal(produitId) {
            document.getElementById('modalProduitId').value = produitId;
            document.getElementById('noteModal').style.display = 'block';
        }

        document.querySelector('.close').onclick = function() {
            document.getElementById('noteModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('noteModal')) {
                document.getElementById('noteModal').style.display = 'none';
            }
        }
    </script>
    <style>
        /* Style pour la fenêtre modale */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</body>
</html>
