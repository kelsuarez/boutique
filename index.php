<?php

// RECUPERATION INIT
require_once('include/init.php');

// RECUPERATION D'AFFICHAGE
require_once('include/affichage.php');

// RECUPERATION HEADER
require_once('include/header.php');
?>

</div>

    <div class="container-fluid">
        <div class="row my-5">

            <!-- MENU ASSIDE AVEC LES CATEGORIES  -->
            <div class="col-md-2">
                <div class="list-group text-center">
                <?php while($menuCategorie = $afficheMenuCategorie->fetch(PDO::FETCH_ASSOC)): ?>
                    <a class="btn btn-outline-success my-2" href="<?=URL?>?categorie=<?=$menuCategorie['categorie']?>"><?=$menuCategorie['categorie']?></a>
                <?php endwhile; ?>
                </div>
            </div>

            <!-- CONDITION POUR LE MENU ASSIDE AVEC LES CATEGORIES DES -->
            <?php if(isset($_GET['categorie'])): ?>
            <div class="col-md-8">
            
                <div class="text-center my-5">
                    <img class='img-fluid' src="img/la_boutique_bis.webp" alt="Bandeau de La Boutique" loading="lazy">
                </div>
                <!-- PHP MONTRER LE NOM DINAMIQUEMENT DE LE TITRE -->
                <div class="row justify-content-around">
                    <h2 class="py-5"><div class="badge badge-dark text-wrap">Nos categorie de <?= ucfirst($titreCategorie['categorie']) ?></div></h2>
                </div>
                <!-- PHP AFFICHAGE DE L'IMAGE DINAMIQUEMENT AVEC LES DESCRIPTION-->
                <div class="row justify-content-around text-center">
                    <?php while($categorie = $afficheProduits->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="card mx-3 shadow p-3 mb-5 bg-white rounded" style="width: 18rem;">
                            <!-- IMAGE PLUS ALT -->
                            <a href="fiche_produit.php?id_produit=<?= $categorie['id_produit']?>"><img src="<?= URL . 'img/' . $categorie['photo']?>" class="card-img-top" alt="Photo de <?=$categorie['titre']?>"></a>
                            <div class="card-body">
                                <!-- TITRE DE PRODUIT -->
                                <h3 class="card-title"><?= $categorie['titre']?></h3>
                                <!-- PRIX DE L'ARTICLE -->
                                <h3 class="card-title"><div class="badge badge-dark text-wrap"><?= $categorie['prix']?> €</div></h3>
                                <!-- DESCRIPTION DE L'ARTICLE -->
                                <p class="card-text"><?= $categorie['description']?></p>
                                <a href="fiche_produit.php?id_produit=<?= $categorie['id_produit']?>" class="btn btn-outline-success"><i class='bi bi-search'></i> Voir Produit</a>
                            </div>
                        </div>
                    <?php endwhile ?>
                </div>
                <!-- dans les 3 <a href> je dois faire référence à la catégorie, en plus de la page, sinon cela ne fonctionnera pas -->
                <nav aria-label="">
                    <ul class="pagination justify-content-end">
                        <li class="mx-1 page-item <?= ($pageCourante == 1) ? 'disabled' : '' ?>">
                            <a class="page-link text-success" href="?page=<?=$pageCourante - 1 ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>
                        <?php for($page = 1; $page <= $nombreCategorie; $page++): ?>
                            <li class="mx-1 page-item ">
                                <a class="btn btn-outline-success <?= ($pageCourante == $page) ? 'active' : '' ?>" href="?page=<?= $page ?>"><?= $page ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="mx-1 page-item <?= ($pageCourante == $nombrePages) ? 'disabled' : '' ?>">
                            <a class="page-link text-success" href="?page=<?=$pageCourante + 1 ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    </ul>
                </nav>
               
            </div>

            <!-- CONDITION POUR LE MENU NAV AVEC LES PUBLIC DES -->
            <?php elseif(isset($_GET['public'])): ?>
            <div class="col-md-8">
            
                <div class="text-center my-5">
                    <img class='img-fluid' src="img/la_boutique_bis.webp" alt="Bandeau de La Boutique" loading="lazy">
                </div>
                <!-- PHP MONTRER LE NOM DINAMIQUEMENT DE LE TITRE -->
                <div class="row justify-content-around">
                    <h2 class="py-5"><div class="badge badge-dark text-wrap">Nos vêtements <?= ucfirst($titrePublic['public']) ?>s</div>
                    </h2>
                </div>
                <!-- PHP AFFICHAGE DE L'IMAGE DINAMIQUEMENT AVEC LES DESCRIPTION-->
                <div class="row justify-content-around text-center">
                    <?php while($produit = $afficheProduits->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="card mx-3 shadow p-3 mb-5 bg-white rounded" style="width: 18rem;">
                            <!-- IMAGE PLUS ALT -->
                            <a href="fiche_produit.php?id_produit=<?= $produit['id_produit']?>"><img src="<?= URL . 'img/' . $produit['photo']?>" class="card-img-top" alt="Photo de <?=$produit['titre']?>"></a>
                            <div class="card-body">
                                <!-- TITRE DE PRODUIT -->
                                <h3 class="card-title"><?= $produit['titre']?></h3>
                                <!-- PRIX DE L'ARTICLE -->
                                <h3 class="card-title"><div class="badge badge-dark text-wrap"><?= $produit['prix']?> €</div></h3>
                                <!-- DESCRIPTION DE L'ARTICLE -->
                                <p class="card-text"><?= $produit['description']?></p>
                                <a href="fiche_produit.php?id_produit=<?= $produit['id_produit']?>" class="btn btn-outline-success"><i class='bi bi-search'></i> Voir Produit</a>
                            </div>
                        </div>
                    <?php endwhile ?>
                </div>
                
                <nav aria-label="">
                <!-- dans les 3 <a href> je dois faire référence à la catégorie, en plus de la page, sinon cela ne fonctionnera pas -->
                    <ul class="pagination justify-content-end">
                        <li class="mx-1 page-item  ">
                            <a class="page-link text-success" href="" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>
                        
                            <li class="mx-1 page-item ">
                                <a class="btn btn-outline-success " href=""></a>
                            </li>
                        
                        <li class="mx-1 page-item ">
                            <a class="page-link text-success" href="" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            
            </div>

            <!-- AFFICHAGE SI ON DEMANDE RIEN AUCUNE DES AUTRES CONDITIONS  -->
            <?php else: ?>
            <div class="col-md-8">
                <div class="row justify-content-around py-5">
                    <img class='img-fluid' src="img/la_boutique.webp" alt="Bandeau de La Boutique" loading="lazy">    
                </div>
            </div>

            <?php endif; ?>
        </div>
    </div>
    
<div class="container">

<?php require_once('include/footer.php');