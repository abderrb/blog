<?php

// On se connecte
require_once('../inc/connect.php');

// On vérifie que POST n'est pas vide
if (!empty($_POST)){

    // On vérifie que tous les champs obligatoires sont remplis
    if (isset($_POST['nom']) &&!empty($_POST['nom'])){

    // On récupère et on nettoie les données
    $nom = strip_tags($_POST['nom']);

    // On écrit la requête
    $sql = 'INSERT INTO `categories` (`name`) VALUES (:nom);';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On injecte les valeurs dans les paramètres
    $query->bindValue(':nom', $nom, PDO::PARAM_STR);

    // On exécute
    $query->execute();


    }else{
        $erreur = "Formulaire incomplet";
    }
}
// On écrit la requête
$sql = "SELECT * FROM `categories`";

// On exécute la requête
$query = $db->query($sql);

// On récupère les données
$categories= $query->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <h1>liste des catégories</h1>
</head>
<body>
<?php
    // On verifie que nom existe dans post et qu'il n'est pas vide

    ?>
    <table>
        <tbody>
            <thead>
                <tr>
                    <th>id</th>
                    <th>nom</th>
                    <th>Action</th>
                </tr>
            </thead>
        </tbody>
            <table>
                <tbody>
                <? foreach($categories as $categories) :?>
                    <tr>
                        <td><?= $categories['id'] ?></td>
                        <td><?= $categories['name'] ?></td>  
                        <td><a href="modifier.php?id=<?= $categorie['id'] ?>">Modifier</a></td>
                        <td><a href="supprimer.php?id=<?= $categorie['id'] ?>">Supprimer</a></td>
                        </td>
                        <? endforeach;?>
                </tbody>
            </table>
        </body>
        </html>
        
        </tbody>
    </table>
    <h1>Ajouter une catégorie</h1>
    <form method="post">
        <div>
            <label for="nom">Nom </label>
            <input type="text" id="nom" name="nom">
        </div>
        <button>ajouter</button>
        <a href="../cat/inscription.php"> <button>s'inscrire</button></a>
    </form>
</body>
</html>