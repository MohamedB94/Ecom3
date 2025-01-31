<?php
session_start();

//verifier si le panier contient des produits
if(isset($_SESSION['panier']) && !empty($_SESSION['panier'])){
    // connexion a la base de donnees avec gestion des erreurs
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=ecom', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //calculer le total du panier
        $total_price = 0;
        $cart_items = $_SESSION['panier'];
        foreach($cart_items as $item_id){
                    $stmt = $pdo->prepare('SELECT Prix FROM modele WHERE id_modele = ?');
                    $stmt->execute([$item_id]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);
                    if($product){
                        $total_price += $product['prix'];
                    }
            }
            // verifier si l'utilisateur est connecte
            $user_id = 1;
    
            $stmt = $pdo->prepare("INSERT INTO commande (id_user, prix_total) VALUES (?, ?)");
            $stmt->execute([$user_id, $total_price]);

                    // recuperer l'id de la commande inseree
                    $order_id = $pdo->lastInsertId();

                    //inserer les element du panier dans la table details_commande
                    foreach($cart_items as $item_id){
                        //recuperer les informations du produit
                        $stmt = $pdo->prepare("SELECT Prix FROM modele WHERE id_modele = ?");
                        $stmt->execute([$item_id]);
                        $product = $stmt->fetch(PDO::FETCH_ASSOC);
                        if($product){
                            //inserer les articles dans la table detail_commande
                            $stmt = $pdo->prepare("INSERT INTO detail_commande (id_commande, id_modele, quantite, prix) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$order_id, $item_id, 1, $product['prix']]);
                        }
                    }
                    // vider le panier apres la commande
                    unset($_SESSION['panier']);

                    // redirection vers stripe
                    $stripe_url = "https://buy.stripe.com/test_8wM8xD3nS9RZ6nS001";
                    header("Location: $stripe_url?order_id=$order_id");
                    // amazonq-ignore-next-line
                    exit();
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        // amazonq-ignore-next-line
        exit();
    }
} else {
    echo "Votre panier est vide.";
}
?>

<?php
//verifier l'id de la commande
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
   
    try{
        $pdo = new PDO('mysql:host=localhost;dbname=ecom', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("UPDATE commande SET status = 1 WHERE id_commande = ?");
        $stmt->execute([$order_id]);

        // verifier si la maj a bien ete effectuee
        if($stmt->rowCount() > 0){
            echo "Votre commande a ete validee avec succes.";
        } else {
            echo "Erreur lors de la validation de la commande.";
        }
        // redirection vers l'historique des commandes
        header("Location: order_history.php");
        exit();
    }
    catch (PDOException $e) {
        echo "Erreur lors de la maj du statut : " . $e->getMessage();
        exit();
    }
}
    else {
        echo "Erreur : id de commande manquant.";
    }
?>


     