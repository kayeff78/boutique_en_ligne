<?php 
require_once 'inc/init.inc.php';

// Si l'internaute N'EST PAS connecté / authentifié sur le site, il n'a rien à faire sur la page profil, on le redirige vers la page connexion.php
if(!connect())
{
    header('location: connexion.php');
}

// echo '<pre>'; print_r($_SESSION); echo '</pre>';

require_once 'inc/header.inc.php';
require_once 'inc/nav.inc.php';
?>

<!-- Tenter d'afficher 'Bonjour Pseudo' sur la page Web en passant par la session de l'utilisateur -->
<h1 class="text-center my-5">Bonjour <span class="text-success"><?= $_SESSION['user']['pseudo']; ?></span></h1>

<!-- Exo : réaliser une page profil affichant les données personnelle de l'utilisateur stockées dans le fichier de session, avec le design de votre choix -->
<div class="col-md-5 mx-auto card shadow-sm mb-5">
    <h5 class="card-header text-center">Vos données personnelle</h5>
    <div class="card-body">

        <?php 
        foreach($_SESSION['user'] as $key => $value): 
            if($key != 'id_user' && $key != 'sexe' && $key != 'statut'):
        ?>

            <p class="card-text d-flex justify-content-between">
                <!-- ucfirst : fonction prédéfinie permettant de mettre la 1ère lettre de la chaine de caractères en majuscule -->
                <strong><?= ucfirst($key); ?></strong>
                <span><?= $value ?></span>
            </p>

        <?php 
            endif;
        endforeach; 
        ?>

        <a href="#" class="btn btn-dark">Modifier</a>
    </div>
</div>

<?php 
require_once 'inc/footer.inc.php';