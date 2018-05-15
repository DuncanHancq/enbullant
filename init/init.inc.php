<?php

session_start();


// Sert à afficher toutes les erreurs. A COMMENTER EN PROD.
error_reporting(E_ALL);

// Forcer l'affichage des erreurs. Important pour contourner la configuration du fichier php.ini
ini_set('display_errors', 'On');


/*
 * Connexion à la BDD :
**/

// Configuration de base :
$bddOptions = array(
    // On force l'encodage en utf8.
    PDO::MYSQL_ATTR_INIT_COMMAND    => "SET NAMES utf8",
    // En absence de paramètre récuperation en tableau par défaut à l'utilisation d'un fetch()
    PDO::ATTR_DEFAULT_FETCH_MODE     => PDO::FETCH_ASSOC,
    // On affiche les erreurs de type warning. A COMMENTER EN PROD.
    PDO::ATTR_ERRMODE               => PDO::ERRMODE_WARNING
);

// Type de la base de donné.
define('TYPEBDD','mysql');
// Domaine du serveur.
define('HOST', 'localhost');
// Nom de l'utilisateur.
define('USER','root');
// Mot de passe.
define('PASSWORD','');
// Nom de la base de donné.
define('DBNAME','enbullant');

// On essai de se connecter à la base de donné.
try
{
    $pdo = new PDO(TYPEBDD . ':host=' . HOST . ';dbname=' . DBNAME,USER,PASSWORD,$bddOptions);
}
catch(Exception $e)
{
    // Indique une erreur si on ne peut pas se connecter à la base de donné.
    die('Erreur(s) BDD : ' . $e ->getMessage());
}

/*
 * Constantes :
**/

// echo '<pre>';
// print_r($_SERVER);
// echo '</pre>';

// echo __DIR__; //Afficher la localisation du fichier.

define('URL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/bullant/');
// Adapte le HTTP de façon automatique.
/* '/PHP/site_dynamique/' <- A commenter lors de la mise en ligne.*/

// A supprimer lors de la mise en ligne.
define('RACINE', $_SERVER['DOCUMENT_ROOT'] . '/bullant/');


/*
 * Variables d'affichage :
**/
$content = "";
$suMenu = "";
$scriptRequire = array();
$scriptRequire = [];




/* fonctions : */
require 'func.inc.php';

?>
