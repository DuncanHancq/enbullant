<?php
require('init/init.inc.php');
$error = "";
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
      echo 'Mauvais mdp';
    }
  }
  else{
    echo 'Email inexistant';
  }
}

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
}

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

  $imgDisplay .= '<th class="cols-meta">Voir</th><th class="cols-meta">Modif.</th></tr>';
  $imgDisplay .= '</tr>';

  while($row = $getImg->fetch())
  {
    $imgDisplay .= '<tr><td>'.$row['id'].'</td><td>'.$row['nom'].'</td><td>'.$row['auteur'].'</td><td>'.$row['chemin'].'</td><td><span class="see far fa-eye" data-path="'.$row['chemin'].'></span>"</td><td><span class="del far fa-trash-alt"></span></td>';
  }
  return $imgDisplay;
}


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

function addContent(){
  global $pdo;

  ///// Recuperation des infos du $_POST
  $userid 	   = (isset($_SESSION['user'])) ? $_SESSION['user']['id'] : 0;
  $type        = ($_POST['type'] != 0) ? 1 : 0;
  $publish     = (isset($_POST['publie'])) ? $_POST['publie'] : null;
  $title       = (isset($_POST['title'])) ? $_POST['title'] : null;
  $chapo       = (isset($_POST['chapo'])) ? $_POST['chapo'] : null;
  $corpsText   = (isset($_POST['content'])) ? $_POST['content'] : null;
  $urlResa     = (isset($_POST['resa'])) ? $_POST['resa'] : null;


  $addContent = $pdo->prepare('INSERT INTO articles(user_id,type,publish,title,chapo,corps_text,url_resa)
  VALUES(
    :userid,
    :type,
    :publish,
    :title,
    :chapo,
    :corpsText,
    :url_resa
  )');
  $addContent->execute([
    ':userid'     => htmlspecialchars($userid),
    ':type'       => htmlspecialchars($type),
    ':publish'    => htmlspecialchars($publish),
    ':title'      => htmlspecialchars($title),
    ':chapo'      => $chapo,
    ':corpsText'  => $corpsText,
    ':url_resa'   => htmlspecialchars($urlResa)
  ]);

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

    }


  }

}


function getArticle(){

}
