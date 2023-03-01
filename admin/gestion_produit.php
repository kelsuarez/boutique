<!-- OUVERTURE DE MON PASSAGE PHP -->
<?php

// INCLUDE INIT
require_once('../include/init.php');

// RENVOIE DE USER QUI SOIT PAS L'ADMIN
if(!internauteConnecteAdmin()){
    header('location:' . URL . 'connexion.php' );
    exit();
}

// PAGINATION PRODUITS
if(isset($_GET['page']) && !empty($_GET['page'])){
    $pageCourante = (int) strip_tags($_GET['page']);
}else{
    $pageCourante = 1;
}

$queryProduit = $pdo->query("SELECT COUNT(id_produit) AS nombreProduit FROM produit");
$resultatProduits = $queryProduit->fetch();
$nombreProduit = (int) $resultatProduits['nombreProduit'];

// echo debug($nombreProduit);

$parPage = 10;

$nombrePages = ceil($nombreProduit / $parPage);

$premierProduit = ($pageCourante - 1) * $parPage;

// REQUETTE GET POUR TRAVAIILLER SUR L'URL
if(isset($_GET['action'])){

    // REQUETTE DE CONTRAINTES
    if($_POST){
        // REFERENCE
        if(!isset($_POST['reference']) || !preg_match('#^[A-Z]{3,20}$#', $_POST['reference'])){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format reference !</div>';
        }
        // CATEGORIE
        if(!isset($_POST['categorie']) || !preg_match('#^[a-zA-Z0-9-_.éà\'è]{3,100}$#', $_POST['categorie'])){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format categorie !</div>';
        }
        // TITRE
        if(!isset($_POST['titre']) || iconv_strlen($_POST['titre']) < 3 || iconv_strlen($_POST['titre']) > 20 ){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format titre !</div>';
        }
        // DESCRIPTION
        if(!isset($_POST['description']) || !preg_match('#^[a-zA-Z0-9-_.éà\'è]{3,100}$#', $_POST['description'])){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format description !</div>';
        }
        // COULEUR
        if(!isset($_POST['couleur']) || $_POST['couleur'] != 'bleu' && $_POST['couleur'] != 'rouge' && $_POST['couleur'] != 'vert' && $_POST['couleur'] != 'jaune' && $_POST['couleur'] != 'blanc' && $_POST['couleur'] != 'noir' && $_POST['couleur'] != 'marron'){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format taille !</div>';
        }
        // TAILLE
        if(!isset($_POST['taille']) || $_POST['taille'] != 'small' && $_POST['taille'] != 'medium' && $_POST['taille'] != 'large' && $_POST['taille'] != 'xlarge'){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format taille !</div>';
        }
        // PUBLIC
        if(!isset($_POST['public']) || $_POST['public'] != 'enfant' && $_POST['public'] != 'femme' && $_POST['public'] != 'homme' && $_POST['public'] != 'mixte'){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format taille !</div>';
        }
        // PHOTO
        if(isset($_FILES['fichier']) && $_FILES['fichier']['error'] == 0) {
            if ($_FILES['fichier']['size'] > $_POST['MAX_FILE_SIZE']) {
                echo "Le fichier téléchargé est trop volumineux.";
            }
            $allowed = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png');
            $filename = $_FILES['fichier']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(!in_array($ext, $allowed)) {
                echo "Le type de fichier n'est pas autorisé.";
            }
        }
        // PRIX
        if(!isset($_POST['prix']) || !preg_match('#^[0-9]{0,5}$#', $_POST['prix'])){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format code prix !</div>';
        }
        // STOCK
        if(!isset($_POST['stock']) || !preg_match('#^[0-9]{0,5}$#', $_POST['stock'])){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format code stock !</div>';
        }
        // TRAITEMENT PHOTO
        $photoBdd = "";
        if($_GET['action'] == 'update'){
            $photoBdd = $_POST['photo_actualle'];
        }
        if(!empty($_FILES['photo']['name'])){
            $photo_nom = $_POST['reference'] . '_' . $_FILES['photo']['name'];
            $photoBdd = "$photo_nom";
            $photoDossier = RACINE_SITE . "img/$photo_nom";
            copy($_FILES['photo']['tmp_name'], $photoDossier);
        }

        // REQUETTE DE SI PAS DE ERREUR ON PEUT CONTINUER
        if(empty($erreur)){

            // REQUETTE DE UPDATE
            if($_GET['action'] == 'update'){
                $modifProduit = $pdo->prepare("UPDATE produit SET id_produit = :id_produit, reference = :reference, categorie = :categorie, titre = :titre, description = :description, couleur = :couleur, taille = :taille, public = :public, photo = :photo, prix = :prix, stock = :stock WHERE id_produit = :id_produit");
                $modifProduit->bindValue(':id_produit', $_POST['id_produit'], PDO::PARAM_INT);
                $modifProduit->bindValue(':reference', $_POST['reference'], PDO::PARAM_STR);
                $modifProduit->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_STR);
                $modifProduit->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
                $modifProduit->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
                $modifProduit->bindValue(':couleur', $_POST['couleur'], PDO::PARAM_STR);
                $modifProduit->bindValue(':taille', $_POST['taille'], PDO::PARAM_STR);
                $modifProduit->bindValue(':public', $_POST['public'], PDO::PARAM_STR);
                $modifProduit->bindValue(':photo', $photoBdd, PDO::PARAM_STR);
                $modifProduit->bindValue(':prix', $_POST['prix'], PDO::PARAM_INT);
                $modifProduit->bindValue(':stock', $_POST['stock'], PDO::PARAM_INT);
                $modifProduit->execute();

                // MESSAGE DE VALIDATION A L'ENVOI
                $queryProduit = $pdo->query("SELECT reference FROM produit WHERE id_produit = '$_GET[id_produit]'");
                    $produit = $queryProduit->fetch(PDO::FETCH_ASSOC);
                    $content .= '<div class="alert alert-success alert-dismissible fade show mt-5" role="alert">
                                    <strong>Félicitations !</strong> Modification de produit ' . $produit['reference'] .  ' est réussie 😉!
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>';
            }else{

                // REQUETTE DE INSERTION A LA BDD
                $incluireProduit = $pdo->prepare(" INSERT INTO produit (reference, categorie, titre, description, couleur, taille, public, photo, prix, stock) VALUES (:reference, :categorie, :titre, :description, :couleur, :taille, :public, :photo, :prix, :stock) ");
                $incluireProduit->bindValue(':reference', $_POST['reference'], PDO::PARAM_STR);
                $incluireProduit->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_STR);
                $incluireProduit->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
                $incluireProduit->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
                $incluireProduit->bindValue(':couleur', $_POST['couleur'], PDO::PARAM_STR);
                $incluireProduit->bindValue(':taille', $_POST['taille'], PDO::PARAM_STR);
                $incluireProduit->bindValue(':public', $_POST['public'], PDO::PARAM_STR);
                $incluireProduit->bindValue(':photo', $photoBdd, PDO::PARAM_STR);
                $incluireProduit->bindValue(':prix', $_POST['prix'], PDO::PARAM_INT);
                $incluireProduit->bindValue(':stock', $_POST['stock'], PDO::PARAM_INT);
                $incluireProduit->execute();
            }
        }
    }

    // REQUETTE DE UPDATE
    if($_GET['action'] == 'update'){
        $tousProduit = $pdo->query("SELECT * FROM produit WHERE id_produit = '$_GET[id_produit]'");
        $produitActuel = $tousProduit->fetch(PDO::FETCH_ASSOC);
    }

    // REQUETTE DE RECUPERATION DE BDD PAR APPORT A LES DONNES EN FORM
    $idProduit = (isset($produitActuel['id_produit'])) ? $produitActuel['id_produit'] : "";
    $reference = (isset($produitActuel['reference'])) ? $produitActuel['reference'] : "";
    $categorie = (isset($produitActuel['categorie'])) ? $produitActuel['categorie'] : "";
    $titre = (isset($produitActuel['titre'])) ? $produitActuel['titre'] : "";
    $description = (isset($produitActuel['description'])) ? $produitActuel['description'] : "";
    $couleur = (isset($produitActuel['couleur'])) ? $produitActuel['couleur'] : "";
    $taille = (isset($produitActuel['taille'])) ? $produitActuel['taille'] : "";
    $public = (isset($produitActuel['public'])) ? $produitActuel['public'] : "";
    $photo = (isset($produitActuel['photo'])) ? $produitActuel['photo'] : "";
    $prix = (isset($produitActuel['prix'])) ? $produitActuel['prix'] : "";
    $stock = (isset($produitActuel['stock'])) ? $produitActuel['stock'] : "";
    // A FAIRE -> PHOTO

    // REQUETTE POUR DELETE
    if($_GET['action'] == 'delete'){
        $pdo->query("DELETE FROM produit WHERE id_produit = '$_GET[id_produit]'");
    }       
}
 
