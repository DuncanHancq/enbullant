<?php
require('init/request.inc.php'); // Fichier d'initialisation : BSD & Debug
require('class/class_template.php'); // moteur de rendu
require('class/class_template_bak.php'); // moteur de rendu


$getUrl = isset($_GET['p']) ? $_GET['p'] : null;
$userInfo = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$username = ($userInfo !== null) ? $userInfo['username'] : "Pas d'username";


//////////// Init CSS ////////////////
$commonCSS = array(
  URL.'asset/css/reset.css',
  URL.'asset/css/common.css',
  'https://fonts.googleapis.com/css?family=Ubuntu:300,400,400i,500',
  URL.'asset/css/fontawesome-all.min.css'
);
$frontCSS = array(URL.'asset/css/style.css');
$accueilCSS = array(URL.'asset/css/jquery.bxslider.css');
$bakCSS = array(URL.'asset/css/style_bak.css');

//////////// Init JS ////////////////
$commonJS = array(URL.'asset/js/sumenu.js');
$bakJS = array(
  URL.'asset/ckeditor/ckeditor.js',
  URL.'asset/js/edit_content.js'
);
$accueilJS = array(
  'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js',
  'https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js'
);



/*************************************************
*                                                *
* FRONT                                          *
*                                                *
*************************************************/
if ($userInfo !== null) // si l'utilisateur est connecté...
{
  switch ($getUrl)
  {

    /*******************************
    * Gestion utilisateur          *
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
    * Gestion Contenu              *
    *******************************/
    case "bullant/content" : // Contenus

      $displayTemplatePage = new TemplateBak('template/content.html');
      $displayTemplatePage->replaceContent('##TITLE##', 'Contenu');
      $displayTemplatePage->replaceContent('##H2##', 'Contenu');
      $displayTemplatePage->replaceContent('##CONTENT-DISPLAY##', displayArticle());


      $commonJS = array_merge($bakJS,$commonJS);
      $commonCSS = array_merge($commonCSS,$bakCSS);


    break;


    case "bullant/content/add" : // Ajout Contenus

      $displayTemplatePage = new TemplateBak('template/content-add.html');
      $displayTemplatePage->replaceContent('##TITLE##', 'Ajout contenu');
      $displayTemplatePage->replaceContent('##H2##', 'Ajout contenu');

      if(isset($_GET['type']) && $_GET['type'] == 0){
          $displayTemplatePage->replaceContent('##ACTU##', 'selected="selected"');
          $displayTemplatePage->replaceContent('##SPEC##', ' ');
      }
      else
      {
        $displayTemplatePage->replaceContent('##ACTU##', ' ');
        $displayTemplatePage->replaceContent('##SPEC##', 'selected="selected"');
      }
      $commonJS = array_merge($bakJS,$commonJS);
      $commonCSS = array_merge($commonCSS,$bakCSS);

      if(isset($_POST['addcontent']))
      {
        addContent();
      }

    break;

    case "bullant/content/modify" :

      $displayTemplatePage = new TemplateBak('template/content-modify.html');
      $displayTemplatePage->replaceContent('##TITLE##', 'Modification contenu');
      $displayTemplatePage->replaceContent('##H2##', 'Modification contenu');
      if(isset($_GET['get'])){
        

        getArticle($disp);
        
        $displayTemplatePage->replaceContent('##TITRE##', $disp['title']);
        $displayTemplatePage->replaceContent('##IMG##', $disp['image']);
        $displayTemplatePage->replaceContent('##CHAPO##', $disp['chapo']);
        $displayTemplatePage->replaceContent('##CORPS##', $disp['corps_text']);
        $displayTemplatePage->replaceContent('##LINK##', $disp['url_resa']);
        if($disp['type'] == 0){
          $displayTemplatePage->replaceContent('##TYPE0##', 'selected="selected"');
          $displayTemplatePage->replaceContent('##TYPE1##', " ");
        }else{
          $displayTemplatePage->replaceContent('##TYPE0##', 'selected="selected"');
          $displayTemplatePage->replaceContent('##TYPE1##', " ");
        }
        if($disp['publie'] == 0){
          $displayTemplatePage->replaceContent('##PUBLIE0##', 'selected="selected"');
          $displayTemplatePage->replaceContent('##PUBLIE1##', " ");
        }else{
          $displayTemplatePage->replaceContent('##PUBLIE0##', " ");
          $displayTemplatePage->replaceContent('##PUBLIE1##', 'selected="selected"');
        }
        if(isset($_POST['modifycontent'])){
          addContent(1);
        }
      }
      $commonJS = array_merge($bakJS,$commonJS);
      $commonCSS = array_merge($commonCSS,$bakCSS);

    break;

    /*******************************
    *  Gestion Image              *
    *******************************/
    case "bullant/image" : //

      $displayTemplatePage = new TemplateBak('template/image-bak.html');
      $displayTemplatePage->replaceContent('##TITLE##', 'Administration images');
      $displayTemplatePage->replaceContent('##H2##', 'Administration images');
      $displayTemplatePage->replaceContent('##IMG-DISPLAY##', displayImg());

      $commonJS  = array_merge($bakJS,$commonJS);
      $commonCSS = array_merge($commonCSS,$bakCSS);

      if(isset($_POST)){
        modifImg();
      }

    break;



    case "bullant/image/add" : // Ajout Image

      $displayTemplatePage = new TemplateBak('template/image-add.html');
      $displayTemplatePage->replaceContent('##TITLE##', 'Ajout Image');
      $displayTemplatePage->replaceContent('##H2##', 'Ajout Image');

      $commonJS  = array_merge($bakJS,$commonJS);
      $commonCSS = array_merge($commonCSS,$bakCSS);

      if(isset($_POST) && isset($_FILES)){
        addImg();
      }


    break;

    case "bullant/user/logout": // Page de déconnexion

        session_destroy();
        header("Refresh:0; url=".URL."?p=accueil");
        exit();

    break;

    default :

      continue;

    break;
  }

}

