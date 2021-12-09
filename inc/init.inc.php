<?php 
// CONNEXION BDD 
$bdd = new PDO('mysql:host=localhost;dbname=commerce', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
]);

// COOKIE 
$tabIdArticle = [];

// SESSION 
session_start();

//  CONSTANTE 
// Lorsque nous enregistrons une image d'article, nous devons la copier dans un dossier sur le serveur, cette constante permettra de définir le chemin du dossier dans lequel nous copierons l'image uplodé
define("RACINE_SITE", $_SERVER['DOCUMENT_ROOT'] . '/PHP/10-boutique/');

// echo $_SERVER['DOCUMENT_ROOT'] . '<br>';
// echo RACINE_SITE . '<br>';

// Cette constante permettra d'enregistrée dans la BDD l'URL de l'image uplodé, on ne conserve jamais l'image elle même dans la BDD mais l'URL jusqu'au dossier où elle est enregistrée
define("URL", "http://localhost/PHP/10-boutique/");

// FAILLES XSS 
foreach($_POST as $key => $value)
{
    $_POST[$key] = htmlspecialchars(strip_tags(trim($value)));
}

foreach($_GET as $key => $value)
{
    $_GET[$key] = htmlspecialchars(strip_tags(trim($value)));
}

// INCLUSION FONCTIONS
require_once 'fonctions.inc.php';





