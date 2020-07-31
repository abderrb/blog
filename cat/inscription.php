<?php
require_once '../inc/header.php';
require_once '../inc/connect.php';

if (!empty($_POST)) {

    // On vérifie que tous les champs obligatoires sont remplis
    if (
        isset($_POST['nom']) && !empty($_POST['nom'])
        && isset($_POST['email']) && !empty($_POST['email'])
        && isset($_POST['password']) && !empty($_POST['password'])
        && isset($_POST['password2']) && !empty($_POST['password2'])
    ) {

        // On récupère et on nettoie les données
        $nom = strip_tags($_POST['nom']);
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            die('email invalide');
            header('Location: inscription.php');
        } else {
            $email = $_POST['email'];
        }
        if ($_POST['password'] != $_POST['password2']) {
            die('Mot de passe différents');
            header('Location: inscription.php');
        } else {
            $pass = password_hash($_POST['password'], PASSWORD_ARGON2ID);
        }

        if(!empty($_SESSION['message'])){
            header('Location: inscription.php');
            exit;

        // On écrit la requête
        $sql = 'INSERT INTO `users` (`email`, `password`, `nickname`) VALUES (:email, :password, :nom);';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans les paramètres
        $query->bindValue(':email', $email, PDO::PARAM_STR);
        $query->bindValue(':password', $pass, PDO::PARAM_STR);
        $query->bindValue(':nom', $nom, PDO::PARAM_STR);

        // On exécute
        $query->execute();

        header('Location: connexion.php');
    } else {
        $erreur = "Formulaire incomplet";
    }
}
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <h1>inscription</h1>
</head>

<body>
<?php
        if(isset($_SESSION['message']) && !empty($_SESSION['message'])){
            foreach($_SESSION['message'] as $message){
                echo "<p>$message</p>";
            }
            unset($_SESSION['message']);
        }
    ?>
    <form method="post">
        <div>
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom">
        </div>
        <div>
            <label for="email">Email :</label>
            <input type="email" name="email" id="email">
        </div>
        <div>
            <label for="pass">Mot de passe :</label>
            <input type="password" name="pass" id="pass">
        </div>
        <div>
            <label for="pass2">Confirmer le mot de passe :</label>
            <input type="password" name="pass2" id="pass2">
        </div>
        <button>M'inscrire</button>
    </form>
</body>

</html>