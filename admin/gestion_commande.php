<!-- OUVERTURE DE MON PASSAGE PHP -->
<?php

// INCLUDE INIT
require_once('../include/init.php');

// RENVOIE DE USER QUI SOIT PAS L'ADMIN
if(!internauteConnecteAdmin()){
    header('location:' . URL . 'connexion.php' );
    exit();
}

// REQUETTE GET POUR TRAVAIILLER SUR L'URL
if(isset($_GET['action'])){

    // REQUETTE DE CONTRAINTES
    if($_POST){
        // MONTANT
        if(!isset($_POST['montant']) || !preg_match('#^[0-9]{0,5}$#', $_POST['montant'])){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format code montant !</div>';
        }
        // ETAT
        if(!isset($_POST['etat']) || $_POST['etat'] != 'en cours' && $_POST['etat'] != 'envoyé' && $_POST['etat'] != 'livré'){
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format etat !</div>';
        }

        // REQUETTE DE SI PAS DE ERREUR ON PEUT CONTINUER
        if(empty($erreur)){

            // REQUETTE DE UPDATE
            if($_GET['action'] == 'update'){
                $modifCommande = $pdo->prepare("UPDATE commande SET id_commande = :id_commande, montant = :montant, etat = :etat WHERE id_commande = :id_commande");
                $modifCommande->bindValue(':id_commande', $_POST['id_commande'], PDO::PARAM_INT);
                $modifCommande->bindValue(':montant', $_POST['montant'], PDO::PARAM_INT);
                $modifCommande->bindValue(':etat', $_POST['etat'], PDO::PARAM_STR);
                $modifCommande->execute();

                // MESSAGE DE VALIDATION A L'ENVOI
                $queryCommande = $pdo->query("SELECT id_commande FROM commande WHERE id_commande = '$_GET[id_commande]'");
                    $commande = $queryCommande->fetch(PDO::FETCH_ASSOC);
                    $content .= '<div class="alert alert-success alert-dismissible fade show mt-5" role="alert">
                                    <strong>Félicitations !</strong> Modification de la commande ' . $commande['id_commande'] .  ' est réussie 😉!
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>';
            }else{
                // REQUETTE DE INSERTION A LA BDD

                // on peut que modifier une commande, le rajout d'une commande est interdit
            }
        }
        
    }

    // REQUETTE DE UPDATE
    if($_GET['action'] == 'update'){
        $detailleCommande = $pdo->query("SELECT * FROM commande WHERE id_commande = '$_GET[id_commande]'");
        $commandeActuel = $detailleCommande->fetch(PDO::FETCH_ASSOC);
    }

    // REQUETTE DE RECUPERATION DE BDD PAR APPORT A LES DONNES EN FORM
    $id_commande = (isset($commandeActuel['id_commande'])) ? $commandeActuel['id_commande'] : "";
    $montant = (isset($commandeActuel['montant'])) ? $commandeActuel['montant'] : "";
    $etat = (isset($commandeActuel['etat'])) ? $commandeActuel['etat'] : "";

    // REQUETTE POUR DELETE
    if($_GET['action'] == 'delete'){
        $pdo->query("DELETE FROM commande WHERE id_commande = '$_GET[id_commande]'");
    }
}

// INCLUDE HEADER
require_once('includeAdmin/header.php');

// FERMETURE DE MON PASSAGE PHP
?>

<!-- TITRE H1 -->             
<h1 class="text-center my-5"><div class="badge badge-warning text-wrap p-3">Gestion des commandes</div></h1>

<!-- MODULE POUR MONTRER LES ERREURS ET LE CONTENU -->
<?= $content ?>
<?= $erreur ?>

<!-- POPUP -->
<div class="modal fade" id="myModalCommand" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-warning" id="exampleModalLabel">Gestion des commandes</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Vous ne pourrez modifier que son montant (pour une réduction, par exemple) ou son état (selon son avancement)</p>
        <p>Vous ne pourrez ajouter une commande. Par contre la suppression sera possible, supprimant par la même occasion les détails de cette commande</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-warning text-dark" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<!-- FORMULAIRE -->
