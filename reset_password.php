<?php
// Definition des constantes
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecom');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // recuperer et nettoyer les donnees du formulaire
    $token = htmlspecialchars(trim($_POST['token'])); // le nouveau token unique de réinitialisation
    $new_password = trim($_POST['new_password']); // le nouveau mot de passe
    $confirm_password = trim($_POST['confirm_password']); // la confirmation du nouveau mot de passe

    // validation des mots de passe
    if ($new_password !== $confirm_password) {
        echo "<p style='color: red;'>Les mots de passe ne correspondent pas.</p>";
        exit; // arrêter l'execution si le mdp ne correspondent pas
    }
    if (strlen($new_password) < 8) {
        echo "<p style='color: red;'>Le mot de passe doit contenir au moins 8 caractères.</p>";
        exit; // arrêter l'execution si le mdp est trop court
    }

    try {
        // Connexion à la base de données avec PDO
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Vérifier si le token est valide et n'a pas expiré
        $stmt = $pdo->prepare("SELECT email FROM utilisateur WHERE reset_token=:token AND reset_token_expiry > NOW()");
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // si le token est valide, recuperer l'adresse email associe
            $email = $stmt->fetchColumn();

            // hasher le mot de passe
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // mettre à jour le mot de passe dans la base de données
            $stmt = $pdo->prepare("UPDATE utilisateur SET mdp=:password, reset_token=NULL, reset_token_expiry=NULL WHERE email=:email");
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            echo "<p style='color: green;'>Votre mot de passe a été réinitialisé avec succès. Veuillez patienter quelques secondes le temps que vous soyez redirigée.</p>";
            // Rediriger vers la page de connexion après 3 secondes
            header("refresh:3;url=connexion.html");
        } else {
            echo "<p style='color: red;'>Le lien de réinitialisation est invalide ou a expiré.</p>";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    } finally {
        // Fermer la connexion PDO
        $pdo = null;
    }
} else if(isset($_GET['token'])) {
    // Si le token est passe dans l'URL, le recuperer et le securiser
    $token = htmlspecialchars($_GET['token']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe</title>
    <style> 
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #f4f4f4;
    }
    .form-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background-color: white;
        padding: 40px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .form-container h2 {
        margin-bottom: 20px;
    }

    .form-container form {
        width: 100%;
        max-width: 400px;
    }

    .form-container form label,
    .form-container form input {
        display: block;
        width: 100%;
        margin-bottom: 10px;
    }

    .form-container form input[type="submit"]:hover,
    .form-container form button:hover {
        background-color: #555;
    }

    .register-link {
        color: #333;
    }

    .register-link:hover {
        text-decoration: underline;
    }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="login-container">
        <div class="form-container">
        <h2>Réinitialiser le mot de passe</h2>
        <form action="reset_password.php" method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label for="new_password">Nouveau mot de passe :</label>
        <input type="password" id="new_password" name="new_password" required>
        <label for="confirm_password">Confirmer le mot de passe :</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <button type="submit">Réinitialiser</button>
        </form>
        </div>
    </div>
    
</body>
</html>