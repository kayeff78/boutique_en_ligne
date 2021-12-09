<?php 
require_once 'inc/init.inc.php';

if(!isset($_SESSION['tabIdProduit']))
{
    $_SESSION['tabIdProduit'] = [];
}

// Si l'indice 'action' est définit dans l'URL et qu'il a pour valeur 'deconnexion', cela veut dire que l'internaute a cliqué sur le lien de deconnexion et par conséquent qu'i;l a envoyé dans l'URL 'action=deconnexion'
if(isset($_GET['action']) && $_GET['action'] == 'deconnexion')
{
    // On vide le tableau ['user'] dans la session lorsque l'internaute clique sur le lien de deconnexion
    unset($_SESSION['user']);
}

// Si l'indice 'cat' est définit dans l'URL et que sa valeur est différente de vide
// On entre dans le IF si l'internaute sur un lien de catégorie
if(isset($_GET['cat']) && !empty($_GET['cat']))
{
    //                                                            sdgszgzgzgzegzegzeg
    $r = $bdd->prepare("SELECT * FROM article WHERE categorie = :categorie");
    $r->bindValue(':categorie', $_GET['cat'], PDO::PARAM_STR);
    $r->execute();

    // Si la requete de selection ne retourne aucun résultat, cela veut dire que la catégorie passer dans l'URL n'exsite pas en BDD, on redirige l'internaute vers la page boutique.php
    if(!$r->rowCount())
    {
        header('location: boutique.php');
    }

    // echo "Catégorie existante !";
}
else // Sinon, si il n'y pas de catégorie passée dans l'URL, on seelctionne l'ensemble de la table 'article'
{
    $r = $bdd->query("SELECT * FROM article");
}

require_once 'inc/header.inc.php';
require_once 'inc/nav.inc.php';
?>

    <h1 class="text-center my-5">Shopping</h1>

    <p class="my-5">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Delectus, labore. Dolor voluptatem nobis ea deleniti, sit possimus eligendi iure recusandae rem eius. Doloribus delectus quas, tempore rem laboriosam nesciunt pariatur velit, illum sint, necessitatibus ea eaque provident. Cupiditate alias repellat aliquid veniam quibusdam corrupti, non odit asperiores illo eligendi necessitatibus! Fugiat quo in provident minus ullam praesentium natus amet sequi delectus quia incidunt beatae rem, labore quisquam pariatur accusantium exercitationem enim suscipit consequatur dolorum animi commodi saepe? Eos quas, aliquid blanditiis officia ipsum natus ea. Porro officiis qui totam unde dignissimos nesciunt repudiandae possimus numquam pariatur placeat! Magnam et aperiam hic officiis? Veniam, laborum voluptate nemo, qui tempore voluptates sed at, suscipit facere sint totam eos beatae nam aperiam molestiae! Asperiores non officia cupiditate itaque sapiente fuga earum illo quibusdam? Adipisci quia aliquid laboriosam saepe, dignissimos eos expedita molestiae quaerat nisi quae ratione provident, optio ad. Recusandae iure hic culpa!</p>

    <!-- 
    Exo : 
    1. réaliser le traitement php + sql permettant de selectionner les catégories d'article  distincte dans la BDD
    2. Afficher dynamiquement les catégories dans l'accordéon ci-dessous (boucle + fetch)
    3. Faites en sorte d'envoyer le nom de catégorie dans l'URL lorsque l'on clique sur le lien
    -->

    <div class="accordion col-12 col-sm-10 col-md-4 col-lg-3 col-xl-3 mx-auto my-5" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                Catégories
                </button>
            </h2>

            <?php

                // SANS EXECUTION FETCH_ASSCO
                // PDOStatement Object
                // (
                //     [queryString] => SELECT DISTINCT categorie FROM article
                // )

                // RESULTAT APRES EXECUTION DU FETCH
                // Array
                // (
                //     [categorie] => tee-shirt
                // )
            
                // Array          
                // (
                //     [categorie] => pull
                // )

                // Array            
                // (
                //     [categorie] => manteau
                // )

            $data = $bdd->query("SELECT DISTINCT categorie FROM article");
            // echo '<pre>'; print_r($data); echo '</pre>';
            ?>

            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                <?php while($cat = $data->fetch(PDO::FETCH_ASSOC)):     
                    // echo '<pre>'; print_r($cat); echo '</pre>';
                ?>

                    <p><a href="?cat=<?= $cat['categorie'] ?>" class="alert-link text-dark"><?= ucfirst($cat['categorie']); ?></a></p>
                
                <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">

        <?php while($product = $r->fetch(PDO::FETCH_ASSOC)): 
            // echo '<pre>'; print_r($product); echo '</pre>';    
        ?>

            <div class="col">
                <div class="card shadow-sm rounded">
                    <a href="fiche_produit.php?id_article=<?= $product['id_article']; ?>">
                        <img src="<?= $product['photo']; ?>" class="card-img-top" alt="<?= $product['titre']; ?>">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title text-center">
                            <a href="fiche_produit.php?id_article=<?= $product['id_article']; ?>" class="alert-link text-dark titre-produit-boutique"><?= $product['titre']; ?></a>
                        </h5>
                        <p class="card-text"><?= substr($product['description'], 0, 100); ?>[...]</p>
                        <p class="card-text fw-bold"><?= $product['prix']; ?>€</p>
                        <p class="card-text text-center">
                            <a href="fiche_produit.php?id_article=<?= $product['id_article']; ?>" class="btn btn-outline-dark">En savoir plus</a>
                        </p>
                    </div>
                </div>
            </div>

        <?php endwhile; ?>

    </div>

<?php 
require_once 'inc/footer.inc.php';       