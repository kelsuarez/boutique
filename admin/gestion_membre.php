<?php
require_once('../include/init.php');

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

$queryProduit = $pdo->query("SELECT COUNT(id_membre) AS nombreMembre FROM membre");
$resultatMembres = $queryProduit->fetch();
$nombreMembre = (int) $resultatMembres['nombreMembre'];

// echo debug($nombreProduit);

$parPage = 5;

$nombrePages = ceil($nombreMembre / $parPage);

$premierMembre = ($pageCourante - 1) * $parPage;






// au préalable, pour introduire le formulaire, je vérifie que j'ai reçu dans l'URL un indice action. Ca permettra de ne pas répéter plusieurs fois cette vérification dans tout le traitement du formulaire qui va suivre
if(isset($_GET['action'])){
    if($_POST){
        if(!isset($_POST['pseudo']) || !preg_match('#^[a-zA-Z0-9-_.]{3,20}$#', $_POST['pseudo'])){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format pseudo !</div>';
        }
        if($_GET['action'] == 'add'){
            $verifPseudo = $pdo->prepare("SELECT pseudo FROM membre WHERE pseudo = :pseudo ");
            $verifPseudo->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
            $verifPseudo->execute();

            if($verifPseudo->rowCount() == 1){
                $erreur .= '<div class="alert alert-danger" role="alert">Erreur, ce pseudo existe déjà, vous devez en choisir un autre !</div>';
            }
            if(!isset($_POST['mdp']) || strlen($_POST['mdp']) < 3 || strlen($_POST['mdp']) > 20 ){
                $erreur .= '<div class="alert alert-danger" role="alert">Erreur format mdp !</div>';
            }
            $_POST['mdp'] = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
        }
        if(!isset($_POST['nom']) || iconv_strlen($_POST['nom']) < 3 || iconv_strlen($_POST['nom']) > 20 ){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format nom !</div>';
        }
        if(!isset($_POST['prenom']) || iconv_strlen($_POST['prenom']) < 3 || iconv_strlen($_POST['prenom']) > 20 ){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format prénom !</div>';
        }
        if(!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format email !</div>';
        }
        if(!isset($_POST['civilite']) || $_POST['civilite'] != 'femme' && $_POST['civilite'] != 'homme' ){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format civilité !</div>';
        }
        if(!isset($_POST['ville']) || strlen($_POST['ville']) < 2 || strlen($_POST['ville']) > 30 ){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format ville !</div>';
        }
        if(!isset($_POST['code_postal']) || !preg_match('#^[0-9]{5}$#', $_POST['code_postal'])){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format code postal !</div>';
        }
        if(!isset($_POST['adresse']) || strlen($_POST['adresse']) < 5 || strlen($_POST['adresse']) > 50 ){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format adresse !</div>';
        }
        if(empty($erreur)){
            // si dans l'URL action == update, on entame une procédure de modification
            if($_GET['action'] == 'update'){
                $modifUser = $pdo->prepare("UPDATE membre SET id_membre = :id_membre, pseudo = :pseudo, nom = :nom, prenom = :prenom, email = :email, civilite = :civilite, ville = :ville, code_postal = :code_postal, adresse = :adresse WHERE id_membre = :id_membre");
                $modifUser->bindValue(':id_membre', $_POST['id_membre'], PDO::PARAM_INT);
                $modifUser->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                $modifUser->bindValue(':nom', $_POST['nom'], PDO::PARAM_STR);
                $modifUser->bindValue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
                $modifUser->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
                $modifUser->bindValue(':civilite', $_POST['civilite'], PDO::PARAM_STR);
                $modifUser->bindValue(':ville', $_POST['ville'], PDO::PARAM_STR);
                $modifUser->bindValue(':code_postal', $_POST['code_postal'], PDO::PARAM_INT);
                $modifUser->bindValue(':adresse', $_POST['adresse'], PDO::PARAM_STR);
                $modifUser->execute();

                $queryUser = $pdo->query("SELECT pseudo FROM membre WHERE id_membre = '$_GET[id_membre]'");
                    $user = $queryUser->fetch(PDO::FETCH_ASSOC);
                    $content .= '<div class="alert alert-success alert-dismissible fade show mt-5" role="alert">
                                    <strong>Félicitations !</strong> Modification de l\'utilisateur' . $user['pseudo'] .  'est réussie 😉!
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>';
            }else{
                // si on récupère autre chose que update (et donc add) on entame une procédure d'insertion en BDD
                $inscrireUser = $pdo->prepare(" INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, ville, code_postal, adresse) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, :ville, :code_postal, :adresse) ");
                $inscrireUser->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                $inscrireUser->bindValue(':mdp', $_POST['mdp'], PDO::PARAM_STR);
                $inscrireUser->bindValue(':nom', $_POST['nom'], PDO::PARAM_STR);
                $inscrireUser->bindValue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
                $inscrireUser->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
                $inscrireUser->bindValue(':civilite', $_POST['civilite'], PDO::PARAM_STR);
                $inscrireUser->bindValue(':ville', $_POST['ville'], PDO::PARAM_STR);
                $inscrireUser->bindValue(':code_postal', $_POST['code_postal'], PDO::PARAM_INT);
                $inscrireUser->bindValue(':adresse', $_POST['adresse'], PDO::PARAM_STR);
                $inscrireUser->execute();
            }
        }
    }
    // procédure de récupération des infos en BDD pour les afficher dans le formulaire lorsque on fait un update (plus practique et plus sur)
    if($_GET['action'] == 'update'){
        $tousUser = $pdo->query("SELECT * FROM membre WHERE id_membre = '$_GET[id_membre]'");
        $userActuel = $tousUser->fetch(PDO::FETCH_ASSOC);

    }
    $idMembre = (isset($userActuel['id_membre'])) ? $userActuel['id_membre'] : "";
    $pseudo = (isset($userActuel['pseudo'])) ? $userActuel['pseudo'] : "";
        // syntaxe de condition classique équivalente à la ternaire juste au dessus
        // if(isset($userActuel['pseudo'])){
        //     $pseudo = $userActuel['pseudo']
        // }else{
        //     $pseudo = "";
        // }
    $email = (isset($userActuel['email'])) ? $userActuel['email'] : "";
    $nom = (isset($userActuel['nom'])) ? $userActuel['nom'] : "";
    $prenom = (isset($userActuel['prenom'])) ? $userActuel['prenom'] : "";
    $civilite = (isset($userActuel['civilite'])) ? $userActuel['civilite'] : ""; 
    $ville = (isset($userActuel['ville'])) ? $userActuel['ville'] : "";
    $codePostal = (isset($userActuel['code_postal'])) ? $userActuel['code_postal'] : "";
    $adresse = (isset($userActuel['adresse'])) ? $userActuel['adresse'] : "";

    if($_GET['action'] == 'delete'){
        $pdo->query("DELETE FROM membre WHERE id_membre = '$_GET[id_membre]'");
    }
}


