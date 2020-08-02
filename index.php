<?php
require_once 'inc/header.php';

require_once 'inc/connect.php';

$sql = 'SELECT a.*, u.`nickname`, c.* FROM `articles` a LEFT JOIN `users` u ON u.id = a.users_id LEFT JOIN `categories` c ON a.categories_id = c.id GROUP BY a.id ';

$query = $db->query($sql);

$articles = $query->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php foreach($articles as $article): ?>
        <h1><?= $article['title'] ?></h1>
        <p>Article écrit par <?= $article['nickname'] ?> dans la catégorie <?= $article['name'] ?> le <?= date('d/m/Y à H:i:s', strtotime($article['created_at'])) ?></p>
        <div><?= substr(strip_tags(htmlspecialchars_decode($article['content'])),0, 300) ?>...</div>

    <?php endforeach; ?>
</body>
</html>