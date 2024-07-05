<?php
session_start(); // Commence une nouvelle session ou reprend une session existante

// Vérifiez si l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: admin_login.php'); // Redirige vers la page de connexion si l'utilisateur n'est pas administrateur
    exit; // Arrête l'exécution du script
}

// Connexion à la base de données
$servername = "localhost"; // Nom du serveur
$username = "root"; // Nom d'utilisateur pour se connecter à la base de données
$password = ""; // Mot de passe pour se connecter à la base de données
$dbname = "ecom"; // Nom de la base de données

$conn = new mysqli($servername, $username, $password, $dbname); // Crée une nouvelle connexion à la base de données

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error); // Affiche un message d'erreur si la connexion échoue
}

// Récupérer tous les produits
$result = $conn->query("SELECT * FROM modele");
if ($result->num_rows > 0) {
    $produits = [];
    while ($row = $result->fetch_assoc()) {
        $produits[] = $row;
    }
}

// Ajouter, modifier ou supprimer un produit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $produits = $_POST['produits'];
    $nom = $_POST['nom'];
    $fabricant = $_POST['fabricant'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $image = $_POST['image'];

    if ($action == 'ajouter') {
        $stmt = $conn->prepare("INSERT INTO modele (produits, nom, fabricant, description, prix, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssds", $produits, $nom, $fabricant, $description, $prix, $image);
    } elseif ($action == 'modifier') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE modele SET produits=?, nom=?, fabricant=?, description=?, prix=?, image=? WHERE id_modele=?");
        $stmt->bind_param("ssssdsi", $produits, $nom, $fabricant, $description, $prix, $image, $id);
    } elseif ($action == 'supprimer') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM modele WHERE id_modele=?");
        $stmt->bind_param("i", $id);
    }
    $stmt->execute();
    $stmt->close();
}

$conn->close(); // Ferme la connexion à la base de données
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .product {
            position: relative;
            width: 200px;
            text-align: center;
        }
        .product img {
            width: 100%;
            cursor: pointer;
        }
        .product-options {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .product:hover .product-options {
            display: flex;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ajouter un produit</h1>
        <a href="logout.php">Déconnexion</a>
        <a href="index.php">Accueil</a>
        <!-- Combo box pour sélectionner un produit -->
        <select id="productSelect" class="form-control mb-3">
            <option value="">Sélectionner un produit</option>
            <?php foreach ($produits as $produit): ?>
                <option value="<?= htmlspecialchars(json_encode($produit)) ?>"><?= $produit['Fabricant'] . ' ' . $produit['Nom'] ?></option>
            <?php endforeach; ?>
        </select>
        <!-- Formulaire pour ajouter un nouveau produit -->
        <form action="admin.php" method="post">
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="action" id="action" value="ajouter">
            <label for="produits">Produits:</label>
            <select id="produits" name="produits" class="form-control" required>
                <option value="Ordinateur">Ordinateur</option>
                <option value="Périphériques">Périphériques</option>
                <option value="Composants">Composants</option>
            </select>
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" class="form-control" required>
            <label for="fabricant">Fabricant:</label>
            <input type="text" id="fabricant" name="fabricant" class="form-control" required>
            <label for="description">Description:</label>
            <textarea id="description" name="description" class="form-control" required></textarea>
            <label for="prix">Prix:</label>
            <input type="number" id="prix" name="prix" class="form-control" step="0.01" required>
            <label for="image">Image (URL):</label>
            <input type="text" id="image" name="image" class="form-control" required>
            <input type="submit" value="Ajouter le produit" onclick="document.getElementById('action').value='ajouter'">
            <input type="submit" value="Modifier le produit" onclick="document.getElementById('action').value='modifier'">
            <input type="submit" value="Supprimer le produit" onclick="document.getElementById('action').value='supprimer'">
        </form>
    </div>
    <script>
        function editProduct(button) {
            const product = button.closest('.product').querySelector('img');
            document.getElementById('id').value = product.dataset.id;
            document.getElementById('produits').value = product.dataset.produits;
            document.getElementById('nom').value = product.dataset.nom;
            document.getElementById('fabricant').value = product.dataset.fabricant;
            document.getElementById('description').value = product.dataset.description;
            document.getElementById('prix').value = product.dataset.prix;
            document.getElementById('image').value = product.dataset.image;
            document.getElementById('action').value = 'modifier';
        }

        function deleteProduct(button) {
            const product = button.closest('.product').querySelector('img');
            document.getElementById('id').value = product.dataset.id;
            document.getElementById('action').value = 'supprimer';
            document.querySelector('form').submit();
        }

        // Remplir les champs de texte lorsque vous sélectionnez un produit dans la combo box
        document.getElementById('productSelect').addEventListener('change', function() {
            const selectedProduct = JSON.parse(this.value);
            if (selectedProduct) {
                document.getElementById('id').value = selectedProduct.id_modele;
                document.getElementById('produits').value = selectedProduct.Produits;
                document.getElementById('nom').value = selectedProduct.Nom;
                document.getElementById('fabricant').value = selectedProduct.Fabricant;
                document.getElementById('description').value = selectedProduct.Description;
                document.getElementById('prix').value = selectedProduct.Prix;
                document.getElementById('image').value = selectedProduct.Image;
                document.getElementById('action').value = 'modifier';
            } else {
                // Réinitialiser les champs si aucun produit n'est sélectionné
                document.getElementById('id').value = '';
                document.getElementById('produits').value = '';
                document.getElementById('nom').value = '';
                document.getElementById('fabricant').value = '';
                document.getElementById('description').value = '';
                document.getElementById('prix').value = '';
                document.getElementById('image').value = '';
                document.getElementById('action').value = 'ajouter';
            }
        });
    </script>
</body>
</html>
