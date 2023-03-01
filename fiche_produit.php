<?php
require_once('include/init.php');

require_once('include/affichage.php');

// substr DANS CE CAS IL ME SERT A EFFACER LA TOUTE DERNIER LETTRE DE MES CATEGORIES
// SI J'AI JUPES ELLE DEVIENDRAI JUPE SANS LE S
$pageTitle = "Fiche " . substr($detail['categorie'],0,-1) . " " . $detail['titre'];

require_once('include/header.php');
?>

</div>

<div class="container-fluid">
    <div class="row">
        <!-- debut de la colonne qui va afficher les categories -->
        <div class="col-md-2">
        <?php while($menuCategorie = $afficheMenuCategorie->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="list-group text-center">
                <a class="btn btn-outline-success my-2" href="<?=URL?>?categorie=<?=$menuCategorie['categorie']?>"><?=$menuCategorie['categorie']?></a>
            </div>
            <?php endwhile; ?>
        </div>
        <!-- fin de la colonne catégories -->

    
        <div class="col-md-8">

            <h2 class='text-center my-5'>
                <div class="badge badge-dark text-wrap p-3">Fiche du produit <?= substr($detail['categorie'],0,-1) . " " . $detail['titre']; ?> </div>
            </h2>

            <div class="row justify-content-around text-center py-5">
                <div class="card shadow p-3 mb-5 bg-white rounded" style="width: 22rem;">
                    <img src="<?= URL . 'img/' . $detail['photo']?>" class="card-img-top" alt="image du produit <?=substr($detail['categorie'],0,-1) . " " . $detail['titre']?>">
                    <div class="card-body">
                        <h3 class="card-title"><div class="badge badge-dark text-wrap"><?=$detail['prix'] . " €"?></div></h3>
                        <p class="card-text"><?=$detail['description']?></p>

                        <?php if($detail['stock'] > 0 ): ?>
                        <form method="POST" action="panier.php">

                            <!-- INPUT HIDDEN -->
                            <input type="hidden" name="id_produit" value="<?= $detail['id_produit']?>">

                            <label for="quantite">J'en achète</label>
                            <select class="form-control col-md-5 mx-auto" name="quantite" id="quantite">
                                <!-- ON CREE UNE BOUCLE POUR ME MONTRER LES NOMBRE DE PIECES DE POSSIBLE -->
                                <?php for($quantite = 1; $quantite <= min($detail['stock'],5); $quantite++):?>
                                    <!-- EN VALUE JE MET LA VALEUR DE MON STOCK EN VALUE -->
                                    <option class="bg-dark text-light" value="<?=$quantite?>"><?=$quantite?></option>
                                <?php endfor; ?>
                            </select>
                            <button type="submit" class="btn btn-outline-success my-2" name="ajout_panier" value="ajout_panier"><i class="bi bi-plus-circle"></i> Panier <i class="bi bi-cart3"></i></button>
                        </form>
                        <?php else: ?>
                                <p class="card-text"><div class="badge badge-danger text-wrap p-3">Produit en rupture de stock</div></p>
                        <?php endif; ?>

                        <p>Voir tous les modèles <a href="<?=URL?>?categorie=<?=$detail['categorie']?>">de la même catégorie</a> ou <a href="<?=URL . '?public=' . $detail['public']?>">pour le même public</a></p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
<?php if(isset($_GET['produit'])): ?>
    <?php endif ?>
<div class="container">

<?php require_once('include/footer.php');
