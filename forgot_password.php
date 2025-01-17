<?php //30:29 video prof
require 'config.php'; // Inclure le fichier de configuration pour les informations de connexion à la base de données

// définir le fuseau horaire sur Paris
date_default_timezone_set('Europe/Paris');

$message = ''; // Initialiser le message à afficher à l'utilisateur

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $token = bin2hex(random_bytes(50)); // Générer un token unique

    try {
        // Connexion à la base de données avec PDO
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Suppression des tokens expirés
        $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE reset_token_expiry < NOW()");
        $stmt->execute();

        // Vérifier si l'email existe dans la table utilisateur
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email= :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Générer un token unique pour la réinitialisation du mot de passe
            $token = bin2hex(random_bytes(32));

            // Définir une expiration de 15 minutes pour ce token
            $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            // Enregistrer le token dans la table utilisateur avec l'email, l'expiration et la date de création
            $stmt = $pdo->prepare("UPDATE utilisateur SET reset_token=:token, reset_token_expiry=:expiry WHERE email=:email");
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':expiry', $expiry);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Créer un lien de réinitialisation basé sur le domaine actuel
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/reset_password.php?token=$token";
            // Préparer le sujet de l'email avec un encodage UTF-8 pour les caractères spéciaux
            $subject = "=?UTF-8?B?" . base64_encode("Réinitialisation de mot de passe") . "?=";
            // Préparer le contenu de l'email en HTML
            $message = "
            <html>
            <head>
                <title>Réinitialisation de mot de passe</title>
            </head>
            <body>
                <p>Bonjour,</p>
                <p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
                <p><a href='$resetLink' style='color: blue; text-decoration: underline;'>Réinitialiser mon mot de passe</a></p>
                <p>Ce lien expirera dans 15 minutes.</p>
                <p>Si vous n'avez pas demandé de réinitialisation de mot de passe, vous pouvez ignorer cet email.</p>
            </body>
            </html>";

            // Configurer les en-têtes de l'email pour supporter HTML et UTF-8
            $headers = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "Content-Transfer-Encoding: 8bit\r\n";

            // Envoyer l'email et afficher un message approprié
            if (mail($email, $subject, $message, $headers)) {
                $message = "<p style='color: green;'>Un lien de réinitialisation a été envoyé à votre adresse e-mail</p>";
            } else {
                $message = "<p style='color: red;'>Une erreur s'est produite lors de l'envoi de l'email.</p>";
            }
        } else {
            $message = "<p style='color: red;'>Aucun compte trouvé avec cet email.</p>";
        }
    } catch (PDOException $e) {
        $message = "Erreur : " . $e->getMessage();
    } finally {
        // Fermer la connexion PDO
        $pdo = null;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe Oublié</title>
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

    .form-container form input[type="submit"],
    .form-container form button {
        background-color: green;
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
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
            <h1>Mot de passe Oublié</h1>
            <form action="forgot_password.php" method="POST">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
                <?php echo $message; ?>
                <button type="submit">Envoyer</button>
            </form>
        </div>
    </div>  
</body>
</html>