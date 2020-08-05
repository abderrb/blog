<?php
require_once '../inc/header.php';
if (!isset($_SESSION['user'])) {
    header('Location: ' . URL . '/connexion.php');
    exit;
}
// Transforme une chaine de caractères "json" en tableau PHP
$roles = json_decode($_SESSION['user']['roles']);

// On vérifie si on a le rôle admin dans $roles
if (!in_array('ROLE_ADMIN', $roles)) {
    header('Location: ' . URL);
    exit;
}

require_once '../inc/connect.php';

$sql = 'SELECT * FROM `categories` ORDER BY `name` ASC;';

$query = $db->query($sql);

$categories = $query->fetchAll(PDO::FETCH_ASSOC);

// On vérifie que POST n'est pas vide
if (!empty($_POST)) {
    $_SESSION['form'] = $_POST;

    // On vérifie que tous les champs obligatoires sont remplis
    if (
        isset($_POST['titre']) && !empty($_POST['titre'])
        && isset($_POST['contenu']) && !empty($_POST['contenu'])
        && isset($_POST['categorie']) && !empty($_POST['categorie'])
    ) {
        // On récupère et on nettoie les données
        $titre = strip_tags($_POST['titre']);
        $contenu = htmlspecialchars($_POST['contenu']);
        $image = null;
        // On récupère et on stocke l'image si elle existe
        if (
            isset($_FILES['image']) && !empty($_FILES['image'])
            && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE
        ) {
            // On vérifie qu'on a pas d'erreur
            if ($_FILES['image']['error'] != UPLOAD_ERR_OK) {
                // On ajoute un message de session
                $_SESSION['message'][] = "Une erreur est survenue lors du transfert du fichier";

                foreach ($_SESSION['message'] as $message) {
                    echo "<p>$message</p>";
                }
            }

            // On génère un nouveau nom de fichier
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $nomImage = md5(uniqid()) . '.' . $extension;

            $extensions = ['png', 'jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp'];
            $types = ['image/png', 'image/jpeg'];

            // On vérifie si l'extension et le type sont absents des tableaux
            if (
                !in_array(strtolower($extension), $extensions)
                || !in_array($_FILES['image'], ['type'], $types)
            ) {
                $_SESSION['message'][] = "le type du fichier n'est pas valide (PNG ou JPG uniquement)";
            }

            $tailleMax = 1048576;  //1Mo = 1024*1024

            // On vérifie si la taille dépasse le maximum
            if ($_FILES['image']['size'] > $tailleMax) {
                $_SESSION['message'][] = "l'image est trop volumiseuse (1Mo maximum)";
            }

            if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
                // Si au moins 1 erreur, on redirige vers le formulaire
                header('Location: ajout.php');
                exit;
            }


            // On transfère le fichier
            if (!move_uploaded_file(
                $_FILES['image']['tmp_name'],
                __DIR__ . '/../uploads/' . $nomImage
            )) {
                // Transfert echoué
                header('Location: ajout.php');
                exit;
            }
            mini(__DIR__ . '/../uploads/' . $nomImage, 200);
            mini(__DIR__ . '/../uploads/' . $nomImage, 300);
        }

        // On écrit la requête
        $sql = 'INSERT INTO `articles`(`title`,`content`,`users_id`,`categories_id`,`featured_image`) VALUES (:titre, :contenu, :user, :categorie, :image);';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans les paramètres
        $query->bindValue(':titre', $titre, PDO::PARAM_STR);
        $query->bindValue(':contenu', $contenu, PDO::PARAM_STR);
        $query->bindValue(':user', $_SESSION['user']['id'], PDO::PARAM_INT);
        $query->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_INT);
        $query->bindValue(':image', $nomImage, PDO::PARAM_STR);

        // On exécute la requête
        $query->execute();


        header('Location: ' . URL);
        exit;
    } else {
        $_SESSION['message'][] = "le formulaire est incomplet";
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
    <?php
    if (isset($_SESSION['message']) && !empty($_SESSION['message'])) :
        foreach ($_SESSION['message'] as $message) :
    ?>
            <p><?= $message ?></p>
    <?php
        endforeach;
        unset($_SESSION['message']);
    endif;
    ?>
    <form method="post" enctype="multipart/form-data">
        <div>
            <label for="titre">Titre : </label>
            <input type="text" name="titre" id="titre" value="<?=isset($_SESSION['form']['titre'])
            ? $_SESSION['form']['titre']:""?>">
        </div>
        <div>
            <label for="contenu">Contenu : </label>
            <textarea name="contenu" id="contenu"><?= isset ($_SESSION['form']['contenu'])
            ? $_SESSION['form']['contenu']:""?></textarea>
        </div>

        <select name="categorie" id="categorie" required>
            <option value="">-- Choisir une catégorie --</option>
            <?php foreach ($categories as $categorie) : ?>
                <option value="<?= $categorie['id'] ?>"<?php
                if(
                    isset($_SESSION['form']['categorie'])
                    && $_SESSION['form']['categorie'] == $categorie['id']
                ){
                    echo "selected";
                }
            ?>>
                    <?= $categorie['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div>
            <label for="image">Ajouter une photo:</label>
            <input type="file" id="image" name="image" accept="image/png, image/jpeg">
        </div> 

        </div>
        <button>Ajouter l'article</button>
    </form>
    <?php unset ($_SESSION['form']); ?>
</body>

</html>