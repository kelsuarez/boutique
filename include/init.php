<?php
// dans ce fichier init, on va coder tout ce qui va nous servir sur l'intégralité des fichiers de notre boutique

// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=boutique', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8') );

// pdo EN LIGNE
// $pdo = new PDO('mysql:host=db5012100074.hosting-data.io;dbname=dbs10181395;charset=utf8', 'dbu1410952', 'Ionos123456!!', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));


// le session_start obligatoire en haut de chaque fichier
session_start();

// constante pour définir le chemin vers mon projet (je récupère automatiqument C:/wamp/www grace à $_SERVER[DOCUMENT_ROOOT] auquel je concatène le dossier de mon projet)
// ce chemin fonctionnera en local comme en ligne
define('RACINE_SITE', $_SERVER['DOCUMENT_ROOT'] .'/boutique/' );

// constante URL pour notre projet (a modifier avec le nom de domaine plus tard lorsque le site sera hébergé, mis en ligne)
define('URL', 'http://localhost/boutique/');

// URL EN LIGNE
// define('URL', 'https://boutique.dwsuarez.com/');

// initialisation de qlq variables utiles sur tout le site
$erreur = "";
$erreur_index = "";
$validate = "";
$validate_index = "";
$content = "";

// protection des formulaires avec une foreach additionnée avec htmlspecialchars
foreach($_POST as $key => $value){
    // on ajoute trim pour des gains en espace mémoire
    // elle va supprimer tous les espaces avant et aprés la valeur renseignée (ils sont inutiles)
    $_POST[$key] = htmlspecialchars(trim($value));
}

// protection des url avec une foreach additionnée avec htmlspecialchars
foreach($_GET as $key => $value){
    $_GET[$key] = htmlspecialchars(trim($value));
}

// inclusion de tout le code de ce fichier, pour le distribuer à toutes les pages du site en une seule fois
require_once('fonctions.php');