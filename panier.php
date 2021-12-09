<?php 
require_once 'inc/init.inc.php';

// echo '<pre>'; print_r($_POST); echo '</pre>';

// On entre dans la condition IF seulement dans le cas où l'internaute a ajouté un article dans le panier
if(isset($_SESSION['infoPanier']['id_article'], $_SESSION['infoPanier']['quantite']))
{
    // On selectionne les données de l'article ajouté au panier en BDD
    $data = $bdd->prepare("SELECT * FROM article WHERE id_article = :id_article");
    $data->bindValue(':id_article', $_SESSION['infoPanier']['id_article'], PDO::PARAM_INT);
    $data->execute();

    $product = $data->fetch(PDO::FETCH_ASSOC);

    // echo '<pre>'; print_r($product); echo '</pre>';

    addPanier($product['id_article'], $product['photo'], $product['titre'], $_SESSION['infoPanier']['quantite'], $product['stock'], $product['prix']);

    unset($_SESSION['infoPanier']);
}   

//echo '<pre>'; print_r($_SESSION); echo '</pre>';

if(isset($_POST['payer']))
{
    // La boucle FOR tourne autant de fois que nous avons de produit dans le tableau $_SESSION['panier']['id_article'] dans la session
    for($i = 0; $i < count($_SESSION['panier']['id_article']); $i++)
    {
        $r = $bdd->query("SELECT * FROM article WHERE id_article = " . $_SESSION['panier']['id_article'][$i]);
        $product = $r->fetch(PDO::FETCH_ASSOC);
        echo '<pre>'; print_r($product); echo '</pre>';

        // Si le stock de l'article en BDD est strictement inférieur à la quantité demandée, on entre dans la condition IF
        if($product['stock'] < $_SESSION['panier']['quantite'][$i])
        {
            echo "stock en BDD : <span class='badge bg-success'>$product[stock]</span><br>";
            echo "Quantité demandée : <span class='badge bg-success'>" . $_SESSION['panier']['quantite'][$i] . "</span>";

            // Si le stock de la BDD est supérieur à 0 mais inférieur à la quantité demandée dans le panier, alors on entre dans le IF
            if($product['stock'] > 0)
            {
                // On affecte le stock restant en BDD au produit demandé dans le panier
                $_SESSION['panier']['quantite'][$i] = $product['stock'];

                $msg = "La quantité de l'article <strong> ". $_SESSION['panier']['titre'][$i] . "</strong> a été réduite car notre stock est insuffisant. Veuillez vérifier vos achats.";
            }
            else // Sinon le stock est à 0, rupture de stock en BDD
            {
                $msg = "L'article <strong> ". $_SESSION['panier']['titre'][$i] . "</strong> a été supprimé car nous sommes en rupture de stock.";

                // On supprime de la session panier, l'article ayant un stock à 0;
                deletePanier($_SESSION['panier']['id_article'][$i]);
                $i--; // on fait un tour en arrière de boucle afin de contrôler l'article qui est remonté d'1 indice dans le tableau de la session après l'execution du array_splice()
            }

            $error = true;
        }

        
    }

    // si la variable $error n'est pas définit, cela veut dire que la quantité en stock est suffisante par rapoort à la quantité dans le panier
    if(!isset($error))
        {
            $bdd->query("INSERT INTO commande (user_id, montant, date) VALUES(" . $_SESSION['user'] ['id_user'] . ", " . montantTotal(). ", NOW())");

            //lastINsertId() : permet de récupérer la derniere clé primaire inséree en BDD, ici le dernier id_commande afin de pouvoir l'inséerer dans la table 'details_commande' et de pouvoir relier chaque produit à la bonne commande
            $idCommande = $bdd ->lastINsertId();

            for($i=0 ; $i< count($_SESSION['panier']['id_article']); $i++)
            {
                $bdd ->query("INSERT INTO details_commande (commande_id, article_id, quantite, prix) VALUES ($idCommande, " .$_SESSION['panier']['id_article'][$i] . ", " .$_SESSION['panier']['quantite'][$i] .", ".$_SESSION['panier']['prix'][$i] .")");

                //depreciation des stocks
                $bdd->query("UPDATE article SET stock = stock - " . $_SESSION['panier']['quantite'][$i] . " WHERE id_article = " . $_SESSION['panier']['id_article'][$i]);
            }
            //On stock dans la session l'id_commande qui nous servira de numero de commande pour l'internaute
            $_SESSION['num_commande'] = $idCommande;

            //On vide le panier dans la session apres la validation de la commande
            unset($_SESSION['panier']);

            //Une fois la commande validée, on redirige l'internaute vers la page validation_commande.php
            header('location: validation_commande.php');
        }
}

