<?php
include 'config.php';

date_default_timezone_set('Europe/Paris');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // initialiser la connexion pdo
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     
    // verifier que les donnees existent et ne sont pas vides
    $errors = [];

    if (!isset($_POST['product_title']) || empty(trim($_POST['product_title']))) {
        $errors[] = "Le nom du produit est obligatoire.";
    }
    if (!isset($_POST['nom']) || empty(trim($_POST['nom']))) {
        $errors[] = "Le nom est obligatoire.";
    }
    if (!isset($_POST['commentaire']) || empty(trim($_POST['commentaire']))) {
        $errors[] = "Le commentaire est obligatoire.";
    }
    if (!isset($_POST['note']) || empty(trim($_POST['note']))) {
        $errors[] = "La note est obligatoire.";
    }
    if (!isset($_POST['id_modele']) || empty(trim($_POST['id_modele']))) {
        $errors[] = "L'identifiant du modèle est obligatoire.";
    }

    // Debugging lines
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    if (empty($errors)) {
        // recuperer les donnees du formulaire
        $product_title = $_POST['product_title'];
        $nom = $_POST['nom'];
        $commentaire = $_POST['commentaire'];
        $note = intval($_POST['note']);
        $id_modele = $_POST['id_modele']; // Add this line

        // Debugging lines
        echo "<pre>";
        var_dump($product_title, $nom, $commentaire, $note, $id_modele);
        echo "</pre>";

        $stmt = $pdo->prepare("INSERT INTO avis (id_modele, product_title, nom, commentaire, note) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id_modele, $product_title, $nom, $commentaire, $note]);

        header('Location: index.php');
        exit;
    } else {
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}
?>