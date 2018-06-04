<?php
require('request.inc.php');

global $pdo;

if(isset($_GET['q']) && $_GET['q'] !== ""){
    echo $_GET['q'];
    $values = "";
    $searchImg = $pdo->prepare('SELECT id_image,name FROM images WHERE name LIKE :request');
    $searchImg->execute([
        ':request' => "%" . htmlspecialchars($_GET['q']) . "%"
    ]);

    if($searchImg->rowCount() != 0){ // Vérif mail existant
      while($showResearch = $searchImg->fetch()){
        echo '<option label="'.$showResearch['id_image'].'">'.$showResearch['name'].'</option>';
      }

    } // rowcount
} // if get

if(isset($_GET['modif']) && $_GET['modif'] !== ""){
  $getInfo = $pdo->prepare('SELECT id_image,name,author FROM images WHERE id_image = :id');
  $getInfo->execute([
      ':id' => intval($_GET['modif'])
  ]);
  if($getInfo->rowCount() == 1){
    while($info = $getInfo->fetch()){
      echo "<h2>Modification de ".$info['name']."</h2>";
      echo '<form method="post">';
      echo '<input type="hidden" name="id_image" value="'.$info['id_image'].'">';
      echo '<label for="name">Nom de l\'image</label>';
      echo '<input type="text" placeholder="'.$info['name'].'" name="name" id="name">';
      echo '<label for="author">Auteur de l\'image</label>';
      echo '<input type="text" placeholder="'.$info['author'].'" name="author" id="author">';
      echo '<input type="submit" name="modifImg" value="Enregistrer">';
      echo '</form>';
    } // while
  } // rowcount
} // if get

if(isset($_GET['suppr'])){
  $getPath = $pdo->prepare('SELECT img_path FROM images WHERE id_image = :id');
  $getPath->execute([
      ':id' => intval($_GET['suppr'])
  ]);
  if($getPath->rowCount() != 0){
    $path = $getPath->fetch();
    $path = $path['img_path'];
    

    unlink('../'.$path);


    $supprImg = $pdo->prepare('DELETE FROM images WHERE id_image = :id');
    $supprImg->execute([
        ':id' => intval($_GET['suppr'])
    ]);
  }
  else
  {
    echo "le fichier n'existe pas !";
  }
}

///// SUPRIMER ARTICLE //////
if(isset($_GET['supprArticle'])){

    $supprImg = $pdo->prepare('DELETE FROM articles WHERE id_article = :id');
    $supprImg->execute([
        ':id' => intval($_GET['supprArticle'])
    ]);
    echo "l'article : " . $_GET['supprArticle'] . " à bien été supprimé.";
}

///// SUPRIMER USER //////
if(isset($_GET['supprUser'])){
    if($_GET['supprUser'] != $_SESSION['user']['username'] && $_GET['supprUser'] != 0){
      $supprImg = $pdo->prepare('DELETE FROM user WHERE id_user = :id');
      $supprImg->execute([
          ':id' => intval($_GET['supprUser'])
      ]);
      echo "l'utilisateur : " . $_GET['supprUser'] . " à bien été supprimé.";
    }
    echo "Cet utilisateur n'existe pas ou ne peut pas être supprimé";
}