require_once 'inc/header.inc.php';
require_once 'inc/nav.inc.php';
?>

    <h1 class="text-center my-5">Votre panier</h1>

    <?php if(isset($msg)): ?>
        <p class="bg-success col-md-6 mx-auto p-3 text-center text-white my-3"><?= $msg; ?></p>
    <?php endif; ?>

    <!-- Si le tableau ARRAY ['id_article'] dans la session n'est pas vide, cela veut dire que l'internaute a ajouté des articles dans le panier de la session -->
    <?php if(!empty($_SESSION['panier']['id_article'])): ?>

        <!-- La boucle FOR tourne autant de fois que nous avons de produit dans le tableau $_SESSION['panier']['id_article'] dans la session  -->
        <!--                   3          -->
        <?php for($i = 0; $i < count($_SESSION['panier']['id_article']); $i++): ?>

            <div class="container col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8 mx-auto d-flex justify-content-center shadow-sm px-0 mb-2">

                <div class="col-md-2 bg-white p-2">
                    <!-- $_SESSION['panier']['photo'][0]    -->
                    <a href="fiche_produit.php?id_article=<?= $_SESSION['panier']['id_article'][$i]; ?>"><img src="<?= $_SESSION['panier']['photo'][$i]; ?>" alt="produit 1" class="img-panier"></a>
                </div>

                <div class="col-md-7 bg-white d-flex flex-column justify-content-center p-2">
                    <h4><a href="fiche_produit.php?id_article=<?= $_SESSION['panier']['id_article'][$i]; ?>" class="alert-link text-dark titre-produit-panier"><?= $_SESSION['panier']['titre'][$i]; ?></a></h4>

                    <?php 
                    $dt = $bdd->query("SELECT stock FROM article WHERE id_article = " . $_SESSION['panier']['id_article'][$i]);
                    $s = $dt->fetch(PDO::FETCH_ASSOC);
                    if($s['stock'] <= 10)
                    {
                        $txt = "Attention ! il ne reste plus que $s[stock] exemplaire(s) disponible.";
                        $color = 'danger';
                    }
                    else 
                    {
                        $txt = "En stock !";
                        $color = 'success';
                    }
                    ?>

                    <small class="text-<?= $color; ?> fw-bold fst-italic mb-2"><?= $txt; ?></small>

                    <p>Quantité : <?= $_SESSION['panier']['quantite'][$i]; ?></p>

                    <p class="mb-0"><a href="?action=suppression&id_article=<?= $tab['id_article'] ?>" class="btn btn-dark" onclick="return(confirm('Voulez-vous réellement supprimer cet article ?'))">>Supprimer</a></p>
                </div>

                <div class="col-md-3 bg-white d-flex justify-content-end align-items-center p-2">
                    <p class="fw-bold mb-0"><?= round($_SESSION['panier']['quantite'][$i]*$_SESSION['panier']['prix'][$i], 2); ?>€</p>
                </div>

            </div>

        <?php endfor; ?>

        <div class="container col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8 d-flex justify-content-end align-items-center shadow-sm px-0 py-3 bg-white mt-2 mb-3"> 

            <h5 class="m-0 px-2 fw-bold">Sous total (<?= array_sum($_SESSION['panier']['quantite']) ?> articles) : <?= montantTotal(); ?>€</h5>
        </div>
        <div class="container col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8 p-0 text-end mb-5">

            <!-- Si l'internaute est authentifié sur le site, on entre dans le IF et il peut valider le panier -->
            <?php if(connect()): ?>

                <form action="" method="post">
                    <input type="submit" class="btn btn-dark" name="payer" value="FINALISER LA COMMANDE">
                </form>

            <?php else: // Sinon l'internaute n'est pas authentifié, on le redirige vers la page de connexion ?>

                <a href="<?= URL ?>connexion.php" class="btn btn-dark">IDENTIFIEZ-VOUS</a>

            <?php endif; ?>

        </div>

    <?php else: // Sinon le tableau ['id_article'] dans la session est vide, donc l'internaute n'a pas ajouté d'article dansle panier, on entre dans le ELSE ?>

        <div class="container col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8 mx-auto d-flex justify-content-center shadow-sm px-0">
            <p class="text-danger fw-bold">Votre panier est vide.</p>
        </div>

    <?php endif; ?>

<?php 
require_once 'inc/footer.inc.php';        