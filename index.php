<?php
require('init/request.inc.php'); // Fichier d'initialisation : BSD & Debug
require('class/class_template.php'); // moteur de rendu
require('class/class_template_bak.php'); // moteur de rendu


$getUrl = isset($_GET['p']) ? $_GET['p'] : null;
$userInfo = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$username = ($userInfo !== null) ? $userInfo['username'] : "Pas d'username";
$desc = "";

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
    case "bullant/content" : // VUE CONTENU

      $displayTemplatePage = new TemplateBak('template/content.html');
      $displayTemplatePage->replaceContent('##TITLE##', 'Contenu');
      $displayTemplatePage->replaceContent('##H2##', 'Contenu');
      $displayTemplatePage->replaceContent('##CONTENT-DISPLAY##', displayArticle());


      $commonJS = array_merge($bakJS,$commonJS);
      $commonCSS = array_merge($commonCSS,$bakCSS);


    break;


    case "bullant/content/add" : // AJOUT DE CONTENU

      $displayTemplatePage = new TemplateBak('template/content-add.html');
      $displayTemplatePage->replaceContent('##TITLE##', 'Ajout contenu');
      $displayTemplatePage->replaceContent('##H2##', 'Ajout contenu');

      if(isset($_GET['type']) && $_GET['type'] == 0){
        $displayTemplatePage->replaceContent('##ACTU##', ' ');
        $displayTemplatePage->replaceContent('##SPEC##', 'selected="selected"');
      }
      else
      {
        $displayTemplatePage->replaceContent('##ACTU##', 'selected="selected"');
        $displayTemplatePage->replaceContent('##SPEC##', ' ');
      }
      $commonJS = array_merge($bakJS,$commonJS);
      $commonCSS = array_merge($commonCSS,$bakCSS);

      if(isset($_POST['addcontent']))
      {
        addContent();
      }

    break;

    case "bullant/content/modify" : // MODIFICATION DE CONTENU

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
          $displayTemplatePage->replaceContent('##TYPE0##', ' ');
          $displayTemplatePage->replaceContent('##TYPE1##', 'selected="selected"');
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
    case "bullant/image" : // VUE IMAGE

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



    case "bullant/image/add" : // AJOUT IMAGE

      $displayTemplatePage = new TemplateBak('template/image-add.html');
      $displayTemplatePage->replaceContent('##TITLE##', 'Ajout Image');
      $displayTemplatePage->replaceContent('##H2##', 'Ajout Image');

      $commonJS  = array_merge($bakJS,$commonJS);
      $commonCSS = array_merge($commonCSS,$bakCSS);

      if(isset($_POST) && isset($_FILES)){
        addImg();
      }

    break;

    case "bullant/livre" : // GESTION LIVRE D'OR

      $displayTemplatePage = new TemplateBak('template/livre-bak.html');

      $displayTemplatePage->replaceContent('##TITLE##', 'Gestion livre d\'or');
      $displayTemplatePage->replaceContent('##H2##', 'Gestion livre d\'or');
      $displayTemplatePage->replaceContent('##DISPLAY##', 'Gestion livre d\'or');

      $commonJS  = array_merge($bakJS,$commonJS);
      $commonCSS = array_merge($commonCSS,$bakCSS);

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
    $displayTemplatePage->replaceContent('##CAROUSEL##', getCarousel());

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

    $displayTemplatePage = new Template('template/bullant.html');

    if(isset($_POST['connexion']) && $_POST['connexion'] == 'Connexion')
    {
      $alert = "";
      $response = $_POST["g-recaptcha-response"];
      $url = 'https://www.google.com/recaptcha/api/siteverify';
      $data = array(
        'secret' => '	6LekZV0UAAAAAEGb1kj9duILBqsNFtPS0KGewz-_',
        'response' => $_POST["g-recaptcha-response"]
      );
      $options = array(
        'http' => array (
          'header' => "Content-Type: application/x-www-form-urlencoded",
          'method' => 'POST',
          'content' => http_build_query($data)
        )
      );
      $context  = stream_context_create($options);
      $verify  = file_get_contents($url, false, $context);
      $captcha_success=json_decode($verify);
      if ($captcha_success->success==false) {
        $alert .= "<p class=\"alert\">Soit vous parlez le language binaire, soit vous n'avez pas indiqué le contraire !</p>";
      } else if ($captcha_success->success==true) {
        connectUser($alert);
      }
      $displayTemplatePage->replaceContent('##ALERT##', $alert);
    } // isset connexion
    else{
      $displayTemplatePage->replaceContent('##ALERT##', ' ');
    }

    array_push($commonJS, 'https://www.google.com/recaptcha/api.js');
    $commonCSS = array_merge($commonCSS,$frontCSS);

  break;

  case "articles":

    $displayTemplatePage = new Template('template/content-front.html');
    if(isset($_GET['c'])){

        getArticle($disp);
        $displayTemplatePage->replaceContent('##TITLE##', 'Article');
        $displayTemplatePage->replaceContent('##CONTENT##', $disp);

    }

    $commonCSS = array_merge($commonCSS,$frontCSS);

  break;

  case "articles/actu":

    $displayTemplatePage = new Template('template/content-front.html');
    $disp = 0;
    getArticle($disp);
    $displayTemplatePage->replaceContent('##TITLE##', 'Actualités');
    $displayTemplatePage->replaceContent('##CONTENT##', $disp);


    $commonCSS = array_merge($commonCSS,$frontCSS);

  break;

  case "articles/spec":

    $displayTemplatePage = new Template('template/content-front.html');
    $disp = 1;
    getArticle($disp);
    $displayTemplatePage->replaceContent('##TITLE##', 'Spectacles');
    $displayTemplatePage->replaceContent('##CONTENT##', $disp);
    $commonCSS = array_merge($commonCSS,$frontCSS);

  break;

  case "livre":

    $displayTemplatePage = new Template('template/livre.html');
    $displayTemplatePage->replaceContent('##DISPLAY##', displayLivre());

    $alert = "";
    if(isset($_POST['livre'])){
      $response = $_POST["g-recaptcha-response"];
      $url = 'https://www.google.com/recaptcha/api/siteverify';
      $data = array(
        'secret' => '	6LekZV0UAAAAAEGb1kj9duILBqsNFtPS0KGewz-_',
        'response' => $_POST["g-recaptcha-response"]
      );
      $options = array(
        'http' => array (
          'header' => "Content-Type: application/x-www-form-urlencoded",
          'method' => 'POST',
          'content' => http_build_query($data)
        )
      );
      $context  = stream_context_create($options);
      $verify  = file_get_contents($url, false, $context);
      $captcha_success=json_decode($verify);
      if ($captcha_success->success==false) {
        $alert = "<p class=\"alert\">Soit vous parlez le language binaire, soit vous n'avez pas indiqué le contraire !</p>";
      } else if ($captcha_success->success==true) {
        addLivre();
        $alert = "<p class=\"success\">Merci pour le message, celui-ci apparaitra aprés validation d'un administrateur.</p>";
      }

    }



    $displayTemplatePage->replaceContent('##TITLE##', 'Livre d\'or');
    $displayTemplatePage->replaceContent('##H1##', 'Livre d\'or');
    $displayTemplatePage->replaceContent('##ALERT##', $alert);
    array_push($commonJS, 'https://www.google.com/recaptcha/api.js');

    $commonCSS = array_merge($commonCSS,$frontCSS);

  break;

  case "galerie":
    $displayTemplatePage = new Template('template/image-front.html');

    $displayTemplatePage->replaceContent('##TITLE##', 'Galerie');
    $displayTemplatePage->replaceContent('##H1##', 'Galerie');
    $displayTemplatePage->replaceContent('##DISPLAY-IMAGE##', getGalerie());

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
// Metatag OPENGRAPH
$displayTemplatePage->replaceContent('##URL##', URL);
$displayTemplatePage->replaceContent('##DESC##', $desc = ($desc == "") ? "Faites un tour sur notre site web :)" : $desc);


echo $displayTemplatePage->display(); //| On affiche le contenu
