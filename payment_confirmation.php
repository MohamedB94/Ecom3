<?php
session_start();

include 'config.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      

        if (isset($_SESSION['nom'])){
            $username = $_SESSION['nom'];
            
            $stmt = $pdo->prepare('SELECT id_user FROM utilisateur WHERE nom = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $user_id = $user['id_user'];
            }else{
                echo "Utilisateur non trouvé.";
                exit;
            }
        }else{
            echo "utilisateur non connecté.";
            exit;
        }
        
        // verifier si la commande existe et appartient à l'utilisateur connecté
        $stmt = $pdo->prepare("SELECT * FROM commande WHERE id_commande = ? AND id_user = ?");
        $stmt->execute([$order_id, $user_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order){
            // Mettre a jour le statut de la commande payée
            $stmt = $pdo->prepare("UPDATE commande SET status = 1 WHERE id_commande = ?");
            $stmt->execute([$order_id]);

            // Verifier si la maj a bien été effectuée
            if ($stmt->rowCount() > 0){
                echo "La commande avec l'ID $order_id a été validée avec succès.";
            }else{
                echo "Erreur lors de la validation de la commande.";
            }
        } else {
            echo "Commande non trouvée ou vous n'êtes pas autorisé a la valider.";
        }
           //redirection vers l'histoirique des commandes apres la maj
        header("Location: order_history.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur lors de la mise à jour du statut : " . $e->getMessage();
        exit();
    }
}else{
    echo "Erreur : ID de commande manquant.";
    exit();
}
?>