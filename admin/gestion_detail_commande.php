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

    // REQUETTE DE UPDATE
    if($_GET['action'] == 'update'){
        $detailleCommande = $pdo->query("SELECT * FROM details_commande WHERE id_details_commande = '$_GET[id_details_commande]'");
        $commandeActuel = $detailleCommande->fetch(PDO::FETCH_ASSOC);
    }

    // REQUETTE DE RECUPERATION DE BDD PAR APPORT A LES DONNES EN FORM
    $id_commande = (isset($commandeActuel['id_commande'])) ? $commandeActuel['id_commande'] : "";
    $id_produit = (isset($commandeActuel['id_produit'])) ? $commandeActuel['id_produit'] : "";
    $quantite = (isset($commandeActuel['quantite'])) ? $commandeActuel['quantite'] : "";
    $prix = (isset($commandeActuel['prix_unite'])) ? $commandeActuel['prix_unite'] : "";

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
<h1 class="text-center my-5"><div class="badge badge-warning text-wrap p-3">Gestion détail des commandes</div></h1>

<!-- MODULE POUR MONTRER LES ERREURS ET LE CONTENU -->
<?= $content ?>
<?= $erreur ?>

<!-- POPUP -->
<div class="modal fade" id="myModalDetailsCommand" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-warning" id="exampleModalLabel">Gestion des détails des commandes</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Visualisez ici votre base de données des détails de commande</p>
        <p>Aucune action n'est possible, ses données étant reliées a d'autres, cela entrainerait des dysfonctionnements</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-warning text-dark" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<!-- FORMULAIRE -->
<?php if(isset($_GET['action'])): ?>
    <h2 class="my-2">Formulaire  des commandes</h2>

    <form method="POST"action="">

        <!-- MODULE ID COMMANDE -->
        <div class="col-md-3 mt-5">
            <label class="form-label" for="id_commande"><div class="badge badge-dark text-wrap">Id Commande</div></label>
            <input type="text" disabled="disabled" class="form-control" name="id_commande" id="id_commande" placeholder="Id de la commande" value="<?= $id_commande?>">
        </div>

        <!-- MODULE ID PRODUIT -->
        <div class="col-md-3 mt-5">
            <label class="form-label" for="id_produit"><div class="badge badge-dark text-wrap">Id Produit</div></label>
            <input type="text" disabled="disabled" class="form-control" name="id_produit" id="id_produit" placeholder="Id du produit" value="<?= $id_produit?>">
        </div>

        <!-- MODULE QUANTITE -->
        <div class="col-md-3 mt-5">
            <label class="form-label" for="quantite"><div class="badge badge-dark text-wrap">Quantité</div></label>
            <input type="text" disabled="disabled" class="form-control" name="quantite" id="quantite" placeholder="Quantité" value="<?= $quantite?>">
        </div>

        <!-- MODULE PRIX -->
        <div class="col-md-3 mt-5">
            <label class="form-label" for="prix"><div class="badge badge-dark text-wrap">Prix</div></label>
            <input type="text" disabled="disabled" class="form-control" name="prix" id="prix" placeholder="Prix" value="<?= $prix?>">
        </div>

    </form>
<?php endif; ?>

<!-- NOMBRE DE COMMANDES EN BDD -->
<?php $nbCommandes = $pdo->query("SELECT id_details_commande FROM details_commande");?>
<h2 class="py-5">Nombre de commandes en base de données: <?= $nbCommandes->rowCount();?></h2>

<!-- AFFICHAGE DE MON TABLEAU PRODUIT -->
<table class="table table-dark text-center">
    <?php $affichageDetailsCommande = $pdo->query("SELECT * FROM details_commande ORDER BY id_details_commande DESC");?>
    <thead>
        <tr>
            <?php for($i = 0; $i < $affichageDetailsCommande->columnCount(); $i++):
                $colonne = $affichageDetailsCommande->getColumnMeta($i); ?>
                <th><?= $colonne['name']?></th>
            <?php endfor ;?>
            <th colspan=2>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($commande = $affichageDetailsCommande->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <?php foreach($commande as $indice => $value): ?>
                <?php if($indice == 'prix_unite'):?>
                    <td> <?php echo $value . '€' ?> </td>
                <?php else:?>
                    <td> <?php echo $value ?> </td>
                <?php endif; ?>
            <?php endforeach; ?>
            <td><a href='?action=update&id_details_commande=<?= $commande['id_details_commande']?>'><i class="bi bi-pen-fill text-warning"></i></a></td>
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