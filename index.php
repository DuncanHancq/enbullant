<?php
require('init/request.inc.php'); // Fichier d'initialisation : BSD & Debug
require('class/class_template.php'); // moteur de rendu
require('class/class_template_bak.php'); // moteur de rendu

$pageMatch = 0;
$getUrl = isset($_GET['p']) ? $_GET['p'] : null;
$userInfo = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$username = ($userInfo !== null) ? $userInfo['username'] : "Pas d'username";

$commonCSS = array(
  'asset/css/reset.css',
  'asset/css/common.css',
  'https://fonts.googleapis.com/css?family=Ubuntu:300,400,400i,500',
  'asset/css/fontawesome-all.min.css'
);
$commonJS = array(
  'asset/js/sumenu.js'
);

$bakCSS = array(
  'asset/css/style_bak.css'
);

$bakJS = array(
  'asset/ckeditor/ckeditor.js',
  'asset/js/edit_content.js'
);

$frontCSS = array(
  'asset/css/style.css',
);

/****************************************************************
---------------BACK OFFICE
****************************************************************/


if ($userInfo !== null) // si l'utilisateur est connecté...
{
  switch ($getUrl)
  {
    /*******************************
    Gestion utilisateur
    *******************************/

    case "bullant/user":

      $displayTemplatePage = new TemplateBak('template/user.html');
      $displayTemplatePage->replaceContent('##TITLE##', 'Gestion des utilisateurs');
      $displayTemplatePage->replaceContent('##H2##', 'Gestion des utilisateurs');
      $displayTemplatePage->replaceContent('##USER-DISPLAY##', displayUsers());

      $commonCSS = array_merge($commonCSS,$bakCSS);

    break;

    case "bullant/user/add" : // Page ajouts utilisateurs

      $displayTemplatePage = new TemplateBak('template/user-add.html');
      $displayTemplatePage->replaceContent('##TITLE##', 'Ajouter membre');
      $displayTemplatePage->replaceContent('##H2##', 'Ajouter membre');

      $commonCSS = array_merge($commonCSS,$bakCSS);

      if(isset($_POST['ajoutmbr']))
      {
        addUser();
      }

    break;

    /*******************************
    Gestion Contenu
    *******************************/

    case "bullant/content/add" : // Ajout Contenus

      $displayTemplatePage = new TemplateBak('template/actu.html');
      $displayTemplatePage->replaceContent('##TITLE##', 'Ajout contenu');
      $displayTemplatePage->replaceContent('##H2##', 'Ajout contenu');

      $commonJS = array_merge($bakJS,$commonJS);
      $commonCSS = array_merge($commonCSS,$bakCSS);

      if(isset($_POST['addcontent']))
      {
        addContent();
      }

    break;

    /*******************************
    Gestion Image
    *******************************/

    case "bullant/image/add" : // Ajout Contenus

      $displayTemplatePage = new TemplateBak('template/image-add.html');
      $displayTemplatePage->replaceContent('##TITLE##', 'Ajout Image');
      $displayTemplatePage->replaceContent('##H2##', 'Ajout Image');

      $commonJS = array_merge($bakJS,$commonJS);
      $commonCSS = array_merge($commonCSS,$bakCSS);

      if(isset($_POST) && isset($_FILES)){
        debug($_POST);
        debug($_FILES);
        foreach($_FILES as $case)
        {
          foreach($case as $key => $val){
            echo $key . " :  " . $val . "<br>" ;
          }
        }
      }

    break;

    case "bullant/user/logout": // Page de déconnexion

        session_destroy();
        header("Refresh:0; url=".URL."?p=accueil");
        exit();

    break;

    default :
      $pageMatch++;
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

    $commonCSS = array_merge($commonCSS,$frontCSS);

  break;

  /*******************************
  Espace connexion
  *******************************/

  case "bullant": // Page de connexion

    if($userInfo !== null)
    {
      header('Location:'.URL.'?p=bullant/user');
      exit();
    }

    if(isset($_POST['connexion']) && $_POST['connexion'] == 'Connexion')
    {
      connectUser();
    }

    $displayTemplatePage = new Template('template/bullant.html');

    $commonCSS = array_merge($commonCSS,$frontCSS);

  break;


  /*******************************
  404
  *******************************/


  default: // 404
    if ($pageMatch == 1){ //| On check si une page à été trouvée
      $displayTemplatePage = new Template('template/404.html');
      $commonCSS = array_merge($commonCSS,$frontCSS);
    }
  break;

}

//if($username !== null){$displayTemplatePage->replaceContent('##USERNAME##', $username);}
$displayTemplatePage->replaceContent('##CSS##', srcCSS($commonCSS));
$displayTemplatePage->replaceContent('##JS##', srcJS($commonJS));

echo $displayTemplatePage->display(); //| On affiche le contenu
