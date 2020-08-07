<?php
// On importe les fichiers nécessaires

use PHPMailer\PHPMailer\Exception;

require_once 'inc/header.php';

// On vérifie si on a posté le formulaire
if(!empty($_POST)){
    // On vérifie que tous les champs obligatoires sont remplis
    if(isset($_POST['email']) && !empty($_POST['email'])){
        // On vérifie que le format de l'e-mail est valide
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $_SESSION['message'][] = 'email invalide';
            header('Location: formulaire.php');
            exit;
        }
        // On récupère l'e-mail
        $email = $_POST['email'];
        
        // On va chercher dans la base l'utilisateur possédant cette adresse e-mail
        // On se connecte à la base
        require_once 'inc/connect.php';

        // On écrit la requête
        $sql = 'SELECT * FROM `users` WHERE `email` = :email;';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs
        $query->bindValue(':email', $email, PDO::PARAM_STR);

        // On exécute la requête
        $query->execute();

        // On vérifie si on a une réponse
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if(!$user){
            $_SESSION['message'][] = "Si le compte concerné existe, vous recevrez un email de réinitialisation de mot de passe";
            header('Location: '. URL);
            exit;
        }

        // Ici un utilisateur ayant l'adresse email entrée a été trouvé
        $token = md5(uniqid());
        $expiration = date('Y-m-d H:i:s', strtotime("+1 hour"));

        // On écrit la requête
        $sql = "UPDATE `users` SET `reset_token` = '$token', `expiration_date` = '$expiration' WHERE `email` = '{$user['email']}';";

        // On exécute la requête
        $query = $db->query($sql);

        // On charge PHPMailer
        require_once 'inc/config-mail.php';

        // On crée le mail
        try{
            // On définit l'expéditeur du mail
            $sendmail->setFrom('no-reply@domaine.fr', 'Blog');
        
            // On définit le/les destinataire(s)
            $sendmail->addAddress($user['email'], $user['nickname']);
        
            // On définit le sujet du mail
            $sendmail->Subject = 'Votre demande de réinitialisation de mot de passe';
        
            // On active le HTML
            $sendmail->isHTML();
        
            // On écrit le contenu du mail
            // En HTML
            $sendmail->Body = "<h1>Réinitialisation de mot de passe</h1>
            <p>Une demande de réinitialisation de mot de passe a été effectuée sur le super blog.</p>
            <p>Si vous n'êtes pas à l'origine de cette demande veuillez ignorer ce message</p>
            <p>Dans le cas contraire, veuillez cliquer sur le lien ci-dessous. Celui-ci expirera après 1 heure.</p>
            <p><a href='".URL."/reset.php?token=$token'>".URL."/reset.php?token=$token</a></p>";
            
            // En texte brut
            $sendmail->AltBody = "Réinitialisation de mot de passe\nUne demande de réinitialisation de mot de passe a été effectuée sur le super blog.\nSi vous n'êtes pas à l'origine de cette demande veuillez ignorer ce message.\nDans le cas contraire, veuillez cliquer sur le lien ci-dessous. Celui-ci expirera après 1 heure.\n".URL."/reset.php?token=$token";
        
            // On envoie le mail
            $sendmail->send();
            $_SESSION['message'][] = "Si le compte concerné existe, vous recevrez un email de réinitialisation de mot de passe";
            header('Location: '. URL);
            exit;

        }catch(Exception $e){
            // Ici le mail n'est pas parti
            echo 'Erreur : ' . $e->errorMessage();
        }
        
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
</head>
<body>
    <h1>Demande de réinitialisation de mot de passe</h1>
    <p>Veuillez entrer votre adresse e-mail ci-dessous</p>
    <form method="post">
        <div>
            <label for="email"></label>
            <input type="email" id="email" name="email">
        </div>
        <div>
            <button>Réinitialiser mon mot de passe</button>
        </div>
    </form>
</body>
</html>