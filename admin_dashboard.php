<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est un admin
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: index.php');
    exit();
}

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Traitement de l'ajout d'un produit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'ajouter') {
    $nom = $_POST['nom'];
    $fabricant = $_POST['fabricant'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $categorie = $_POST['categorie'];
    
    // Traitement de l'image
    $target_dir = "images/";
    $image = "";
    
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        // Vérifier si c'est une vraie image
        if(getimagesize($_FILES["image"]["tmp_name"]) !== false) {
            // Vérifier la taille du fichier
            if ($_FILES["image"]["size"] <= 500000) {
                // Autoriser certains formats de fichier
                if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif" ) {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $image = basename($_FILES["image"]["name"]);
                    }
                }
            }
        }
    }

    // Insérer le produit dans la base de données
    $stmt = $conn->prepare("INSERT INTO modele (Nom, Fabricant, Description, Prix, Produits, Image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdss", $nom, $fabricant, $description, $prix, $categorie, $image);
    
    if ($stmt->execute()) {
        $_SESSION['notification'] = "Produit ajouté avec succès";
    } else {
        $_SESSION['notification'] = "Erreur lors de l'ajout du produit";
    }
    
    $stmt->close();
    header('Location: admin_dashboard.php');
    exit();
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .admin-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h2 class="text-center mb-4">Ajouter un nouveau produit</h2>
        
        <?php if (isset($_SESSION['notification'])): ?>
            <div class="alert alert-info">
                <?php 
                echo $_SESSION['notification'];
                unset($_SESSION['notification']);
                ?>
            </div>
        <?php endif; ?>

        <form action="admin_dashboard.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="ajouter">
            
            <div class="form-group">
                <label for="nom">Nom du produit</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>

            <div class="form-group">
                <label for="fabricant">Fabricant</label>
                <input type="text" class="form-control" id="fabricant" name="fabricant" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="prix">Prix</label>
                <input type="number" class="form-control" id="prix" name="prix" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="categorie">Catégorie</label>
                <select class="form-control" id="categorie" name="categorie" required>
                    <option value="Ordinateur">Ordinateur</option>
                    <option value="Composant">Composant</option>
                    <option value="Péripheriques">Périphériques</option>
                    <option value="Gaming">Gaming</option>
                </select>
            </div>

            <div class="form-group">
                <label for="image">Image du produit</label>
                <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
            </div>

            <button type="submit" class="btn btn-primary">Ajouter le produit</button>
            <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?> 