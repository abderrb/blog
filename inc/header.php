<?php
session_start();
require_once 'functions.php';

define('URL', 'http://localhost/blog');

if(isset($_SESSION['user'])){
    echo "Bonjour ".$_SESSION['user']['nickname']." 
    <a href='".URL."/deconnexion.php'>Déconnexion</a>";
}else{
    echo '<a href="'.URL.'/connexion.php">Connexion</a> - <a href="'.URL.'/inscription.php">Inscription</a>';
}