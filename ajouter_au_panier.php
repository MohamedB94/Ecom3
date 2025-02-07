<?php
session_start();

// Vérifiez si le panier existe dans la session, sinon créez-le
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = array();
}

// Traitez la requête AJAX pour ajouter un produit au panier
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'ajouter') {
    $produit = $_POST['produit'];
    $prix = $_POST['prix'];
    $quantite = $_POST['quantite'];
    $image_path = $_POST['image_path'];
    $fabricant = $_POST['fabricant'];

    // Ajoutez le produit au panier
    $_SESSION['panier'][] = array(
        'produit' => $produit,
        'prix' => $prix,
        'quantite' => $quantite,
        'image_path' => $image_path,
        'fabricant' => $fabricant
    );

    // Réponse JSON
    echo json_encode(array('success' => true));
    exit();
}

foreach ($_SESSION['panier'] as $item) {
    echo $item['fabricant'] . " " . $item['produit'] . " - " . $item['prix'] . " € x" . $item['quantite'] . " = " . ($item['prix'] * $item['quantite']) . " €<br>";
}
?>