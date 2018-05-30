<?php

require('init.inc.php');

if(isset($_GET['q']) && $_GET['q'] !== ""){
    echo $_GET['q'];
    $searchImg = $pdo->prepare('SELECT id_image,name FROM images WHERE name LIKE :request');
    $searchImg->execute([
        ':request' => "%" . htmlspecialchars($_GET['q']) . "%"
    ]);
    $showResearch = $searchImg->fetch();
    debug($showResearch);

}
else{
    echo 'appel AJAX vide';
}
