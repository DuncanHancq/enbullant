<?php
require_once 'init/init.inc.php'; // Fichier d'initialisation : BSD & Debug
require_once 'class/class_template.php'; // moteur de rendu
require_once 'class/class_template_bak.php'; // moteur de rendu

function notFound($e){
  echo "404 page not found boï " . $e . '\n GET : ' . debug($_GET['p']);

  exit(0);
}

$isFound = false;
$getUrl = isset($_GET['p']) ? $_GET['p'] : null;
echo $getUrl;
/****************************************************************
---------------BACK OFFICE
****************************************************************/

if (isset($_SESSION['user']))
{
  switch ($getUrl)
  {
    case "add-user" :

      $displayTemplatePage = new TemplateBak('template/add-user.html');
      $displayTemplatePage->replaceContent('##TITLE##', 'Ajout membre');
      $displayTemplatePage->replaceContent('##H1##', 'Ajout membre');

      if(isset($_POST['ajoutmbr']))
      {
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
        $isFound = true;
      }

    break;

    default :
      continue;
    break;
  }

}


/****************************************************************
---------------FRONT-OFFICE---------------
*****************************************************************/

switch($getUrl)
{
  case null :
  case "accueil":

    $displayTemplatePage = new Template('template/index.html');
    $displayTemplatePage->replaceContent('##TITLE##', 'Accueil');
    $displayTemplatePage->replaceContent('##HEADERBO##', 'caca');

  break;

  case "bullant" :

    if(isset($_POST['connexion']) && $_POST['connexion'] == 'Connexion')
    {
      $getData = $pdo->prepare("SELECT role,username,email,password FROM user WHERE email = :email");
      $getData->bindparam(':email', $_POST['email']);
      $getData->execute();

      if($getData->rowCount() != 0) // Vérif mail existant
      {
        $userInfo = $getData->fetch();

        if(password_verify($_POST['mdp'],$userInfo['password'])) // Vérif password
        {
          $userInfo = $getData->fetch();
          $_SESSION['user']['role'] = $userInfo['role'];
          $_SESSION['user']['username'] = $userInfo['username'];
          $_SESSION['user']['email'] = $userInfo['email'];
          header("Refresh:0; url=".URL."?page=accueil");
        }
        else{
          echo '/!\ Mauvais mdp';
        }
      }
      else{
        echo '/!\ Email inexistant';
      }
    }
    $displayTemplatePage = new Template('template/bullant.html');

  break;

  case "logout" :

    session_destroy();
    header("Refresh:0; url=".URL."?page=accueil");
    exit();

  break;



  default : if($isFound === false)
  {
    notFound("1");
  }
  else{
    continue;
  }
  break;

} // FIN du switch



echo $displayTemplatePage->display();


?>
