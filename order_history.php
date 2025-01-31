<?php
session_start();

include 'config.php';

// Définir les détails de connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=ecom';
$username = 'root';
$password = '';

// Assurez-vous que la connexion à la base de données est établie
try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    exit;
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom'])){
   echo "Utilisateur non connecté.";
    exit;
}

//recuperer le nom d'utilisateur de la session
$username = $_SESSION['nom'];

//connexion a la base de donnees et recuperer l'id de l'utilisateur
try{
    //recuperer l'id de l'utilisateur basé sur le nom d'utilisateur
    $stmt = $pdo->prepare("SELECT id_user FROM utilisateur WHERE Nom = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$user){
        echo "Utilisateur introuvable.";
        exit;
    }

    $user_id = $user['id_user'];

    //recuperer les commandes payées (status = 1)
    $stmt = $pdo->prepare("
    SELECT c.id_commande AS id_commande, c.prix_total, c.date_commande,c.status, p.nom AS product_name, p.image AS product_image
    FROM commande c
    JOIN detail_commande ci ON c.id_commande = ci.id_commande
    JOIN modele p ON ci.id_modele = p.id_modele
    WHERE c.id_user = ? AND c.status = 1
    ORDER BY c.date_commande DESC
    ");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des commandes</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <h1>Historique des commandes</h1>
    <?php if(!empty($orders)): ?>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Nom du produit</th>
                    <th>Prix total</th>
                    <th>Date de commande</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $order): ?>
                    <tr>

                        <td><img src="images/<?php echo htmlspecialchars( $order['image']); ?>" alt="<?php echo htmlspecialchars( $order['name']);?>" width="100"></td>
                        <td><?php echo htmlspecialchars( $order['nom']); ?></td>
                        <td><?php echo htmlspecialchars( $order['prix_total'],2,',',''); ?>€</td>
                        <td><?php echo htmlspecialchars( $order['date_commande']); ?></td>
                        <td>
                            <?php
                            switch ($order['status']) {
                                case 1:
                                    echo 'Payée';
                                    break;
                                case 2:
                                    echo 'Annulée';
                                    break;
                                default:
                                    echo 'En attente';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Vous n'avez aucune commande payée.</p>
    <?php endif; ?>
    <a href="index.php">Retourner au catalogue</a>
    
</body>
</html>