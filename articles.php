<?php
require_once 'inc/header.php';
if (!isset($_SESSION['user'])) {
    header('Location: connexion.php');
    exit;
}
// Transforme une chaine de caractères "json" en tableau PHP
//$roles = json_decode($_SESSION['user']['roles']);

// On vérfie si on a le rôle admin dans $roles
if(!in_array('ROLE_ADMIN', $roles)){
    header('Location: '.URL);
    exit;
}
// On se connecte
require_once 'inc/connect.php';

$sql = 'SELECT * FROM `categories` ORDER BY `name` ASC;';


$query = $db->query($sql);

$categories = $query->fetchAll(PDO::FETCH_ASSOC);


if (!empty($_POST)) {

    // On vérifie que tous les champs obligatoires sont remplis
    if (
        isset($_POST['title']) && !empty($_POST['title'])
        && isset($_POST['content']) && !empty($_POST['content'])
        && isset($_POST['categorie']) && !empty($_POST['categorie'])
    ) {
        $titre = strip_tags($_POST['title']);
        $contenu = htmlspecialchars($_POST['content']);



        $sql = 'INSERT INTO`articles`(`title`,`content`, `users_id`,`categories_id`) VALUES (":titre",":contenu",:user,:categorie);';

        $query = $db->prepare($sql);

        $query->bindValue(':titre', $titre, PDO::PARAM_STR);
        $query->bindValue(':contenu', $contenu, PDO::PARAM_STR);
        $query->bindValue(':user', $_SESSION['user']['id'], PDO::PARAM_INT);
        $query->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_INT);

        $query->execute();

        header('Location: connexion.php');
    } else {
        $erreur = "Formulaire incomplet";
    }
}

?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>articles</title>
</head>

<body>
    <h1>Articles</h1>
    <h2>Ajouter votre article</h2>
    <form method="post">
        <div>
            <label for="title">titre </label>
            <input type="text" name="title" id="title">
        </div>
        <div>
            <label for="content">ajouter le contenu</label>

            <textarea id="content" name="content"></textarea>
        </div>
        <div>
            <label for="categorie">Catégories:</label>

            <select name="categorie" id="categorie" required>
                <option value=" ">--Choisir une catégorie--</option>
                <?php foreach ($categories as $categorie) : ?>
                    <option value="<?= $categorie['id'] ?>">
                        <?= $categorie['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button>Envoyer l'article</button>
    </form>
</body>

</html>