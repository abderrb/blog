<?php
require_once '../inc/header.php';
if(!isset($_SESSION['user'])){
    header('Location: '.URL.'/connexion.php');
    exit;
}
// Transforme une chaine de caractères "json" en tableau PHP
$roles = json_decode($_SESSION['user']['roles']);

// On vérifie si on a le rôle admin dans $roles
if(!in_array('ROLE_ADMIN', $roles)){
    header('Location: '.URL);
    exit; 
}

require_once '../inc/connect.php';

$sql = 'SELECT * FROM `categories` ORDER BY `name` ASC;';

$query = $db->query($sql);

$categories = $query->fetchAll(PDO::FETCH_ASSOC);

// On vérifie que POST n'est pas vide
if(!empty($_POST)){
    // On vérifie que tous les champs obligatoires sont remplis
    if(
        isset($_POST['titre']) && !empty($_POST['titre'])
        && isset($_POST['contenu']) && !empty($_POST['contenu'])
        && isset($_POST['categorie']) && !empty($_POST['categorie'])
    ){
        // On récupère et on nettoie les données
        $titre = strip_tags($_POST['titre']);
        $contenu = htmlspecialchars($_POST['contenu']);

        // On récupère et on stocke l'image si elle existe
        if(isset($_FILES['image']) && !empty($_FILES['image'])){
            // On vérifie qu'on a pas d'erreur
            if($_FILES['image']['error'] != UPLOAD_ERR_OK){
                header('Location: ajout.php');
                exit;
            }

            // On génère un nouveau nom de fichier
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $nomImage = md5(uniqid()).'.'.$extension;

            // On transfère le fichier
            if(!move_uploaded_file($_FILES['image']['tmp_name'],
            __DIR__.'/../uploads/'.$nomImage
            )
            ){
                // Transfert echoué
                header('Location: ajout.php');
            }
            
        }

        // On écrit la requête
        $sql = 'INSERT INTO `articles`(`title`,`content`,`users_id`,`categories_id`) VALUES (:titre, :contenu, :user, :categorie);';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans les paramètres
        $query->bindValue(':titre', $titre, PDO::PARAM_STR);
        $query->bindValue(':contenu', $contenu, PDO::PARAM_STR);
        $query->bindValue(':user', $_SESSION['user']['id'], PDO::PARAM_INT);
        $query->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_INT);

        // On exécute la requête
        $query->execute();

        header('Location: '.URL);
        exit;
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
    <title>Ajouter un article</title>
</head>
<body>
    <h1>Ajouter un article</h1>
    <form method="post" enctype="multipart/form-data">
        <div>
            <label for="titre">Titre : </label>
            <input type="text" name="titre" id="titre">
        </div>
        <div>
            <label for="contenu">Contenu : </label>
            <textarea name="contenu" id="contenu"></textarea>
        </div>
        
<select name="categorie" id="categorie" required>
    <option value="">-- Choisir une catégorie --</option>
    <?php foreach($categories as $categorie): ?>
        <option value="<?= $categorie['id'] ?>">
            <?= $categorie['name'] ?>
        </option>
    <?php endforeach; ?>
</select>
        <div>
            <label for="image">Ajouter une photo:</label>
            <input type="file"
            id="image" name="image"
            accept="image/png, image/jpeg">
        </div>

</div>
        <button>Ajouter l'article</button>
    </form>
</body>
</html>