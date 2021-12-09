<?php 
require_once 'inc/init.inc.php';

// echo '<pre>'; print_r($_GET); echo '</pre>';

// CREATION COOKIE
// On stock les id des articles consultés par l'internaute dans le fichier de session de l'utilisateur
if(isset($_SESSION['tabIdProduit']))
{
    array_push($_SESSION['tabIdProduit'], $_GET['id_article']);
}

// echo '<pre>'; print_r($_SESSION['tabIdProduit']); echo '</pre>';

// encodage en json des id_articles, il n'est pas possible de transmettre un array à un fichier cookie
// On peux seulement stock du texte ou un objet textuel dans le fichier cookie
$arrayJson = json_encode($_SESSION['tabIdProduit']);

// déclaration du cookie
$un_an = 24*365*3600;
setcookie('derniers_articles', $arrayJson, time()+$un_an);
// echo '<pre>'; print_r($_COOKIE); echo '</pre>';

// STOCKAGE EN SESSION DES ARTICLES AJOUTES AU PANIER
if(isset($_POST['quantite']))
{
    if(!is_numeric($_POST['quantite']))
    {
        $msg = 'Vous devez choisir une quantité.';
    }
    else 
    {
        $_SESSION['infoPanier'] = [
            'id_article' => $_POST['id_article'],
            'quantite' => $_POST['quantite']
        ];

        header('location: panier.php');
    }
}

// Si l'indice 'id_article' est définit dans l'URL et que sa valeur est différente de vide, on entre dans le IF et on execute la requete de selection
if(isset($_GET['id_article']) && !empty($_GET['id_article']))
{
    $data = $bdd->prepare("SELECT * FROM article WHERE id_article = :id_article");
    $data->bindValue(':id_article', $_GET['id_article'], PDO::PARAM_INT);
    $data->execute();

    // Si la requete de selection retourne 1 résultat, cela veut que l'article est stocké en BDD
    if($data->rowCount())
    {
        // echo "article stocké en BDD";
        // $product contient un Array avec toute les données de la BDD
        $product = $data->fetch(PDO::FETCH_ASSOC);
        // echo '<pre>'; print_r($product); echo '</pre>';
    }
    else // Sinon la requete ne retourne aucun résultat, l'article n'existe pas en BDD
    {
        // On redirige l'internaute vers la boutique si l'id_article passé dans l'URL n'exsite pas
        header('location: boutique.php');
        // echo "article inconnu en BDD";
    }
}
else // Sinon l'indice article n'est pas définit ou n'a pas de valeur, l'internaute a modifier les paramètres de l'url, on le redirige vers la page boutique.php
{
    header('location: boutique.php');
}

require_once 'inc/header.inc.php';
require_once 'inc/nav.inc.php';
?>

    <h1 class="text-center my-5">Détails de l'article</h1>

    <?php if(isset($msg)): ?>
        <p class="bg-danger col-md-4 mx-auto p-3 text-center text-white my-3"><?= $msg; ?></p>
    <?php endif; ?>

    <div class="row mb-5">
        <div class="bg-white shadow-sm rounded d-flex zone-card-fiche-produit">

            <a href="<?= $product['photo']; ?>" data-lightbox="image" data-title="<?= $product['titre'] ?>" data-alt="<?= $product['titre']; ?>" class="d-flex flex-column justify-content-center">

                <img src="<?= $product['photo']; ?>" class="img-produit-fiche" alt="<?= $product['titre']; ?>">
                <small class="text-info text-center fst-italic">Cliquer pour agrandir</small>

            </a>

            <div class="col-12 col-sm-12 col-md-12 col-lg-9 card-body d-flex flex-column justify-content-center zone-card-body">

                <h5 class="card-title text-center fw-bold my-3"><?= $product['titre']; ?></h5>

                <p class="card-text"><?= $product['description']; ?></p>

                <p class="card-text fw-bold">Taille : <?= strtoupper($product['taille']); ?></p>
                <p class="card-text fw-bold">Couleur : <?= $product['couleur']; ?></p>
                <p class="card-text fw-bold">Service : <?= $product['sexe']; ?></p>
                <p class="card-text fw-bold"><?= $product['prix']; ?>€</p>
                
                <?php if($product['stock'] > 0): ?>

                    <?php if($product['stock'] <= 10): ?>
                        <p class="card-text text-danger fst-italic fw-bold">Attention ! il ne reste que <strong><?= $product['stock'] ?></strong> exemplaire(s) en stock.</p>
                    <?php else: ?>
                        <p class="card-text text-success fst-italic fw-bold">En stock !</p>
                    <?php endif; ?>

                    <p class="card-text">

                        <!-- A la validation du formulaire, on redirige l'internaute vers la page panier (attribut 'action') et les données saisie dasn le formulaire seront accessible sur la page panier.php (quantité + id_article) -->
                        <form method="post" action="" class="row g-3">

                            <input type="hidden" id="id_article" name="id_article" value="<?= $product['id_article']; ?>">

                            <div class="col-12 col-sm-7 col-md-4 col-lg-3 col-xl-3">
                                <label class="visually-hidden" for="quantite">Quantité</label>
                                <select class="form-select" id="quantite" name="quantite">
                                    <option>Choisir une quantité...</option>
                                    <?php for($i = 1; $i <= 30 && $i <= $product['stock']; $i++): ?>

                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-sm">
                                <input type="submit" class="btn btn-dark" value="Ajouter au panier">
                            </div>
                        </form>
                    </p>

                <?php else: ?>

                    <p class="card-text text-danger fs-5 fst-italic fw-bold">Y'en avait mais y'en a plus !</p>

                <?php endif; ?>
            </div>
        </div>
        <p class="mt-1"><a href="boutique.php?cat=<?= $product['categorie']; ?>" class="text-dark alert-link"><i class="bi bi-arrow-left-circle-fill"></i> Retour à &laquo; <span><?= $product['categorie']; ?></span> &raquo;</a></p>
    </div>

<?php 
require_once 'inc/footer.inc.php';        