<?php if(isset($_GET['action'])): ?>
    <h2 class="my-2">Formulaire des commandes</h2>

    <form method="POST"action="">

        <!-- INPUT HIDDEN -->
        <input type="hidden" name="id_commande" value="<?= $id_commande ?>">
        <input type="hidden" name="id_membre" value="<?= $id_commande ?>">

        <!-- MODULE MONTANT -->
        <div class="col-md-3 mt-5">
            <label class="form-label" for="montant"><div class="badge badge-dark text-wrap">Montant</div></label>
            <input type="text" class="form-control" name="montant" id="montant"  placeholder="Montant" value="<?= $montant?>">
        </div>

        <!-- MODULE ETAT -->
        <div class="col-md-4 mt-5">
            <p><div class="badge badge-dark text-wrap">Etat de la livraison</div></p>
            <input type="radio" name="etat" id="etat1" value="en cours" <?= ($etat == "en cours") ? 'checked' : "" ?>>
            <label class="mx-2" for="etat1"><div class="badge badge-danger text-wrap">En cours</div></label>
            <input type="radio" name="etat" id="etat2" value="envoyé" <?= ($etat == "envoyé") ? 'checked' : "" ?>>
            <label class="mx-2" for="etat2"><div class="badge badge-warning text-wrap">Envoyé</div></label>
            <input type="radio" name="etat" id="etat3" value="livré" <?= ($etat == "livré") ? 'checked' : "" ?>> 
            <label class="mx-2" for="etat3"><div class="badge badge-success text-wrap">Livré</div></label>
        </div>

        <!-- BUTTON VALIDER -->
        <div class="col-md-1 mt-5">
            <button type="submit" class="btn btn-outline-dark shadow rounded">Valider</button>
        </div>

    </form>
<?php endif; ?>

<!-- NOMBRE DE COMMANDES EN BDD -->
<?php $nbCommandes = $pdo->query("SELECT id_commande FROM commande");?>
<h2 class="py-5">Nombre de commandes en base de données: <?= $nbCommandes->rowCount();?></h2>

<!-- AFFICHAGE DE MON TABLEAU COMMANDE -->
<table class="table table-dark text-center">
    <?php $afficheCommande = $pdo->query("SELECT * FROM commande ORDER BY id_commande DESC");?>
    <thead>
        <tr>
            <?php for($i = 0; $i < $afficheCommande->columnCount(); $i++):
                $colonne = $afficheCommande->getColumnMeta($i); ?>
                <th><?= $colonne['name']?></th>
            <?php endfor ;?>
            <th colspan=2>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($commande = $afficheCommande->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <?php foreach($commande as $indice => $value): ?>
                <?php if($indice == 'montant'):?>
                    <td> <?php echo $value . '€' ?> </td>
                <?php else:?>
                    <td> <?php echo $value ?> </td>
                <?php endif; ?>
            <?php endforeach; ?>
            <td><a href='?action=update&id_commande=<?= $commande['id_commande']?>'><i class="bi bi-pen-fill text-warning"></i></a></td>
            <td><a data-href="?action=delete&id_commande=<?= $commande['id_commande']?>" data-toggle="modal" data-target="#confirm-delete"><i class="bi bi-trash-fill text-danger" style="font-size: 1.5rem;"></i></a></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- AFFICHAGE DE MA NAV -->
<nav>
  <ul class="pagination justify-content-end">
    <li class="page-item ">
        <a class="page-link text-dark" href="" aria-label="Previous">
            <span aria-hidden="true">précédente</span>
            <span class="sr-only">Previous</span>
        </a>
    </li>
    
        <li class="mx-1 page-item">
            <a class="btn btn-outline-dark " href=""></a>
        </li>
    
    <li class="page-item ">
        <a class="page-link text-dark" href="" aria-label="Next">
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