<?php
require_once 'inc/header.php';

require_once 'inc/connect.php';

require_once 'inc/functions.php';

$sql = 'SELECT a.*, u.`nickname`, c.* FROM `articles` a LEFT JOIN `users` u ON u.id = a.users_id LEFT JOIN `categories` c ON a.categories_id = c.id GROUP BY a.id ORDER BY a. `created_at` desc;' ;

$query = $db->query($sql);

$articles = $query->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php foreach($articles as $article): ?>
        <h1><a href="articles/detail.php?id=<?= $article['id'] ?>"><?= $article['title']?></a></h1>
        <?php if (!is_null($article['featured_image'])):
            // On fabrique le nom de l'image
            $nomImage = pathinfo($article['featured_image'], PATHINFO_FILENAME);
            $extension = pathinfo($article['featured_image'], PATHINFO_EXTENSION);

            $miniature = "$nomImage-300x300.$extension";

            
            ?>
            <P><img src="<?= URL . '/uploads/' .$miniature?>" alt="<?= $article['title']?>"> </p>
        <?php endif; ?>
        <p>Article écrit par <?= $article['nickname'] ?> dans la catégorie <?= $article['name'] ?> le <?= formatDate($article['created_at']) ?></p>
        <p><?= extrait($article['content'], 171) ?></p>

    <?php endforeach; ?>
</body>
</html>