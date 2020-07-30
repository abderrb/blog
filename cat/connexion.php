<?php
require_once('../inc/connect.php');

if (!empty($_POST)){

    if (
        isset($_POST['email']) && !empty($_POST['email'])
        && isset($_POST['password']) && !empty($_POST['pass'])
    ){      
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        die('email invalide');
        header('Location: inscription.php');
        }else{
            $email = $_POST['email'];
    }
  
    $sql = 'SELECT * FROM `users` WHERE `email` =:email';

    $query = $db->prepare($sql);

    $query->bindValue(':email', $nom, PDO::PARAM_STR);

    $query->execute();

    $user =$query->fetch(PDO::FETCH_ASSOC);

    if(!$user){
        die('email et/ou mot de passe incorrecte');
    }

    if(!password_verify($_POST['bonjour'], $user['password'])){
        echo 'Vous êtes connecté';
       }else{
           echo 'email et/ou mot de passe incorrecte';
       }
       }else{
           $erreur = 'Formulaire incomplet';
       }

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>blog</title>
    <h1>connexion</h1>
</head>
<body>
<form method="post">
        <div>
            <label for="email">email </label>
            <input type="text" id="nom" name="nom">
        </div>
        <div>
            <label for="password"> Mot de passe </label>
            <input type="password" id="password" name="password">
        </div>
        <button>me connecter</button>
    </form>
</body>
</html>