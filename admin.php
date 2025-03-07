<?php
session_start(); // Commence une nouvelle session ou reprend une session existante
require_once 'config.php';

// Vérifiez si l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: admin_login.php'); // Redirige vers la page de connexion si l'utilisateur n'est pas administrateur
    exit; // Arrête l'exécution du script
}

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); // Crée une nouvelle connexion à la base de données

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error); // Affiche un message d'erreur si la connexion échoue
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    switch($action) {
        case 'modifier':
            $nom = $_POST['nom'];
            $fabricant = $_POST['fabricant'];
            $description = $_POST['description'];
            $prix = $_POST['prix'];
            $categorie = $_POST['produits'];
            
            // Gestion de l'image
            $image = $_POST['image_actuelle']; // Garder l'image actuelle par défaut
            
            if (isset($_FILES["nouvelle_image"]) && $_FILES["nouvelle_image"]["error"] == 0) {
                $target_dir = "images/";
                $target_file = $target_dir . basename($_FILES["nouvelle_image"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                
                if(getimagesize($_FILES["nouvelle_image"]["tmp_name"]) !== false) {
                    if ($_FILES["nouvelle_image"]["size"] <= 500000) {
                        if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif" ) {
                            if (move_uploaded_file($_FILES["nouvelle_image"]["tmp_name"], $target_file)) {
                                $image = basename($_FILES["nouvelle_image"]["name"]);
                            }
                        }
                    }
                }
            }

            $stmt = $conn->prepare("UPDATE modele SET Nom=?, Fabricant=?, Description=?, Prix=?, Produits=?, Image=? WHERE id_modele=?");
            $stmt->bind_param("sssdssi", $nom, $fabricant, $description, $prix, $categorie, $image, $id);
            
            if ($stmt->execute()) {
                $_SESSION['notification'] = "Produit modifié avec succès";
            } else {
                $_SESSION['notification'] = "Erreur lors de la modification du produit";
            }
            $stmt->close();
            break;

        case 'supprimer':
            // Récupérer l'image avant la suppression
            $stmt = $conn->prepare("SELECT Image FROM modele WHERE id_modele = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $image_path = "images/" . $row['Image'];
                if (file_exists($image_path)) {
                    unlink($image_path); // Supprimer l'image du serveur
                }
            }
            $stmt->close();

            // Supprimer le produit
            $stmt = $conn->prepare("DELETE FROM modele WHERE id_modele = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['notification'] = "Produit supprimé avec succès";
            } else {
                $_SESSION['notification'] = "Erreur lors de la suppression du produit";
            }
            $stmt->close();
            break;
    }
}

// Récupérer tous les produits
$result = $conn->query("SELECT * FROM modele");
if ($result->num_rows > 0) {
    $produits = [];
    while ($row = $result->fetch_assoc()) {
        $produits[] = $row;
    }
}

$conn->close(); // Ferme la connexion à la base de données
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter un produit</h1>
        <div class="d-flex justify-content-between mb-3">
            <a href="logout.php" class="btn btn-link">Déconnexion</a>
            <a href="index.php" class="btn btn-link">Accueil</a>
        </div>
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
            <div class="form-row">
                <div class="col">
                    <label for="nom">Nom:</label>
                    <input type="text" id="nom" name="nom">
                </div>
                <div class="col">
                    <label for="fabricant">Fabricant:</label>
                    <input type="text" id="fabricant" name="fabricant">
                </div>
            </div>
            <div class="form-row">
                <div class="col">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description"></textarea>
                </div>
            </div>
            <div class="form-row">
                <div class="col">
                    <label for="prix">Prix:</label>
                    <input type="text" id="prix" name="prix">
                </div>
                <div class="col">
                    <label for="image">Image (URL):</label>
                    <input type="text" id="image" name="image">
                </div>
            </div>
            <input type="submit" value="Ajouter le produit">
            <input type="submit" value="Modifier le produit">
            <input type="submit" value="Supprimer le produit">
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