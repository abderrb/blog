<?php
    require_once 'inc/header.php';
    require_once '..inc/connect.php';
   
    $id = strip_tags($_GET['id']);
    $sql = "DELETE FROM `liste` WHERE `id`=:id;";

    $query = $db->prepare($sql);

    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();

    header('Location: index.php');
?>