require_once('includeAdmin/header.php');

?>

<h1 class="text-center my-5"><div class="badge badge-warning text-wrap p-3">Gestion des utilisateurs</div></h1>
<?php echo $erreur ?>
<?php echo $content ?>
<!-- le formulaire et son h2 n'apparaissent que si on en a fait la demande a partir du tableau des données. Sinon, c'est le tableau uniquement qui s'affiche, plus pratique pour l'admin -->
<?php if(isset($_GET['action'])): ?>
    <h2 class="my-5">Formulaire <?=($_GET['action'] == 'add') ? "d'ajout" : "de modification"?>  des utilisateurs</h2>
    <form class="my-5" method="POST" action="">

        <input type="hidden" name="id_membre" value="<?= $idMembre ?>">

        <div class="row">
            <div class="col-md-4 mt-5">
                <label class="form-label" for="pseudo"><div class="badge badge-dark text-wrap">Pseudo</div></label>
                <input class="form-control" type="text" name="pseudo" id="pseudo"  placeholder="Pseudo" value="<?= $pseudo?>">
            </div>
            <?php if($_GET['action'] == 'add'): ?>
                <div class="col-md-4 mt-5">
                    <label class="form-label" for="mdp"><div class="badge badge-dark text-wrap">Mot de passe</div></label>
                    <input class="form-control" type="password" name="mdp" id="mdp" placeholder="Mot de passe">
                </div>
            <?php endif; ?>
            <div class="col-md-4 mt-5">
                <label class="form-label" for="email"><div class="badge badge-dark text-wrap">Email</div></label>
                <input class="form-control" type="email" name="email" id="email"  placeholder="Email" value="<?= $email?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mt-5">
                <label class="form-label" for="nom"><div class="badge badge-dark text-wrap">Nom</div></label>
                <input class="form-control" type="text" name="nom" id="nom"  placeholder="Nom" value="<?= $nom?>">
            </div>
            <div class="col-md-4 mt-5">
                <label class="form-label" for="prenom"><div class="badge badge-dark text-wrap">Prénom</div></label>
                <input class="form-control" type="text" name="prenom" id="prenom"  placeholder="Prénom" value="<?= $prenom?>">
            </div>
            <div class="col-md-4 mt-4">
                <p><div class="badge badge-dark text-wrap">Civilité</div></p>

                <input type="radio" name="civilite" id="civilite1" value="femme" <?= ($civilite == "femme") ? 'checked' : "" ?>>
                <label class="mx-2" for="civilite1">Femme</label>

                <input type="radio" name="civilite" id="civilite2" value="homme" <?= ($civilite == "homme") ? 'checked' : "" ?>>
                <label class="mx-2" for="civilite2">Homme</label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mt-5">
                <label class="form-label" for="ville"><div class="badge badge-dark text-wrap">Ville</div></label>
                <input class="form-control" type="text" name="ville" id="ville"  placeholder="Ville" value="<?= $ville?>">
            </div>
            <div class="col-md-4 mt-5">
                <label class="form-label" for="code_postal"><div class="badge badge-dark text-wrap">Code Postal</div></label>
                <input class="form-control" type="text" name="code_postal" id="code_postal"  placeholder="Code postal" value="<?= $codePostal?>">
            </div>
            <div class="col-md-4 mt-5">
                <label class="form-label" for="adresse"><div class="badge badge-dark text-wrap">Adresse</div></label>
                <input class="form-control" type="text" name="adresse" id="adresse"  placeholder="Adresse" value="<?= $adresse?>">
            </div>
        </div>
        <div class="col-md-1 mt-5">
            <button type="submit" class="btn btn-outline-dark btn-warning">Valider</button>
        </div>
    </form>