// INCLUDE HEADER
require_once('includeAdmin/header.php');

// FERMETURE DE MON PASSAGE PHP
?>

<!-- TITRE H1 -->
<h1 class="text-center my-5"><div class="badge badge-warning text-wrap p-3">Gestion des produits</div></h1>


<!-- MODULE POUR MONTRER LES ERREURS ET LE CONTENU -->
<?php echo $erreur ?>
<?= $content ?>

<!-- POPUP -->
<?php if(!isset($_GET['action']) && !isset($_GET['page'])):?>
    <div class="blockquote alert alert-dismissible fade show mt-5 shadow border border-warning rounded" role="alert">
        <p>Gérez ici votre base de données des produits</p>
        <p>Vous pouvez modifier leurs données, ajouter ou supprimer un produit</p>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif ?>

<!-- FORMULAIRE -->
<?php if(isset($_GET['action'])): ?>
    <h2 class="pt-5">Formulaire <?=($_GET['action'] == 'add') ? "d'ajout" : "de modification" ?> des produits</h2>

    <form id="monForm" class="my-5" method="POST" action=""  enctype="multipart/form-data">

        <!-- INPUT HIDDEN -->
        <input type="hidden" name="id_produit" value="<?= $idProduit ?>">

        <div class="row mt-5">
            <!-- MODULE REFERENCE -->
            <div class="col-md-4">
                <label class="form-label" for="reference"><div class="badge badge-dark text-wrap">Référence</div></label>
                <input class="form-control" type="text" name="reference" id="reference"  placeholder="Référence" value="<?= $reference?>">
            </div>
            <!-- MODULE CATEGORIE -->
            <div class="col-md-4">
                <label class="form-label" for="categorie"><div class="badge badge-dark text-wrap">Catégorie</div></label>
                <input class="form-control" type="text" name="categorie" id="categorie"  placeholder="Catégorie" value="<?= $categorie?>">
            </div>
            <!-- MODULE TITRE -->
            <div class="col-md-4">
                <label class="form-label" for="titre"><div class="badge badge-dark text-wrap">Titre</div></label>
                <input class="form-control" type="text" name="titre" id="titre"  placeholder="Titre" value="<?= $titre?>">
            </div>
        </div>
        <div class="row justify-content-around mt-5">
            <!-- MODULE DESCRIPTION -->
            <div class="col-md-6">
                <label class="form-label" for="description"><div class="badge badge-dark text-wrap">Description</div></label>
                <textarea class="form-control" name="description" id="description" placeholder="Description" rows="5"><?= $description?></textarea>
                <!-- Pour recuperer l'info sur le text area il va falloir mettre le passage php directement entre les deux balises textarea -->
            </div>
        </div>
        <div class="row mt-5">
            <!-- MODULE COULEUR -->
            <div class="col-md-4 mt-3">
                <label class="badge badge-dark text-wrap" for="couleur">Couleur</label>
                <select class="form-control" name="couleur" id="couleur">
                    <option value="">Choisissez</option>
                    <option class="bg-primary text-light" value="bleu" <?= ($couleur == "bleu") ? 'selected' : "" ?>>Bleu</option>
                    <option class="bg-danger text-light" value="rouge" <?= ($couleur == "rouge") ? 'selected' : "" ?>>Rouge</option>
                    <option class="bg-success text-light" value="vert" <?= ($couleur == "vert") ? 'selected' : "" ?>>Vert</option>
                    <option class="bg-warning text-light" value="jaune" <?= ($couleur == "jaune") ? 'selected' : "" ?>>Jaune</option>
                    <option class="bg-light text-dark" value="blanc" <?= ($couleur == "blanc") ? 'selected' : "" ?>>Blanc</option>
                    <option class="bg-dark text-light" value="noir" <?= ($couleur == "noir") ? 'selected' : "" ?>>Noir</option>
                    <option class="text-light" style="background:brown;" value="marron" <?= ($couleur == "marron") ? 'selected' : "" ?>>Marron</option>
                </select>
            </div>
            <!-- MODULE TAILLE -->
            <div class="col-md-4">
                <p><div class="badge badge-dark text-wrap">Taille</div></p>
                <input type="radio" name="taille" id="taille1" value="small" <?= ($taille == "small") ? 'checked' : "" ?>>
                <label class="mx-1" for="taille1">Small</label>
                <input type="radio" name="taille" id="taille2" value="medium" <?= ($taille == "medium") ? 'checked' : "" ?>>
                <label class="mx-1" for="public2">Medium</label>
                <input type="radio" name="taille" id="taille3" value="large" <?= ($taille == "large") ? 'checked' : "" ?>> 
                <label class="mx-1" for="taille3">Large</label>
                <input type="radio" name="taille" id="taille4" value="xlarge" <?= ($taille == "xlarge") ? 'checked' : "" ?>> 
                <label class="mx-1" for="taille4">XLarge</label>
            </div>
            <!-- MODULE PUBLIC -->
            <div class="col-md-4">
                <p><div class="badge badge-dark text-wrap">Public</div></p>
                <input type="radio" name="public" id="public1" value="enfant" <?= ($public == "enfant") ? 'checked' : "" ?>>
                <label class="mx-1" for="public1">Enfant</label>
                <input type="radio" name="public" id="public2" value="femme" <?= ($public == "femme") ? 'checked' : "" ?>>
                <label class="mx-1" for="public2">Femme</label>
                <input type="radio" name="public" id="public3" value="homme" <?= ($public == "homme") ? 'checked' : "" ?>>
                <label class="mx-1" for="public3">Homme</label>
                <input type="radio" name="public" id="public4" value="mixte" <?= ($public == "mixte") ? 'checked' : "" ?>> 
                <label class="mx-1" for="public4">Mixte</label>
            </div>
        </div>
        <div class="row mt-5">
            <!-- MODULE PHOTO -->
            <div class="col-md-4">
                <label class="form-label" for="photo"><div class="badge badge-dark text-wrap">Photo</div></label>
                <input class="form-control" type="file" name="photo" id="photo" placeholder="Photo">
            </div>
            <!-- MODIF PHOTO -->
            <?php if(!empty($photo)): ?>
                <div class="mt-4">
                    <p>Vous pouvez changer d'image
                        <img src="<?= URL . 'img/' . $photo ?>" width="50px">
                    </p>
                </div>
            <?php endif; ?>
            <input type="hidden" name="photo_actualle" value="<?php $photo ?>">
            <!-- MODULE PRIX -->
            <div class="col-md-4">
                <label class="form-label" for="prix"><div class="badge badge-dark text-wrap">Prix</div></label>
                <input class="form-control" type="text" name="prix" id="prix"  placeholder="Prix" value="<?= $prix?>">
            </div>
            <!-- MODULE STOCK -->
            <div class="col-md-4">
                <label class="form-label" for="stock"><div class="badge badge-dark text-wrap">Stock</div></label>
                <input class="form-control" type="text" name="stock" id="stock"  placeholder="Stock" value="<?= $stock?>">
            </div>
        </div>
        <!-- BUTTON VALIDER -->
        <div class="col-md-1 mt-5">
            <button type="submit" class="btn btn-outline-dark btn-warning">Valider</button>
        </div>
    </form>
