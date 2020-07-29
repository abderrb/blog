<?php
// On se connecte à la base de données
try{
    // On essai de se connecter
    $dsn = 'mysql:dbname=blog;host=localhost';

    // DSN, Utilisateur, Mot de passe
    $db = new PDO($dsn, 'root', '');

    //echo "La connexion a fonctionné"; ligne à tester avant de commenter pour vérifier la connextion

}catch(Exception $erreur){
    // On gère l'échec du "try"
    echo "La connexion a échoué".$erreur -> getMessage();
    die;
}
