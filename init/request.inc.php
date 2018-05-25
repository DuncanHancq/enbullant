<?php
require('init/init.inc.php');

function connectUser(){
  global $pdo;
  $getData = $pdo->prepare("SELECT role,username,email,password FROM user WHERE email = :email");
  $getData->bindparam(':email', $_POST['email']);
  $getData->execute();

  if($getData->rowCount() != 0) // Vérif mail existant
  {

    $userInfo = $getData->fetch();
    $verifPass = password_verify($_POST['mdp'],$userInfo['password']);

    if($verifPass == 1) // Vérif password
    {
      $userInfo = $getData->fetch();
      $_SESSION['user']['role'] = $userInfo['role'];
      $_SESSION['user']['username'] = $userInfo['username'];
      $_SESSION['user']['email'] = $userInfo['email'];
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


  $addContent = $pdo->prepare('INSERT INTO user(user_id,type,publish,title,chapo,corps)
  VALUES(
    :user_id,
    :type,
    :publish,
    :title,
    :chapo,
    :corps_text
  )');
  $addContent = $pdo->execute([
    ':user_id'    => htmlspecialchars($userid),
    ':type'       => htmlspecialchars($type),
    ':publish'    => htmlspecialchars($publish),
    ':title'      => htmlspecialchars($title),
    ':chapo'      => htmlspecialchars($chapo),
    ':corps_text' => htmlspecialchars($corps_text)
  ]);

}

function getArticle(){

}