<?php endif; ?>

<!-- requete SQL pour récupérer le nb d'utilisateurs inscrits en BDD, nb que je pourrais afficher grace à rowCount deux lignes en dessous -->
<?php $nbUser = $pdo->query("SELECT id_membre FROM membre");?>
<h2 class="py-5">Nombre d'utilisateurs en base de données: <?= $nbUser->rowCount();?></h2>

<div class="row justify-content-center py-5">
    <a href='?action=add'>
        <button type="button" class="btn btn-sm btn-outline-dark shadow rounded">
        <i class="bi bi-plus-circle-fill"></i> Ajouter un utilisateur...
        </button>
    </a>
</div>

<table class="table table-dark text-center">
        <?php $afficheUsers = $pdo->query("SELECT * FROM membre ORDER BY pseudo ASC LIMIT $parPage OFFSET $premierMembre");?>
        <thead>
            <tr>
                <?php for($i = 0; $i < $afficheUsers->columnCount(); $i++):
                    $colonne = $afficheUsers->getColumnMeta($i); ?>
                    <?php if($colonne['name'] != 'mdp'): ?>
                    <th><?= $colonne['name']?></th>
                    <?php endif; ?>
                <?php endfor ;?>
                <th colspan=2>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($user = $afficheUsers->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <?php foreach($user as $indice => $value): ?>
                <?php if($indice != 'mdp'): ?>
                <td> <?php echo $value ?> </td>
                <?php endif; ?>
                <?php endforeach; ?>
                <td><a href='?action=update&id_membre=<?= $user['id_membre']?>'><i class="bi bi-pen-fill text-warning"></i></a></td>
                <td><a data-href="?action=delete&id_membre=<?= $user['id_membre']?>" data-toggle="modal" data-target="#confirm-delete"><i class="bi bi-trash-fill text-danger" style="font-size: 1.5rem;"></i></a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

<nav>
  <ul class="pagination justify-content-end">
    <li class="page-item <?= ($pageCourante == 1) ? 'disabled' : '' ?>">
        <a class="page-link text-dark" href="?page=<?=$pageCourante - 1 ?>" aria-label="Previous">
            <span aria-hidden="true">précédente</span>
            <span class="sr-only">Previous</span>
        </a>
    </li>
    <?php for($page = 1; $page <= $nombrePages; $page++): ?>
        <li class="mx-1 page-item <?= ($pageCourante == $page) ? 'active' : '' ?>">
            <a class="btn btn-outline-dark " href="?page=<?= $page ?>"><?= $page ?></a>
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

<!-- <td><a href=''><i class="bi bi-pen-fill text-warning"></i></a></td>-->
<!-- <td><a data-href="" data-toggle="modal" data-target="#confirm-delete"><i class="bi bi-trash-fill text-danger" style="font-size: 1.5rem;"></i></a></td> -->

<!-- modal suppression codepen https://codepen.io/lowpez/pen/rvXbJq -->

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Supprimer Utilisateur
            </div>
            <div class="modal-body">
                Etes-vous sur de vouloir retirer cet utilisateur de votre base de données ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
                <a class="btn btn-danger btn-ok">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<!-- modal -->

<!-- pour empecher la modale de s'ouvrir à chaque rafraichissement de page, le temps de terminer de coder cette page -->
<?php if(!isset($_GET['action']) && !isset($_GET['page'])):?>
    <div class="modal fade" id="myModalUsers" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title text-warning" id="exampleModalLabel">Gestion des utilisateurs</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <p>Gérez ici votre base de données des utilisateurs</p>
            <p>Vous pouvez modifier leurs données, ajouter ou supprimer un utilisateur</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-warning text-dark" data-dismiss="modal">Fermer</button>
        </div>
        </div>
    </div>
    </div>
<?php endif; ?>

<?php require_once('includeAdmin/footer.php'); ?>