/*************************************************
*                                                *
* FRONT                                          *
*                                                *
*************************************************/
switch($getUrl)
{
  case null:
  case "accueil":

    $displayTemplatePage = new Template('template/index.html');
    $displayTemplatePage->replaceContent('##TITLE##', 'Accueil');

    $commonCSS = array_merge($commonCSS,$frontCSS,$accueilCSS);
    $commonJS = array_merge($commonJS,$accueilJS);

  break;

  /*******************************
  * Espace connexion             *
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

  case "articles":
      
    $displayTemplatePage = new Template('template/content-front.html');
    if(isset($_GET['c'])){
        
        getArticle($disp);
        $displayTemplatePage->replaceContent('##TITLE##', 'lshf');
        $displayTemplatePage->replaceContent('##CONTENT##', $disp);
        
    }

    $commonCSS = array_merge($commonCSS,$frontCSS);
    
  break;
  
  case "articles/actu":
      
    $displayTemplatePage = new Template('template/content-front.html');
    getArticle($disp);
    $displayTemplatePage->replaceContent('##TITLE##', 'Actualités');
    $displayTemplatePage->replaceContent('##CONTENT##', $disp);
        
   
    $commonCSS = array_merge($commonCSS,$frontCSS);
    
  break;
  
  case "articles/spec":
      
    $displayTemplatePage = new Template('template/content-front.html');
        
    getArticle($disp);
    $displayTemplatePage->replaceContent('##TITLE##', 'Spectacles');
    $displayTemplatePage->replaceContent('##CONTENT##', $disp);

    $commonCSS = array_merge($commonCSS,$frontCSS);
    
  break;

  /*******************************
  * 404                          *
  *******************************/
  case "404":
  default: // 404

    if(empty($displayTemplatePage)){
      $displayTemplatePage = new Template('template/404.html');
      $displayTemplatePage->replaceContent('##TITLE##', '404');

      $commonCSS = array_merge($commonCSS,$frontCSS);
    }

  break;

}

if($username !== null){$displayTemplatePage->replaceContent('##USERNAME##', $username);}
$displayTemplatePage->replaceContent('##CSS##', srcCSS($commonCSS));
$displayTemplatePage->replaceContent('##JS##', srcJS($commonJS));

echo $displayTemplatePage->display(); //| On affiche le contenu
