<?php 
require_once 'inc/init.inc.php';

if(!connect())
{
  header('location: connexion.php');
}
require_once 'inc/nav.inc.php';
require_once 'inc/header.inc.php';
?>


<h1 class="text-center my-5">FELICITATIONS !</h1>

<p class="text-center"> Votre commande n° <?= $_SESSION['num_commande'] ?> a bien été validée !</p>

<p class="text-center">Un mail de confirmation vous a bien été envoyé. </p>

<?php
require_once 'inc/footer.inc.php';