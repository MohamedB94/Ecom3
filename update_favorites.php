<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Initialiser les favoris s'ils n'existent pas
    if (!isset($_SESSION['favorites'])) {
        $_SESSION['favorites'] = array();
    }

    // Ajouter ou supprimer le produit des favoris
    if (in_array($product_id, $_SESSION['favorites'])) {
        // Supprimer des favoris
        $_SESSION['favorites'] = array_diff($_SESSION['favorites'], array($product_id));
        $favorited = false;
    } else {
        // Ajouter aux favoris
        $_SESSION['favorites'][] = $product_id;
        $favorited = true;
    }

    // Retourner une réponse JSON
    echo json_encode(array('success' => true, 'favorited' => $favorited));
    exit;
}

echo json_encode(array('success' => false));
?>