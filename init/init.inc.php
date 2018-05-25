<?php
session_start();
/* fonctions : */
require 'func.inc.php';


// Afficher toutes les erreurs
error_reporting(E_ALL);

// Forcer l'affichage des erreurs
ini_set('display_errors', 'On');

function test($t){
  echo "$t";
}

/*
 * Connexion BDD :
**/

// Configuration de base :
$bddOptions = array(
    // Encodage en utf8
    PDO::MYSQL_ATTR_INIT_COMMAND    => "SET NAMES utf8",
    // Recuperation des données en array par défaut
    PDO::ATTR_DEFAULT_FETCH_MODE     => PDO::FETCH_ASSOC,
    // Affiche des erreurs "warning" A COMMENTER EN PROD.
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
    die('Erreur(s) de la base de données : ' . $e ->getMessage());
}

/*
 * Constantes :
**/

// echo '<pre>';
// print_r($_SERVER);
// echo '</pre>';

// echo __DIR__; //Afficher la localisation du fichier.

// Adapte le HTTP de façon automatique.
define('URL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/enbullant/');

// A supprimer lors de la mise en ligne.
define('RACINE', $_SERVER['DOCUMENT_ROOT'] . '/enbullant/');

?>
