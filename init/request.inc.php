<?php
require('init.inc.php');
$error = "";

/*************************************************
*
* GESTION UTILISATEUR
*
*************************************************/

//////////// CONNEXION ////////////////
function connectUser(){

  global $pdo;
  $getData = $pdo->prepare("SELECT id_user,role,username,email,password FROM user WHERE email = :email");
  $getData->bindparam(':email', $_POST['email']);
  $getData->execute();

  if($getData->rowCount() != 0) // Vérif mail existant
  {

    $userInfo = $getData->fetch();
    $verifPass = password_verify($_POST['mdp'],$userInfo['password']);

    if($verifPass == 1) // Vérif password
    {
      $_SESSION['user']['id'] = $userInfo['id_user'];
      $_SESSION['user']['role'] = $userInfo['role'];
      $_SESSION['user']['username'] = $userInfo['username'];
      $_SESSION['user']['email'] = $userInfo['email'];
      header("Refresh:0; url=".URL."?p=accueil");
    }
    else{
      echo '<script>alert("Mauvais mot de passe")</script>';
    }// verif password
  } // if rowcount
  else{
    echo '<script>alert("Email inexistant")</script>';
  }
} // connectUser

//////////// Afficher ////////////////
function displayUsers(){

  global $pdo;
  $userDisplay = null;
  $getUser = $pdo->query('SELECT id_user AS id, username, email, role FROM user ORDER BY id_user ASC');
  $nbrCol = $getUser->columnCount();
  $userDisplay .= '<tr>';

  for($i = 0; $i < $nbrCol; $i++)
  {

      $nomCol = $getUser->getColumnMeta($i); //A chaque tour de boucle je récupère les intitulés de mes champs
      $userDisplay .= '<th class="cols-meta">' . ucfirst($nomCol['name']) . '</th>';

  }

  $userDisplay .= '<th class="cols-meta">Suppr.</th></tr>';

  while($row = $getUser->fetch())
  {
    $userDisplay .= '<tr><td>'.$row['id'].'</td>';
    $userDisplay .= '<td>'.$row['username'].'</td>';
    $userDisplay .= '<td>'.$row['email'].'</td>';
    $userDisplay .= '<td>'.$row['role'].'</td>';
    if($row['username'] != $_SESSION['user']['username']){
      $userDisplay .= '<td><span class="del far fa-trash-alt" data-id="'.$row['id'].'"></span></td>';
    }
    else{
      $userDisplay .= '<td></td>';
    }
  }
  return $userDisplay;
} // displayUsers

