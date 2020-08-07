<?php
session_start();
require_once 'functions.php';
// On vérifie le cookie remember et on restaure la session si besoin
if(isset($_COOKIE ['remember']) && !empty($_COOKIE ['remember'])){
    // On récupère et on nettoie le token
    $token = strip_tags($_COOKIE ['remember']);

    // On se connecte à la base
    require_once 'inc/connect.php';
    
    // On écrit la requête
    $sql = "SELECT * FROM `users` WHERE `remember_token` = :token;";

    // On prépare la requête
    $query = $db->prepare($sql);

    // On injecte le token
    $query->bindValue(':token', $token, PDO::PARAM_STR);

     // On exécute la requête
     $query->execute();

     // On va chercher les données
     $user = $query->fetch(PDO::FETCH_ASSOC);

     // Si un utilisateur existe
     if($user){
         // On restaure la session
         $_SESSION['user'] = [
            'id' => $user['id'],
            'nickname' => $user['nickname'],
            'email' => $user['email'],
            'roles' => $user['roles'],
        ];
     }else{
         // Effacer un cookie
         setcookie('remember', '', 1);
     }

}

define('URL', 'http://localhost/blog');

if(isset($_SESSION['user'])){
    echo "Bonjour ".$_SESSION['user']['nickname']." 
    <a href='".URL."/deconnexion.php'>Déconnexion</a>";
}else{
    echo '<a href="'.URL.'/connexion.php">Connexion</a> - <a href="'.URL.'/inscription.php">Inscription</a>';
}