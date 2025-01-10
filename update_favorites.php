<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Initialize favorites if not set
    if (!isset($_SESSION['favorites'])) {
        $_SESSION['favorites'] = array();
    }

    // Add or remove the product from favorites
    if (in_array($product_id, $_SESSION['favorites'])) {
        // Remove from favorites
        $_SESSION['favorites'] = array_diff($_SESSION['favorites'], array($product_id));
        $favorited = false;
    } else {
        // Add to favorites
        $_SESSION['favorites'][] = $product_id;
        $favorited = true;
    }

    // Return a JSON response
    echo json_encode(array('success' => true, 'favorited' => $favorited));
    exit;
}

echo json_encode(array('success' => false));
?>