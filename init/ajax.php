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
    echo "<p class='alert'>le fichier n'existe pas !<p>";
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
      echo "<p class='success'>l'utilisateur : " . $_GET['supprUser'] . " à bien été supprimé.</p>";
    }
    else{
      echo "<p class='alert'>Cet utilisateur n'existe pas ou ne peut pas être supprimé</p>";
    }
}

// REQUEST POUR GESTION LIVRE D'OR





if(isset($_GET['t'])){
    $isWait = $_GET['t'];
    $display = "";

    // Suppression du message
    if(isset($_GET['supprLivre'])){
      $supprLivre = $pdo->prepare('DELETE FROM livre WHERE id_livre = :id');
      $supprLivre->execute([
          ':id' => intval($_GET['supprLivre'])
      ]);
      $display .= '<p class="success">Le message à bien été supprimé</p>';
    }

    // Passe le message soumis à 'publié'
    if(isset($_GET['addLivre'])){
      $addLivre = $pdo->prepare('UPDATE livre SET confirm = 1 WHERE id_livre = :id');
      $addLivre->execute([
          ':id' => intval($_GET['addLivre'])
      ]);

      $display .= '<p class="success">Le message à bien été validé</p>';
    }

    // Affiche les messages non validés
    if($isWait == 0){
      $getInWait = $pdo->query('SELECT id_livre AS id,pseudo_livre AS pseudo,message_livre AS message FROM livre WHERE confirm = 0');
    }
    // affiche les messages validés
    else{
      $getInWait = $pdo->query('SELECT id_livre AS id,pseudo_livre AS pseudo,message_livre AS message FROM livre WHERE confirm = 1');
    }

    $nbrCol = $getInWait->columnCount();

    $display .= '<table><tr>';

    for($i = 0; $i < $nbrCol; $i++)
    {
        $nomCol = $getInWait->getColumnMeta($i); //A chaque tour de boucle je récupère les intitulés de mes champs
        $display .= ($nomCol['name'] == "message") ? '<th class="cols-meta t-left">' . ucfirst($nomCol['name']) . '</th>' : '<th class="cols-meta">' . ucfirst($nomCol['name']) . '</th>';

    }

    $display .= ($isWait == 0) ? '<th class="cols-meta">Valid.</th>' : null;
    $display .= '<th class="cols-meta">suppr.</th>';
    $display .= '</tr>';

    while($row = $getInWait->fetch())
    {
      $display .= '<tr><td>'.$row['id'].'</td>';
      $display .= '<td>'.$row['pseudo'].'</td>';
      $display .= '<td class="t-left">'.$row['message'].'</td>';
      $display .= ($isWait == 0) ? '<td><span class="add fas fa-check" data-id="'.$row['id'].'"></span></td>' : null;
      $display .= '<td><span class="del fas fa-times" data-id="'.$row['id'].'"></span></td></tr>';
    }
    $display .= '</table>';

    echo $display;

}
