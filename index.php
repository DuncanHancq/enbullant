<?php
require('init/init.inc.php'); // Fichier d'initialisation : BSD & Debug
require('class/class_template.php'); // moteur de rendu
require('class/class_template_bak.php'); // moteur de rendu

$pageMatch = true;
$getUrl = isset($_GET['p']) ? htmlspecialchars($_GET['p']) : null;

/****************************************************************
---------------BACK OFFICE
****************************************************************/

if (isset($_SESSION['user'])) // si l'utilisateur est connecté...
{
  switch ($getUrl)
  {
    case "user":
        
      $userDisplay = null;
      $getUser = $pdo->query('SELECT id_user AS id, username, role FROM user');
      $nbrCol = $getUser->columnCount();
      $userDisplay .= '<tr>';
      
      for($i = 0; $i < $nbrCol; $i++)
      {

          $nomCol = $getUser->getColumnMeta($i);//A chaque tour de boucle je récupère les intitulés de mes champs
          $userDisplay .= '<th>' . ucfirst($nomCol['name']) . '</th>';

      }
      
      $userDisplay .= '<th>Modif.</th><th>Suppr.</th></tr>';
      $userDisplay .= '</tr>';

      while($row = $getUser->fetch())
      {
        $userDisplay .= '<tr><td>'.$row['id'].'</td><td>'.$row['username'].'</td><td>'.$row['role'].'</td><td>0</td><td>X</td>';
      }

      $displayTemplatePage = new TemplateBak('template/user.html');
      $displayTemplatePage->replaceContent('##H2##', 'Gestion des utilisateurs');
      $displayTemplatePage->replaceContent('##USER-DISPLAY##', $userDisplay);

    break;

    case "user-add" :
        
      $displayTemplatePage = new TemplateBak('template/user-add.html');
      $displayTemplatePage->replaceContent('##TITLE##', 'Ajouter membre');
      $displayTemplatePage->replaceContent('##H2##', 'Ajouter membre');

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

      }

    case "actu" :
        
      $displayTemplatePage = new TemplateBak('template/actu.html');
      $displayTemplatePage->replaceContent('##TITLE##', 'Ajout contenu');
      $displayTemplatePage->replaceContent('##H2##', 'Ajout contenu');
    
    break;
      
    break;
    
    case "logout": // Page de déconnexion

        session_destroy();
        header("Refresh:0; url=".URL."?p=accueil");
        exit();

    break;

    default :
      $pageMatch = false;
      continue;

    break;
  }

}


/****************************************************************
---------------FRONT-OFFICE---------------
*****************************************************************/

switch($getUrl)
{
  case null:
  case "accueil":

    $displayTemplatePage = new Template('template/index.html');
    $displayTemplatePage->replaceContent('##TITLE##', 'Accueil');
    $displayTemplatePage->replaceContent('##HEADERBO##', 'caca');

  break;

  case "bullant": // Page de connexion

    if(isset($_POST['connexion']) && $_POST['connexion'] == 'Connexion')
    {
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
    
    $displayTemplatePage = new Template('template/bullant.html');

  break;

  



  default: // Aucune page détecté, redirection sur la page 404

    if ($pageMatch == false){
      $displayTemplatePage = new Template('template/404.html');
    }
    
  break;

} // FIN du switch



echo $displayTemplatePage->display();


?>
