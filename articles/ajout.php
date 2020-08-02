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
    <form method="post">
        <div>
            <label for="titre">Titre : </label>
            <input type="text" name="titre" id="titre">
        </div>
        <div>
            <label for="contenu">Contenu : </label>
            <textarea name="contenu" id="contenu"></textarea>
        </div>
        <div>
            <label for="categorie">Catégorie : </label>



<select name="categorie" id="categorie" required>
    <option value="">-- Choisir une catégorie --</option>
    <?php foreach($categories as $categorie): ?>
        <option value="<?= $categorie['id'] ?>">
            <?= $categorie['name'] ?>
        </option>
    <?php endforeach; ?>
</select>


</div>
        <button>Ajouter l'article</button>
    </form>
</body>
</html>