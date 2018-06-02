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
      debug($_SESSION['user']);
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
  $getUser = $pdo->query('SELECT id_user AS id, username, role FROM user');
  $nbrCol = $getUser->columnCount();
  $userDisplay .= '<tr>';

  for($i = 0; $i < $nbrCol; $i++)
  {

      $nomCol = $getUser->getColumnMeta($i); //A chaque tour de boucle je récupère les intitulés de mes champs
      $userDisplay .= '<th class="cols-meta">' . ucfirst($nomCol['name']) . '</th>';

  }

  $userDisplay .= '<th class="cols-meta">Modif.</th><th class="cols-meta">Suppr.</th></tr>';
  $userDisplay .= '</tr>';

  while($row = $getUser->fetch())
  {
    $userDisplay .= '<tr><td>'.$row['id'].'</td><td>'.$row['username'].'</td><td>'.$row['role'].'</td><td>0</td><td>X</td>';
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

  $imgDisplay .= '<th class="cols-meta">Voir</th><th class="cols-meta">Modif.</th><th class="cols-meta">suppr.</th></tr>';
  $imgDisplay .= '</tr>';

  while($row = $getImg->fetch())
  {
    $imgDisplay .= '<tr><td>'.$row['id'].'</td>';
    $imgDisplay .= '<td>'.$row['nom'].'</td>';
    $imgDisplay .= '<td>'.$row['auteur'].'</td>';
    $imgDisplay .= '<td>'.$row['chemin'].'</td>';
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


    echo $fileName . '.' .  $fileType;

    echo 'name : ' . $name;
    echo '<br><br>';
    echo 'author : ' . $author;
    echo '<br><br>';
    echo 'fileName : ' . $fileName;
    echo '<br><br>';
    echo 'fileType : ' . $fileType;
    echo '<br><br>';
    echo 'fileTmp : ' . $fileTmp;
    echo '<br>______________________<br>';
    echo $destinationPathCopy = 'files' . $slash . $fileName . '.' .  $fileType;
    echo $destinationPath = 'files' . "/" . $fileName . '.' .  $fileType;

    if($filesInfo['error'] == 0){

      copy($fileTmp,$destinationPathCopy);

      $addImg = $pdo->prepare('INSERT INTO images(img_path,folder,name,author) VALUES(
        :img_path,
        :folder,
        :name,
        :author
      )');
      $addImg->execute([
        ':img_path' => htmlspecialchars($destinationPath),
        ':folder'   => htmlspecialchars($folder),
        ':name'     => htmlspecialchars($name),
        ':author'   => htmlspecialchars($author)
      ]);

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
*
* GESTION ARTICLE
*
*************************************************/


function displayArticle(){
  global $pdo;
  $articleDisplay = "";
  $getArticle = $pdo->query('SELECT id_article AS id, type, title AS titre, name AS image, publie AS publié FROM articles INNER JOIN images ON articles.img_article = images.id_image');
  $nbrCol = $getArticle->columnCount();
  $articleDisplay .= '<tr>';

  for($i = 0; $i < $nbrCol; $i++)
  {

    $nomCol = $getArticle->getColumnMeta($i); //A chaque tour de boucle je récupère les intitulés de mes champs
    $articleDisplay .= '<th class="cols-meta">' . ucfirst($nomCol['name']) . '</th>';

  }

  $articleDisplay .= '<th class="cols-meta">Voir</th><th class="cols-meta">Modif.</th><th class="cols-meta">suppr.</th></tr>';
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
    $articleDisplay .= '<td><span class="see far fa-eye"></span></td>';
    $articleDisplay .= '<td><a href="?p=bullant/content/modify&get='.$row['id'].'" class="edit far fa-edit"></a></td>';
    $articleDisplay .= '<td><span class="del far fa-trash-alt"></span></td></tr>';
  }
  return $articleDisplay;
}

function addContent(int $update = 0){

  global $pdo;
  ///// Recuperation des infos du $_POST
  $userid 	   = (isset($_SESSION['user'])) ? $_SESSION['user']['id'] : 0;
  $type        = ($_POST['type'] != 0) ? 1 : 0;
  $publish     = (isset($_POST['publie'])) ? $_POST['publie'] : null;
  $title       = (isset($_POST['title'])) ? $_POST['title'] : null;
  $imgArticle  = (isset($_POST['image'])) ? $_POST['image'] : null;
  $chapo       = (isset($_POST['chapo'])) ? $_POST['chapo'] : null;
  $corpsText   = (isset($_POST['content'])) ? $_POST['content'] : null;
  $urlResa     = (isset($_POST['resa'])) ? $_POST['resa'] : null;
  $publie      = ($_POST['publie'] != 1) ? 0 : 1;

  // recupéré img path
  // echo $imgArticle;

  $getImgPath = $pdo->prepare('SELECT id_image FROM images WHERE name = :imgArticle');
  $getImgPath->execute([':imgArticle' => htmlspecialchars($imgArticle)]);
  if($getImgPath->rowCount() !== 0){
    while($imgPath = $getImgPath->fetch()){
      $imgArticle = $imgPath['id_image'];
    }
  }
  else{
    echo 'la demande n\'existe pas';
  }
  if($update = 0){
    $addContent = $pdo->prepare('INSERT INTO articles(user_id,type,title,img_article,chapo,corps_text,url_resa,publie)
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
  else{
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

} // fin fonction

function modifyArticle(){
  global $pdo;
  $articleDisplay = "";
  $getArticle = $pdo->query('SELECT id_article AS id, title AS titre, name AS image FROM articles INNER JOIN images ON articles.img_article = images.id_image');
  $nbrCol = $getArticle->columnCount();
  $articleDisplay .= '<tr>';

  for($i = 0; $i < $nbrCol; $i++)
  {

      $nomCol = $getArticle->getColumnMeta($i); //A chaque tour de boucle je récupère les intitulés de mes champs
      $articleDisplay .= '<th class="cols-meta">' . ucfirst($nomCol['name']) . '</th>';

  }

  $articleDisplay .= '<th class="cols-meta">Voir</th><th class="cols-meta">Modif.</th><th class="cols-meta">suppr.</th></tr>';
  $articleDisplay .= '</tr>';

  while($row = $getArticle->fetch())
  {
    $articleDisplay .= '<tr><td>'.$row['id'].'</td>';
    $articleDisplay .= '<td>'.$row['titre'].'</td>';
    $articleDisplay .= '<td>'.$row['image'].'</td>';
    $articleDisplay .= '<td><span class="see far fa-eye"></span></td>';
    $articleDisplay .= '<td><a href="?p=bullant/article/modify&get='.$row['id'].'" class="edit far fa-edit"></a></td>';
    $articleDisplay .= '<td><span class="del far fa-trash-alt"></span></td></tr>';
  }
  return $articleDisplay;
}

function getArticle(&$disp){
  global $pdo;

  $idArticle = (isset($_GET['get'])) ? intval($_GET['get']) : null;
  if($idArticle !== null){
    $getArticle = $pdo->prepare('SELECT id_article, type, title, chapo, corps_text, url_resa, publie, name AS image FROM articles INNER JOIN images ON articles.img_article = images.id_image WHERE id_article = :id');
    $getArticle->execute([':id' => $idArticle]);
    $disp = $getArticle->fetch();

  }

}

function updateArticle(){
  global $pdo;

}
