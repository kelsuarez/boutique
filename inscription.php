<?php
require_once('include/init.php');

// redirection de l'internaute déjà connecté, il n'a rien a faire sur une page incription (on le redirige vers sa page profil grace à header(location))
if(internauteConnecte()){
    header('location:' . URL . 'profil.php');
}

// tout le controle des inputs et la procédure d'envoi de données en BDD devra etre codé dans cette condition
if($_POST){

    // controle du pseudo avec un preg_match
    if(!isset($_POST['pseudo']) || !preg_match('#^[a-zA-Z0-9-_.]{3,20}$#', $_POST['pseudo'])){
        $erreur .= '<div class="alert alert-danger" role="alert">Erreur format pseudo !</div>';
    }

    // controle du champs mot de passe sur la longueur autorisée avec strlen
    if(!isset($_POST['mdp']) || strlen($_POST['mdp']) < 3 || strlen($_POST['mdp']) > 20 ){
        $erreur .= '<div class="alert alert-danger" role="alert">Erreur format mdp !</div>';
    }

    // controle du nom toujours sur la longueur autorisée, cette fois avec iconv_strlen (nep as pénaliser les scandinaves et asiatiques)
    if(!isset($_POST['nom']) || iconv_strlen($_POST['nom']) < 3 || iconv_strlen($_POST['nom']) > 20 ){
        $erreur .= '<div class="alert alert-danger" role="alert">Erreur format nom !</div>';
    }

    if(!isset($_POST['prenom']) || iconv_strlen($_POST['prenom']) < 3 || iconv_strlen($_POST['prenom']) > 20 ){
        $erreur .= '<div class="alert alert-danger" role="alert">Erreur format prénom !</div>';
    }

    // controle de l'email avec filter_var
    if(!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        $erreur .= '<div class="alert alert-danger" role="alert">Erreur format email !</div>';
    }

    // controle de la civilité en vérifiant que la valeur receptionnée n'est pas différente de femme ou homme (attention, les séparer d'un && et non pas OR, c'est obligatoire pour les boutons radios, checkbox ou sélecteurs)
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

    // va vérifier que le pseudo entré n'existe pas déjà en BDD. C'est impossible d'en avoir deux similaires, car il doit rester unique pour des raison d'authentification
    $verifPseudo = $pdo->prepare("SELECT pseudo FROM membre WHERE pseudo = :pseudo ");
    $verifPseudo->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
    $verifPseudo->execute();

    // le résultat de notre requete ne doit pas = 1, cela voudrait dire qu'une entrée possède déjà ce pseudo, et dans ce cas on génère un message d'erreur
    if($verifPseudo->rowCount() == 1){
        $erreur .= '<div class="alert alert-danger" role="alert">Erreur, ce pseudo existe déjà, vous devez en choisir un autre !</div>';
    }

    // Avec les RGPD, il faut hasher le mot de passe avant de l'envoyer en BDD
    // on utilise la fonction prédéfinie password_hash avec son algorithme PASSWORD_DEFAULT
    $_POST['mdp'] = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

    //  si aucun message d'erreur n'a été généré, c'est que l'utilisateur ne s'est pas trompé en remplissant le formulaire, on peut enclencher la procédure d'envoi en BDD
    if(empty($erreur)){
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

require_once('include/header.php');
?>


<h2 class="text-center py-5"><div class="badge badge-dark text-wrap p-3">Inscription</div></h2>

<?= echo $erreur ?>

<!-- $erreur .= '<div class="alert alert-danger" role="alert">Erreur format pseudo !</div>'; -->

<form class="my-5" method="POST" action="">

    <div class="row">
        <div class="col-md-4 mt-5">
        <label class="form-label" for="pseudo"><div class="badge badge-dark text-wrap">Pseudo</div></label>
        <input class="form-control btn btn-outline-success" type="text" name="pseudo" id="pseudo" placeholder="Votre pseudo" max-length="20" pattern="[a-zA-Z0-9-_.]{3,20}" title="caractères acceptés: majuscules et minuscules, chiffres, signes tels que: - _ . , entre trois et vingt caractères." required>
        </div>

        <div class="col-md-4 mt-5">
        <label class="form-label" for="mdp"><div class="badge badge-dark text-wrap">Mot de passe</div></label>
        <input class="form-control btn btn-outline-success" type="password" name="mdp" id="mdp" placeholder="Votre mot de passe" required>
        </div>
        
        <div class="col-md-4 mt-5">
        <label class="form-label" for="email"><div class="badge badge-dark text-wrap">Email</div></label>
        <input class="form-control btn btn-outline-success" type="email" name="email" id="email" placeholder="Votre email" required>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mt-5">
        <label class="form-label" for="nom"><div class="badge badge-dark text-wrap">Nom</div></label>
        <input class="form-control btn btn-outline-success" type="text" name="nom" id="nom" placeholder="Votre nom">
        </div>

        <div class="col-md-4 mt-5">
        <label class="form-label" for="prenom"><div class="badge badge-dark text-wrap">Prénom</div></label>
        <input class="form-control btn btn-outline-success" type="text" name="prenom" id="prenom" placeholder="Votre prénom">
        </div>

        <div class="col-md-4 mt-5 pt-2">
        <p><div class="badge badge-dark text-wrap">Civilité</div></p> 
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="civilite" id="civilite1" value="femme">
                <label class="form-check-label mx-2" for="civilite1">Femme</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="civilite" id="civilite2" value="homme" checked>
                <label class="form-check-label mx-2" for="civilite2">Homme</label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mt-5">
            <label class="form-label" for="ville"><div class="badge badge-dark text-wrap">Ville</div></label>
            <input class="form-control btn btn-outline-success" type="text" name="ville" id="ville" placeholder="Votre ville">
        </div>

        <div class="col-md-4 mt-5">
            <label class="form-label" for="code_postal"><div class="badge badge-dark text-wrap">Code Postal</div></label>
            <input class="form-control btn btn-outline-success" type="text" name="code_postal" id="code_postal" placeholder="Votre code postal">
        </div>

        <div class="col-md-4 mt-5">
            <label class="form-label" for="adresse"><div class="badge badge-dark text-wrap">Adresse</div></label>
            <input class="form-control btn btn-outline-success" type="text" name="adresse" id="adresse" placeholder="Votre adresse">
        </div>
    </div>

    <div class="col-md-1 mt-5">
    <button type="submit" class="btn btn-lg btn-outline-success">Valider</button>
    </div>
    
</form>

<?php require_once('include/footer.php') ?>