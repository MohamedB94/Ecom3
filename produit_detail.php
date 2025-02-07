<?php

// Inclure le fichier de configuration
require_once 'config.php';

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Connexion à la base de données avec PDO
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion échouée : " . $e->getMessage());
}

// Récupérer l'ID du produit
$produit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Préparer la requête SQL pour obtenir les détails du produit
$sql = "SELECT * FROM modele WHERE id_modele = $produit_id";
$result = $pdo->query($sql);

// Vérifier si le produit existe
if ($result->rowCount() > 0) {
    $produit = $result->fetch(PDO::FETCH_ASSOC);
    $product_title = $produit['Nom'];
} else {
    echo "<p>Produit non trouvé</p>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_SESSION['nom'])) {
    $nom = $_SESSION['nom'];
    $commentaire = trim($_POST['commentaire']);
    $note = (int) $_POST['note'];
    $date_ajout = date('Y-m-d H:i:s');

    if (!empty($commentaire) && $note >= 1 && $note <= 5) {
        $stmt = $pdo->prepare("INSERT INTO avis (product_title, nom, commentaire, note, date_ajout) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$product_title, $nom, $commentaire, $note, $date_ajout]);
    }
}

$avisStmt = $pdo->prepare("SELECT * FROM avis WHERE product_title = ? ORDER BY date_ajout DESC");
$avisStmt->execute([$product_title]);
$avis = $avisStmt->fetchAll();



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du produit</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .product-details {
            margin-top: 50px;
        }
        .product-title {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 20px;
            margin-left: 575px;
        }
        .product-image {
            max-width: 100%;
            height: auto;
        }
        .product-info {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .product-price {
            font-size: 24px;
            color: #28a745;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .add-to-cart-btn {
            margin-top: 20px;
            background-color: #007bff;
            border-color: #007bff;
        }
        .add-to-cart-btn:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .back-to-home-btn {
            margin-top: 20px;
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
        
        .avis {
        background-color: white;
        padding: 15px;
        margin-top:10px ;
        border-radius: 5px;
        box-shadow: 0 2px 3px black;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .avis:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 6px black;
        }

        .avis strong{
            font-size: 16px;
            color: #333;
        }

        .avis span {
            font-size: 16px;
            color: #f39c12;
        }

        .avis p {
            margin-top: 10px;
            line-height: 1.6;
            font-size: 14px;
            color: #555;
        }

        .avis small {
            display: block;
            margin-top: 10px;
            font-size: 12px;
            color: #666;
        }

        textarea{
            width: 99%;
            min-height: 80px;
            margin-top: 5px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: none;
            font-size: 14px;
        }


    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container product-details">
        <h2 class="text-center product-title"><?= htmlspecialchars($produit['Nom']) ?></h2>
        <div class="row">
            <div class="col-md-6">
                <img src="images/<?= htmlspecialchars($produit['Image']) ?>" class="product-image" alt="<?= htmlspecialchars($produit['Nom']) ?>">
            </div>
            <div class="col-md-6 product-info">
                <h3><?= htmlspecialchars($produit['Fabricant']) ?></h3>
                <p><?= htmlspecialchars($produit['Description']) ?></p>
                <p class="product-price">Prix: <?= htmlspecialchars($produit['Prix']) ?> €</p>
                <button class="btn btn-primary add-to-cart-btn">Ajouter au panier</button>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-secondary back-to-home-btn">Retour à l'accueil</a>
        </div>
    

    <h2>Donnez votre avis</h2>
    <?php if (isset($_SESSION['nom'])) : ?>
        <form action="" method="post">
            <input type="hidden" name="note" id="note" value="0">

            <label>Commentaire: </label>
            <textarea name="commentaire" required></textarea>
            <button type="submit">Envoyer</button>
        </form>
    <?php else : ?>
        <p><a href="Connexion.html">Connectez-vous</a> pour laisser un avis.</p>
    <?php endif; ?>
    </div>
    <div class="star-rating">
        <?php for ($i = 1; $i <= 5; $i++) : ?>
        <span class="star" data-value="">★</span>
        <?php endfor; ?>
    </div>
    <h2>Avis des utilisateur</h2>
    <?php foreach ($avis as $a): ?>
        <div class="avis">
            <strong><?= htmlspecialchars($a['nom']); ?></strong> -
            <span><?= str_repeat('⭐', $a['note']); ?> </span>
            <p><?= nl2br(htmlspecialchars($a['commentaire'])); ?></p>
            <small>Posté le <?= $a['date_ajout']; ?></small>     
        </div>
    <?php endforeach; ?>
    <script>

             const stars = document.querySelectorAll('.star');
             const note = document.getElementById('note'); 

             stars.forEach(star=>{
                star.addEventListener('click', ()=>{
                    let value = this.getAttribute('data-value');
                    noteInput.value = value;

                    stars.forEach(s=> s.classList.remove('selected'));
                    for (let i = 0; i < value; i++) {
                        stars[i].classList.add('selected');
                    }
                })
             })
    </script>
</body>
</html>