<?php endif; ?>

<!-- NOMBRE DE PRODUITS EN BDD -->
<?php $nbProduit = $pdo->query("SELECT id_produit FROM produit");?>
<h2 class="py-5">Nombre de produits en base de données: <?= $nbProduit->rowCount();?></h2>

<!-- BUTTON AJOUT -->
<div class="row justify-content-center py-5">
    <a href='?action=add'>
        <button type="button" class="btn btn-sm btn-outline-dark shadow rounded">
        <i class="bi bi-plus-circle-fill"></i> Ajouter un produit
        </button>
    </a>
</div>

<!-- AFFICHAGE DE MON TABLEAU PRODUIT -->
<table class="table table-dark text-center">
    <?php $afficheProduits = $pdo->query("SELECT * FROM produit ORDER BY prix ASC LIMIT $parPage OFFSET $premierProduit");?>
    <thead>
        <tr>
            <?php for($i = 0; $i < $afficheProduits->columnCount(); $i++):
                $colonne = $afficheProduits->getColumnMeta($i); ?>
                <th><?= $colonne['name']?></th>
            <?php endfor ;?>
            <th colspan=2>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($produit = $afficheProduits->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
        <?php foreach($produit as $indice => $value): ?>
                <?php if($indice == 'prix'):?>
                    <td> <?php echo $value . '€' ?> </td>
                <?php elseif($indice == 'photo'):?>
                    <td><img src="<?php echo '../img/' . $value?>" class="img-fluid" width="50px"></td>
                <?php else:?>
                    <td> <?php echo $value ?> </td>
                <?php endif; ?>
                <?php endforeach; ?>
            <td><a href='?action=update&id_produit=<?= $produit['id_produit']?>'><i class="bi bi-pen-fill text-warning"></i></a></td>
            <td><a data-href="?action=delete&id_produit=<?= $produit['id_produit']?>" data-toggle="modal" data-target="#confirm-delete"><i class="bi bi-trash-fill text-danger" style="font-size: 1.5rem;"></i></a></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- AFFICHAGE DE MA NAV PAGINATION -->
<nav>
  <ul class="pagination justify-content-end">
    <li class="page-item <?= ($pageCourante == 1) ? 'disabled' : '' ?>">
        <a class="page-link text-dark" href="?page=<?=$pageCourante - 1 ?>" aria-label="Previous">
            <span aria-hidden="true">précédente</span>
            <span class="sr-only">Previous</span>
        </a>
    </li>
    <?php for($page = 1; $page <= $nombrePages; $page++): ?>
    <li class="mx-1 page-item">
        <a class="btn btn-outline-dark <?= ($pageCourante == $page) ? 'active' : '' ?>" href="?page=<?= $page ?>"><?= $page ?></a>
    </li>
    <?php endfor; ?>
    
    <li class="page-item <?= ($pageCourante == $nombrePages) ? 'disabled' : '' ?>">
        <a class="page-link text-dark" href="?page=<?=$pageCourante + 1 ?>" aria-label="Next">
            <span aria-hidden="true">suivante</span>
            <span class="sr-only">Next</span>
        </a>
    </li>
  </ul>
</nav>

<!-- MODAL SUP -->
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Supprimer article
            </div>
            <div class="modal-body">
                Etes-vous sur de vouloir retirer cet article de votre panier ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
                <a class="btn btn-danger btn-ok">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<!-- INCLUD FOOTER -->
<?php require_once('includeAdmin/footer.php'); ?>