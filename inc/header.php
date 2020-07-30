<?php
session_start();

define('URL', 'http://localhost/blog');

if(isset($_SESSION['user'])){
    echo "bonjour".$_SESSION['user']['nickname']."<a hreh='".URL."/deconnexion.php'>Déconnexion</a>";
}else{
    echo '<a href="'.URL.'/connexion.php">Connexion</a> - <a href="'.URL.'/inscription.php">Inscription<:a>';
}

?>



