<?php
require_once 'inc/header.php';

// On vérifie si on a un token dans l'URL
if (isset($_GET['token']) && !empty($_GET['token'])) {
    // On a un token
    // On récupère le token et on le nettoie
    $token = strip_tags($_GET['token']);

    // On se connecte à la base
    require_once 'inc/connect.php';

    // On vérifie que le token se trouve dans la base de donnée
    $sql = 'SELECT * FROM `users` WHERE `reset_token` = :token;';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On injecte le token
    $query->bindValue(':token', $token, PDO::PARAM_STR);

    // On exécute la requête
    $query->execute();

    // On récupère les données
    $user = $query->fetch(PDO::FETCH_ASSOC);

    // On a un utilisateur ou pas
    if (!$user) {
        // Pas d'utilisateur ayant de jeton
        $_SESSION['message'][] = "Jeton invalide";
        header('Location: ' . URL);
        exit;

        //on a un user
        // On génère les dates actuelle et expiration
        $maintenant = new DateTime();
        $expiration = new DateTime($user['expiration']);

        // On compare pour savoir si le token a expiré
        if ($expiration < $maintenant) {
            // Le token a expiré
            // On l'efface de la base
            $sql = "UPDATE `users` SET `reset_token` = null, `expiration_date` = null WHERE `id` = {$user['id']};";

            // On exécute la requête
            $query = $db->query($sql);


            $_SESSION['message'][] = "Le jeton a expiré";
            header('Location: ' . URL);
            exit;
        }
        // Tout est bon on peut traiter le formulaire
        if (!empty($_POST)) {
            // On vérifie que tous les champs obligatoires sont remplis
            if (
                isset($_POST['pass1']) && !empty($_POST['pass'])
                && isset($_POST['pass2']) && !empty($_POST['pass2'])
            ) {
                // Le formulaire est complet
                if ($_POST['pass'] === $_POST['pass2']) {
                    // Mot de passe identique
                    // On chiffre le mdp
                    $pass = password_hash($_POST['pass1'], PASSWORD_ARGON2ID);

                    // On met à jour la BBD
                    $sql = "UPDATE `users` SET `password` =  '$pass', `reset_token` = null, `expiration_date` = null WHERE `id` = {$user['id']} ";

                    // On exécute la requête
                    $query = $db->query($sql);

                    $_SESSION['message'][] = "Mot de passe modifié";
                    header('Location: ' . URL.'/connexion.php');
                } else {
                    // Mot de passe différents
                    $_SESSION['message'][] = 'Mots de passe différents';
                }
            } else {
                $_SESSION['message'][] = "le formulaire est incomplet";
            }
        } else {
            //on n' pas de token
            $_SESSION['message'][] = "jeton absent";
            header('Location: ' . URL);
            exit;
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
    <p>Veuillez entrer votre nouveau mot de passe</p>
    <?php
        if(isset($_SESSION['message']) && !empty($_SESSION['message'])):
            foreach($_SESSION['message'] as $message):
            ?>
                <p><?= $message ?></p>
            <?php
            endforeach;
            unset($_SESSION['message']);
        endif;
    ?>
    <form method="post">
        <div>
            <label for="pass">Mot de passe :</label>
            <input type="password" name="pass" id="pass">
        </div>
        <div>
            <label for="pass2">Confirmer le mot de passe :</label>
            <input type="password" name="pass2" id="pass2">
        </div>
        <button>Confirmer</button>
    </form>
</body>

</html>