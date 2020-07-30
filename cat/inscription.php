
<?php
require_once('../inc/connect.php');

if (!empty($_POST)){

    // On vérifie que tous les champs obligatoires sont remplis
    if (isset($_POST['nom']) &&!empty($_POST['nom'])
        &&isset($_POST['email']) &&!empty($_POST['email'])
        &&isset($_POST['password']) &&!empty($_POST['pass'])
        &&isset($_POST['password']) &&!empty($_POST['pass2'])
    ){

    // On récupère et on nettoie les données
    $nom = strip_tags($_POST['nom']);
    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        die('email invalide');
        header('Location: inscription.php');
    }else{
        $email = $_POST['email'];
    }
    if($_POST['pass'] != $_POST['pass2']){
        die('Mot de passe différents');
        header('Location: inscription.php');
    }else{
        $pass = password_hash($_POST['pass'], PASSWORD_ARGON2ID);
    }

    // On écrit la requête
    $sql = 'INSERT INTO `users` (`email`, `password`, `nickname`) VALUES (:email, :password, :nom);';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On injecte les valeurs dans les paramètres
    $query->bindValue(':email', $email, PDO::PARAM_STR);
    $query->bindValue(':password', $password, PDO::PARAM_STR);
    $query->bindValue(':nickname', $nickname, PDO::PARAM_STR);
    
    // On exécute
    $query->execute();

        header('Location: connexion.php');


    }else{
        $erreur = "Formulaire incomplet";
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
    <form method="post">
        <div>
            <label for="nom">Nom </label>
            <input type="text" id="nom" name="nom">
        </div>
        <div>
            <label for="email">e-mail </label>
            <input type="text" id="email" name="email">
        </div>
        <div>
            <label for="password"> Mot de passe </label>
            <input type="password" id="password" name="password">
        </div>
        <div>
            <label for="password"> confirmer le mot de passe </label>
            <input type="password" id="password" name="password">
        </div>
        <button>m'inscrire</button>
    </form>
</body>
</html>