//////////// Ajout ////////////////
function addUser(){

  global $pdo;
  $username 	= (isset($_POST['pseudo'])) ? $_POST['pseudo'] : null;
  $email 	    = (isset($_POST['email'])) ? $_POST['email'] : null;
  $role 	    = (isset($_POST['role'])) ? $_POST['role'] : null;

  $password   = (isset($_POST['mdp'])) ? password_hash($_POST['mdp'],PASSWORD_DEFAULT) : null;

  $ajoutmbr = $pdo->prepare('INSERT INTO user(role,username,password,email,token)
  VALUES(
    :role,
    :username,
    :password,
    :email,
    :token
  )');

  $ajoutmbr->execute([
    ':role'     => htmlspecialchars($role),
    ':username' => htmlspecialchars($username),
    ':password' => htmlspecialchars($password),
    ':email'    => htmlspecialchars($email),
    ':token'    => token()
  ]);

}

/*************************************************
*
* GESTION IMAGE
*
*************************************************/


function displayImg(){

  global $pdo;
  
  $imgDisplay = null;
  $getImg = $pdo->query('SELECT id_image AS id, name AS nom, author AS auteur, img_path AS chemin FROM images');
  $nbrCol = $getImg->columnCount();
  $imgDisplay .= '<tr>';

  for($i = 0; $i < $nbrCol; $i++)
  {

      $nomCol = $getImg->getColumnMeta($i); //A chaque tour de boucle je récupère les intitulés de mes champs
      $imgDisplay .= '<th class="cols-meta">' . ucfirst($nomCol['name']) . '</th>';

  }

  $imgDisplay .= '<th class="cols-meta">Voir</th>';
  $imgDisplay .= '<th class="cols-meta">Modif.</th>';
  $imgDisplay .= '<th class="cols-meta">suppr.</th>';
  $imgDisplay .= '</tr>';

  while($row = $getImg->fetch())
  {
    $imgDisplay .= '<tr><td>'.$row['id'].'</td>';
    $imgDisplay .= '<td>'.$row['nom'].'</td>';
    $imgDisplay .= '<td>'.$row['auteur'].'</td>';
    $imgDisplay .= '<td>'.URL.$row['chemin'].'</td>';
    $imgDisplay .= '<td><span class="see far fa-eye" data-path="'.$row['chemin'].'"></span></td>';
    $imgDisplay .= '<td><span class="edit far fa-edit" data-id="'.$row['id'].'"></span></td>';
    $imgDisplay .= '<td><span class="del far fa-trash-alt" data-id="'.$row['id'].'"></span></td></tr>';
  }
  return $imgDisplay;
}

function addImg(){ // Fonction pour ajouter les Images en base
  global $pdo;

  $post = $_POST;

  // Caratére de separation pour les chemins de l'OS utilisé
  $slash = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? '\\' : '/';

  foreach($_FILES as $filesInfo)
  {
    //Récuperation
    $fileName = $filesInfo['name'];
    $fileType = $filesInfo['type'];
    $fileTmp  = $filesInfo['tmp_name'];

    $folder = 'affiches'; // preparation pour l'ajout de dossier  mkdir()
    $name   = array_shift($post);
    $author = array_shift($post);

    $fileName = explode(".",$fileName);
    $fileName = $fileName[0];
    $fileType = explode("/",$fileType);
    $fileType = $fileType[1];


    $destinationPathCopy = 'files' . $slash . $fileName . '.' .  $fileType;

    if($filesInfo['error'] == 0){
      if(file_exists($destinationPathCopy))
      {
        $incr = 1;
        $destinationPathCopy = 'files' . $slash . $fileName . $incr . '.' .  $fileType;
        while(file_exists($destinationPathCopy))
        {
          $incr++;
          $destinationPathCopy = 'files' . $slash . $fileName . $incr . '.' .  $fileType;
        }
      }
      if(copy($fileTmp,$destinationPathCopy)){
        $addImg = $pdo->prepare('INSERT INTO images(img_path,folder,name,author) VALUES(
          :img_path,
          :folder,
          :name,
          :author
        )');
        $addImg->execute([
          ':img_path' => htmlspecialchars($destinationPathCopy),
          ':folder'   => htmlspecialchars($folder),
          ':name'     => htmlspecialchars($name),
          ':author'   => htmlspecialchars($author)
        ]);
        echo $destinationPathCopy;
      }
    } // if error
  } // foreach $_FILES
} // addImg()

function modifImg(){
  global $pdo;

  $name = (!empty($_POST['name'])) ? $_POST['name'] : null;
  $author = (!empty($_POST['author'])) ? $_POST['author'] : null;
  $id = (!empty($_POST['id_image'])) ? $_POST['id_image'] : null;

  $modifImg = $pdo->prepare('UPDATE images SET name = :name, author = :author WHERE id_image = :id');
  $modifImg->execute([
    ':name' => htmlspecialchars($name),
    ':author' => htmlspecialchars($author),
    ':id' => intval($id)
  ]);
  return 'La modifs est bien faite';
}


/*************************************************
*                                                *
* GESTION ARTICLE                                *
*                                                *
*************************************************/
function displayArticle(){
  global $pdo;
  $articleDisplay = "";
  $getArticle = $pdo->query('SELECT
    id_article AS id,
    type, title AS titre,
    name AS image,
    publie AS publié
    FROM articles
    INNER JOIN images ON articles.img_article = images.id_image
  ');

  $nbrCol = $getArticle->columnCount();
  $articleDisplay .= '<tr>';

  for($i = 0; $i < $nbrCol; $i++)
  {
    $nomCol = $getArticle->getColumnMeta($i); //A chaque tour de boucle je récupère les intitulés de mes champs
    $articleDisplay .= '<th class="cols-meta">' . ucfirst($nomCol['name']) . '</th>';
  }

  $articleDisplay .= '<th class="cols-meta">Voir</th>';
  $articleDisplay .= '<th class="cols-meta">Modif.</th>';
  $articleDisplay .= '<th class="cols-meta">suppr.</th>';
  $articleDisplay .= '</tr>';

  while($row = $getArticle->fetch())
  {
    $row['type'] = ($row['type'] == 0) ? "Actualités" : "Spectacles";
    $row['publié'] = ($row['publié'] == 0) ? "Non" : "Oui";
    $articleDisplay .= '<tr><td>'.$row['id'].'</td>';
    $articleDisplay .= '<td>'.$row['type'].'</td>';
    $articleDisplay .= '<td>'.$row['titre'].'</td>';
    $articleDisplay .= '<td>'.$row['image'].'</td>';
    $articleDisplay .= '<td>'.$row['publié'].'</td>';
    $articleDisplay .= '<td><a class="see far fa-eye" href="'.URL.'?p=articles&c='.$row['id'].'" target="_blank"></a></td>';
    $articleDisplay .= '<td><a href="?p=bullant/content/modify&get='.$row['id'].'" class="edit far fa-edit"></a></td>';
    $articleDisplay .= '<td><span class="del far fa-trash-alt" data-id="'.$row['id'].'"></span></td></tr>';
  }

  return $articleDisplay;

} // displayArticle

function addContent(int $update = 0){

  global $pdo;
  ///// Recuperation des infos du $_POST
  $userid      = (isset($_SESSION['user'])) ? $_SESSION['user']['id'] : 0;
  $type        = (isset($_POST['type'])) ? $_POST['type'] : null;
  $publish     = (isset($_POST['publie'])) ? $_POST['publie'] : null;
  $title       = (isset($_POST['title'])) ? $_POST['title'] : null;
  $imgArticle  = (isset($_POST['image'])) ? $_POST['image'] : null;
  $chapo       = (isset($_POST['chapo'])) ? $_POST['chapo'] : null;
  $corpsText   = (isset($_POST['content'])) ? $_POST['content'] : null;
  $urlResa     = (isset($_POST['resa'])) ? $_POST['resa'] : null;
  $publie      = ($_POST['publie'] != 1) ? 0 : 1;

  // recupéré img path
  // echo $imgArticle;

  $getImgPath = $pdo->prepare('SELECT
    id_image
    FROM images
    WHERE name = :imgArticle
  ');
  $getImgPath->execute([':imgArticle' => htmlspecialchars($imgArticle)]);

  if($getImgPath->rowCount() !== 0){
    while($imgPath = $getImgPath->fetch()){
      $imgArticle = $imgPath['id_image'];
    }
  }
  else{
    echo 'la demande n\'existe pas';
  }
  if($update == 0){
    $addContent = $pdo->prepare('INSERT INTO
    articles(user_id,type,title,img_article,chapo,corps_text,url_resa,publie)
    VALUES(
      :userid,
      :type,
      :title,
      :image,
      :chapo,
      :corpsText,
      :url_resa,
      :publie
    )');
  }
  else{ //UPDATE ARTCICLE
    $addContent = $pdo->prepare('UPDATE articles
      SET user_id = :userid,
          type = :type,
          title = :title,
          img_article = :image,
          chapo = :chapo,
          corps_text = :corpsText,
          url_resa = :url_resa,
          publie = :publie
      WHERE id_article = '.$_GET['get'].'
      ');
  }
  $addContent->execute([
    ':userid'     => htmlspecialchars($userid),
    ':type'       => intval($type),
    ':title'      => htmlspecialchars($title),
    ':image'      => intval($imgArticle),
    ':chapo'      => $chapo,
    ':corpsText'  => $corpsText,
    ':url_resa'   => htmlspecialchars($urlResa),
    ':publie'     => intval($publie)
  ]);
} // fin addContent


function getArticle(&$disp){ // function pour récupéré un article en base
  global $pdo;

  // recuperation d'article pour modification 
  $idArticle = (isset($_GET['get'])) ? intval($_GET['get']) : null;
  // recuperation d'article pour affichage en front 
  $idArticle = (isset($_GET['c'])) ? intval($_GET['c']) : $idArticle;

  
  // requête de recuperation
  if(is_int($idArticle)){
      
    $getArticle = $pdo->prepare('SELECT
      id_article,
      type,
      title,
      chapo,
      corps_text,
      url_resa,
      publie,
      name AS image,
      img_path AS chemin
      FROM articles
      INNER JOIN images ON articles.img_article = images.id_image
      WHERE id_article = :id
      ');
    $getArticle->execute([':id' => $idArticle]);
    $article = $getArticle->fetch();
        
    //debug($article);
    
    if(isset($_GET['c']) && $article['publie'] == 1){ // si l'id article est indiqué & si publié
        
      if($article['type'] == 0){ // spectacle
          $processPage  = '<article class="spec"><div id="left">';
          $processPage .= '<img src="'.URL.$article['chemin'].'" alt="'.$article['image'].'">';
          $processPage .= '<a href="'.$resa = (!empty($article['resa'])) ? $article['resa'] : null.'" target="_blank">Reservation</a>';
          $processPage .= '<a href="" target="_blank">Reservation</a>'; // TWITTER
          $processPage .= '<a href="" target="_blank">Reservation</a>'; // FACEBOOK
          $processPage .= '</div><div id="text">'; // FIN #LEFT DEBUT #TEXT
          $processPage .= '<h1>'.$article['title'].'</h1><hr>';
          $processPage .= '<div id="chapo">'.$article['chapo'].'</div>';
          $processPage .= $article['corps_text'];
          $processPage .= '</article>';
      }
      else{ // Actu
          $processPage  = '<article class="actu"><div id="head">';
          $processPage .= '<img src="'.URL.$article['chemin'].'" alt="'.$article['image'].'">';
          $processPage .= '<h1>'.$article['title'].'</h1><hr>';
          $processPage .= '</div><div id="text">'; // FIN #LEFT DEBUT #TEXT
          $processPage .= '<div id="chapo">'.$article['chapo'].'</div>';
          $processPage .= $article['corps_text'];
          $processPage .= '</article>';
      }
          $disp = $processPage;
    }
    else {
        header('Location: '.URL."?p=article");
    }
    if(isset($_GET['get'])){ // recup modif
        $disp = $article;
    }
    
    
    
    
  } // if id article
  else{ // vue d'ensemble contenu par type
      if($_GET['p'] == "articles/actu"){
          $type = 1;
      }
      else if($_GET['p'] == "articles/spec"){
          $type = 0;
      }
      else{
        header('Location: '.URL."?p=404");
      }
      $getArticle = $pdo->query('SELECT
        id_article,
        type,
        title,
        chapo,
        publie,
        img_path AS chemin
        FROM articles
        INNER JOIN images ON articles.img_article = images.id_image
        WHERE type = '.$type.'');
      $processPage = "";
      while($article = $getArticle->fetch()){
        $processPage .= '<div class="article-wrap">';
        $processPage .= '<div class="img-content">';                        // BLOCK IMG
        $processPage .= '<img src="'.URL."/".$article['chemin'].'"></div>'; //
        $processPage .= '<div class="preview"><h2>'.$article['title'].'</h2>';
        $processPage .= '<p>'.$article['chapo'].'</p>';
        $processPage .= '<a class="more" href="'.URL.'?p=articles&c='.$article['id_article'].'">Lire la suite...</a></div></div>';
      }
      $disp = $processPage;
  }

} // getArticle


function getCarrousel(){

}

function getFrontArticle(){
  
}
