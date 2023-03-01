<?php
// affichage des catégories dans la navigation latérale
$afficheMenuCategorie = $pdo->query("SELECT DISTINCT categorie FROM produit ORDER BY categorie ASC");
// fin de navigation laterale catégories

// tout l'affichage par categorie
if(isset($_GET['categorie'])){

    // pagination pour les categories



    // PAGINATION PRODUITS
    if(isset($_GET['page']) && !empty($_GET['page'])){
        $pageCourante = (int) strip_tags($_GET['page']);
    }else{
        $pageCourante = 1;
    }
    $queryProduit = $pdo->query("SELECT COUNT(id_produit) AS nombreCategorie FROM produit");
    $resultatCategorie = $queryProduit->fetch();
    $nombreCategorie = (int) $resultatCategorie['nombreCategorie'];
    // echo debug($nombreProduit);
    $parPage = 3;
    $nombrePages = ceil($nombreCategorie / $parPage);
    $premierProduit = ($pageCourante - 1) * $parPage;



    // fin pagination pour les categories

    // AFFICHAGE DES PRODUITS PAR PUBLIC
    $afficheProduits = $pdo->query("SELECT * FROM produit WHERE categorie = '$_GET[categorie]' ORDER BY prix ASC");

    // AFFICHAGE DE TITLE H2
    $afficheTitreCategorie = $pdo->query("SELECT categorie FROM produit WHERE categorie = '$_GET[categorie]'");
    $titreCategorie = $afficheTitreCategorie->fetch(PDO::FETCH_ASSOC);

    // AFFICHAGE DE TITLE POUR LES ONGLETS
    $pageTitle = "Nous modéles de " . $_GET['categorie'];
    // fin onglets categories
}
// fin affichage par categorie

// -----------------------------------------------------------------------------------

// AFFICHAGE PUBLIC DEPUIS LA NAV
if(isset($_GET['public'])){
    // pagination produits par public
    
    // fin pagination produits par public

    // AFFICHAGE DES PRODUITS PAR PUBLIC
    $afficheProduits = $pdo->query("SELECT * FROM produit WHERE public = '$_GET[public]' ORDER BY prix ASC");

    // AFFICHAGE DE TITLE H2
    $afficheTitrePublic = $pdo->query("SELECT public FROM produit WHERE public = '$_GET[public]'");
    $titrePublic = $afficheTitrePublic->fetch(PDO::FETCH_ASSOC);

    // AFFICHAGE DE TITLE POUR LES ONGLETS
    $pageTitle = "Nous vêtements " . $_GET['public'] . "s";
}
// fin affichage par public

// ---------------------------------------------------------------------------------------

// Tout ce qui concerne la fiche produit
if(isset($_GET['id_produit'])){

    // AFFICHAGE DES PRODUITS PAR ID_PRODUIT
    $afficheProduitSelect = $pdo->query("SELECT * FROM produit WHERE id_produit = '$_GET[id_produit]'"); 
    if($afficheProduitSelect->rowCount() <= 0){
        header('location:' . URL);
        exit;
    }
    $detail = $afficheProduitSelect->fetch(PDO::FETCH_ASSOC);

}

// fin affichage d'un seul produit


//  fin fiche produit

// --------------------------------------------------------------------------------------------