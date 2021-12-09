<?php 
require_once '../inc/init.inc.php';

// Si l'internaute N'EST PAS admin ou n'est peut-être même pas authentifié, il n'a rien faire sur cette page, on le redirige vers la page connexion.php
if(!adminConnect())
{
    //                http://localhost/PHP/10-boutique/connexion.php
    header('location: ' . URL . 'connexion.php');
}

// SUPPRESSION PRODUIT 
if(isset($_GET['action']) && $_GET['action'] == 'suppression')
{
    // Si l'indice 'action' est définit et que sa valeur est différente de vide, alors on entre dansla condition IF et on execute la requete de suppression
    if(isset($_GET['id_article']) && !empty($_GET['id_article']))
    {
        $deleteProduct = $bdd->prepare("DELETE FROM article WHERE id_article = :id_article");
        $deleteProduct->bindValue(':id_article', $_GET['id_article'], PDO::PARAM_INT);
        $deleteProduct->execute();

        // On redefinit la valeur de l'indice 'action' dans l'URL afin dans d'entrée dans la condition IF de l'affichage des articles
        $_GET['action'] = 'affichage';

        $msg = "L'article n° <strong>$_GET[id_article]</strong> a été supprimé avec succés.";

    }
    else // sinon on redirige l'internaute vers l'affichage des articles
    {   
        header('location: ' . URL . 'admin/gestion_boutique.php?action=affichage');
    }
}

// SELECTION ARTICLE POUR MODIFICATION EN BDD
if(isset($_GET['action']) && $_GET['action'] == 'modification')
{
    if(isset($_GET['id_article']) && !empty($_GET['id_article']))
    {
        $produitActuel = $bdd->prepare("SELECT * FROM article WHERE id_article = :id_article");
        $produitActuel->bindValue(':id_article', $_GET['id_article'], PDO::PARAM_INT);
        $produitActuel->execute();

        // Si la requete retourne au moins 1 résultat, cela veut dire que l'id du produit est connu en BDD, on entre dans le IF
        if($produitActuel->rowCount())
        {   
            // On récupère sous forme de tableau Array toute les données du produit à modifier 
            $product = $produitActuel->fetch(PDO::FETCH_ASSOC);
            // echo '<pre style="margin-left: 320px;">'; print_r($product); echo '</pre>';

            // On stock chaque donnée du produit à modifier dans une variable qui sera dans les attributs 'value' du formulaire
            $id_produit = (isset($product['id_article'])) ? $product['id_article'] : ''; // 12
            $reference = (isset($product['reference'])) ? $product['reference'] : '';
            $categorie = (isset($product['categorie'])) ? $product['categorie'] : '';
            $titre = (isset($product['titre'])) ? $product['titre'] : '';
            $description = (isset($product['description'])) ? $product['description'] : '';
            $couleur = (isset($product['couleur'])) ? $product['couleur'] : '';
            $taille = (isset($product['taille'])) ? $product['taille'] : '';
            $sexe = (isset($product['sexe'])) ? $product['sexe'] : '';
            $photo = (isset($product['photo'])) ? $product['photo'] : '';
            $prix = (isset($product['prix'])) ? $product['prix'] : '';
            $stock = (isset($product['stock'])) ? $product['stock'] : '';

            // echo '<pre style="margin-left: 320px;">'; print_r($reference); echo '</pre>';
        }
        else // Sinon l'id dans l'URL n'est pas connu en BDD, on redirige l'internaute vers l'affichage des articles
        {
            header('location: ' . URL . 'admin/gestion_boutique.php?action=affichage');
        }
    }
    else 
    {
        header('location: ' . URL . 'admin/gestion_boutique.php?action=affichage');
    }
}

if(isset($_POST['reference'], $_POST['categorie'], $_POST['titre'], $_POST['description'], $_POST['couleur'], $_POST['taille'], $_POST['sexe'], $_POST['prix'], $_POST['stock']))
{
    // TRAITEMENT FICHIER UPLODE
    $photoBdd = '';

    if(isset($_GET['action']) && $_GET['action'] == 'modification')
    {
        // En cas de modification, si nous ne changeons pas d'image, le champ type 'file' n'a pas d'attribut 'value', donc nous aurons un champ vide dans la BDD
        // Si nous ne changeons pas d'image, nous récupérons l'URL de l'image existante de l'article en BDD afin de l'affecter à la variable $photoBdd et de le ré-insérer en BDD
        $photoBdd = $_POST['photo_actuelle'];
    }

    if(!empty($_FILES['photo']['name']))
    {
        // On renomme l'image en concaténant la référence saisie dans le formulaire et le nom de l'image d'origine piochée dans $_FILES 
        $nomPhoto = $_POST['reference'] . '-' . $_FILES['photo']['name'];
        
        // On définit l'URL de l'image de l'image qui sera stockée en BDD
        $photoBdd = URL . "assets/img/$nomPhoto";

        // On définit le chamin physique de l'image qui sera copiée dans le dossier
        $photoDossier = RACINE_SITE . "assets/img/$nomPhoto";

        // copy() fonction prédéfinie permettant de copier un fichier umplodé dans un dossier 
        // arguments :
        // 1. Le nom temporaire de l'image piochée dans $_FILES 
        // 2. Le chemin physique de l'image sur le serveur
        copy($_FILES['photo']['tmp_name'], $photoDossier);
    }

    if(isset($_GET['action']) && $_GET['action'] == 'ajout')
    {
        $data = $bdd->prepare("INSERT INTO article (reference, categorie, titre, description, couleur, taille, sexe, photo, prix, stock) VALUES (:reference, :categorie, :titre, :description, :couleur, :taille, :sexe, :photo, :prix, :stock)");

        // Après l'insertion, on redirige l'internaute vers l'affichage des produits en modifiant la valeur de l'indice 'action' dans l'URL
        $_GET['action'] = 'affichage';

        $msg = "L'article <strong>$_POST[titre]</strong> référence <strong>$_POST[reference]</strong> a bien été enregistré.";
    }
    else 
    {
        // Requete SQL de modification en fonction de l'id_article transmit dans l'URL
        $data = $bdd->prepare("UPDATE article SET reference = :reference, categorie = :categorie, titre = :titre, description = :description, couleur = :couleur, taille = :taille, sexe = :sexe, photo = :photo, prix = :prix, stock = :stock WHERE id_article = :id_article");

        $data->bindValue(':id_article', $_GET['id_article'], PDO::PARAM_INT);

        // Après l'insertion, on redirige l'internaute vers l'affichage des produits en modifiant la valeur de l'indice 'action' dans l'URL
        $_GET['action'] = 'affichage';

        $msg = "L'article <strong>$_POST[titre]</strong> référence <strong>$_POST[reference]</strong> a bien été modifié.";
    }
    
    $data->bindValue(':reference', $_POST['reference'], PDO::PARAM_STR);
    $data->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_STR);
    $data->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
    $data->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
    $data->bindValue(':couleur', $_POST['couleur'], PDO::PARAM_STR);
    $data->bindValue(':taille', $_POST['taille'], PDO::PARAM_STR);
    $data->bindValue(':sexe', $_POST['sexe'], PDO::PARAM_STR);
    $data->bindValue(':photo', $photoBdd, PDO::PARAM_STR);
    $data->bindValue(':prix', $_POST['prix'], PDO::PARAM_INT);
    $data->bindValue(':stock', $_POST['stock'], PDO::PARAM_INT);
    $data->execute();
}

require_once '../inc/admin_inc/header.inc.php';
require_once '../inc/admin_inc/nav.inc.php';

// TEST IMAGE : 
// 45R15-tee-shirt1.jpg
// echo $nomPhoto . '<br>';
// http://localhost/PHP/10-boutique/assets/img/45R15-tee-shirt1.jpg
// echo $photoBdd . '<br>';
// C:/xampp/htdocs/PHP/10-boutique/assets/img/45R15-tee-shirt1.jpg
// echo $photoDossier . '<br>';

// echo '<pre>'; print_r($_POST); echo '</pre>'; 
// Les données d'un fichier uploadé sont accessible en PHP via la superglobale $_FILES (Array)
// echo '<pre>'; print_r($_FILES); echo '</pre>'; 
// echo '<pre>'; print_r($_GET); echo '</pre>'; 
?>

<div class="d-flex mx-auto mt-2 mb-5 justify-content-center">
    <a href="?action=affichage" class="btn btn-outline-dark">AFFICHAGE DES ARTICLES</a>
    &nbsp;&nbsp;
    <a href="?action=ajout" class="btn btn-outline-dark">NOUVEL ARTICLE</a>
    &nbsp;&nbsp;
    <a href="<?= URL ?>admin/gestion_boutique.php" class="btn btn-outline-dark">INFORMATIONS STOCK</a>
</div>

<?php 
if(empty($_GET)): 

    $d = $bdd->query("SELECT id_article, photo, reference, categorie, titre, stock FROM article WHERE stock <= 10 OR stock <= 20");
    $p = $d->fetchAll(PDO::FETCH_ASSOC);
    //echo '<pre>'; print_r($p); echo '</pre>'; 

    if($p != false):
?>

    <p class="text-center"><span class="badge bg-success"><?= $d->rowCount() ?></span> article(s) présentent une quantité de stock insuffisante.</p>

    <table id="table-infos-stock" class="mx-auto table table-bordered table-striped text-center">
        <thead>
            <tr class="table-info">
            <?php foreach($p[0] as $key => $value): ?>
                <?php if($key != 'id_article'): ?>
                    <th><?= strtoupper($key); ?></th>
                <?php endif; ?>
            <?php endforeach; ?>
                <th>EDIT</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($p as $tab): ?>
            <tr>
            <?php foreach($tab as $key => $value): ?>

                <?php if($key != 'id_article'): ?>

                    <?php if($key == 'photo'): ?>

                        <td class="d-flex flex-column align-items-center">
                            <a href="<?= $value; ?>" data-lightbox="image-infos-stock" data-title="<?= $tab['titre'] ?>" data-alt="<?= $tab['titre']; ?>" class="d-flex align-items-center">
                                <img src="<?= $value; ?>" alt="<?= $tab['titre']; ?>" class="img-articles-bo">
                            </a>
                            <small class="text-info fst-italic">Cliquer pour agrandir</small>
                        </td>
                    
                    <?php elseif($key == 'stock'): 
                            
                            if($value <= 10)
                                $color = 'bg-danger text-white';
                            elseif($value <= 20)
                                $color = 'bg-warning text-white';
                            else 
                                $color = ''; 
                        ?>

                            <td class="<?= $color; ?>"><?= $value ?></td>

                    <?php else: ?>

                        <td><?= $value; ?></td>

                    <?php endif; ?>

                <?php endif; ?>

            <?php endforeach; ?>

                <td><a href="?action=modification&id_article=<?= $tab['id_article'] ?>" class="btn btn-dark"><i class="bi bi-pencil-square"></i></a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php else: ?>

        <p class="text-center">Aucun article ne présente une quantité de stock insuffisante.</p>

<?php 
    endif;
endif; 
?>

<!-- Si l'indice 'action' est définit dans l'URL et qu'il a pour valeur 'affichage', cela veut dire l'internaute a cliqué sur le lien 'AFFICHAGE DES ARTICLES' et par conséquent transmit dans l'URL 'action=affichage' -->
<?php if(isset($_GET['action']) && $_GET['action'] == 'affichage'): ?>

    <?php if(isset($msg)): ?>
        <p class="bg-success col-md-5 mx-auto p-3 text-center text-white my-3 shadow-sm"><?= $msg; ?></p>
    <?php endif; ?>

    <?php 
    // $data : objet PDOStatement
    $data = $bdd->query("SELECT * FROM article");
    $articles = $data->fetchAll(PDO::FETCH_ASSOC);
    // echo '<pre>'; print_r($articles); echo '</pre>'; 
    ?>

    <p><span class="badge bg-success"><?= $data->rowCount(); ?></span> article(s) enregistrés.</p>

    <div class="table-responsive">
        <table id="table-backoffice" class="table table-bordered table-striped text-center">
            <thead>
                <tr class="table-info">
                <!-- On va crocheter à l'indice [0] du tableau multi et on place chaque indice de l'array dans les entêtes <th> du tableau HTML -->
                <?php foreach($articles[0] as $key => $value): 
                    
                        if($key != 'id_article'):
                ?>

                    <!-- strtoupper : fonction prédéfinie affichant la chaine de caractères en majuscule -->
                    <th><?= strtoupper($key); ?></th>

                <?php 
                        endif;
                      endforeach; 
                ?>
                    <th>EDIT</th>
                    <th>SUPP</th>
                </tr>     
            </thead>            <!-- array --> 
            <tbody>
            <?php foreach($articles as $tab): ?>

                <tr>       <!-- photo     http://localhost/1245-tee-shirt.jpg  -->
                <?php foreach($tab as $key => $value): ?>

                    <?php 
                    if($key != 'id_article'):

                        if($key == 'photo'): ?>

                        <td>
                            <a href="<?= $value; ?>" data-lightbox="image" data-title="<?= $tab['titre'] ?>" data-alt="<?= $tab['titre']; ?>" class="d-flex align-items-center">
                                <img src="<?= $value; ?>" alt="<?= $tab['titre']; ?>" class="img-articles-bo">
                            </a>
                            <small class="text-info fst-italic">Cliquer pour agrandir</small>
                        </td>

                    <?php elseif($key == 'prix'): ?>

                        <td><strong><?= $value ?>€</strong></td>

                    <?php elseif($key == 'taille'): ?>

                        <td><?= strtoupper($value) ?></td>

                    <?php elseif($key == 'stock'): 
                        
                        if($value <= 10)
                            $color = 'bg-danger text-white';
                        elseif($value <= 20)
                            $color = 'bg-warning text-white';
                        else 
                            $color = ''; 
                    ?>

                        <td class="<?= $color; ?>"><?= $value ?></td>

                    <?php elseif($key == 'description' && iconv_strlen($value) > 50): ?>

                        <td><?= substr($value, 0, 50); ?>[...]</td>

                    <?php else: ?>

                        <td><?= $value ?></td>

                    <?php endif; 
                    
                    endif;
                    ?>

                <?php endforeach; ?>

                    <td><a href="?action=modification&id_article=<?= $tab['id_article'] ?>" class="btn btn-success"><i class="bi bi-pencil-square"></i></a></td>

                    <td>
                        <a href="?action=suppression&id_article=<?= $tab['id_article'] ?>" class="btn btn-danger" onclick="return(confirm('Voulez-vous réellement supprimer cet article ?'))">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>

            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>

<!-- Si l'indice 'action' est définit dans l'URL et qu'il a pour valeur 'ajout', cela veut dire l'internaute a cliqué sur le lien 'AJOUTER UN ARTICLE' et par conséquent transmit dans l'URL 'action=ajout' -->
<?php if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')): ?>
    <!-- Exo : faites en sorte d'afficher dans le titre h1 'modification article' lorsque l'on clique sur le lien de modification et 'ajout article' lorsque l'on clique sur le lien pour ajouter un article -->

    <!-- enctype="multipart/form-data" : attribut permettant de récupérer les informations d'un fichier uploadé via un formulaire -->
    <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-12">
            <label for="reference" class="form-label">Référence</label>
            <input type="text" class="form-control" id="reference" name="reference" value="<?php if(isset($reference)) echo $reference; ?>">
        </div>
        <div class="col-md-6">
            <label for="categorie" class="form-label">Catégorie</label>
            <input type="text" class="form-control" id="categorie" name="categorie" value="<?php if(isset($categorie)) echo $categorie; ?>">
        </div>
        <div class="col-md-6">
            <label for="titre" class="form-label">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre" value="<?php if(isset($titre)) echo $titre; ?>">
        </div>
        <div class="col-md-12">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" rows="10" id="description" name="description"><?php if(isset($description)) echo $description; ?></textarea>
        </div>
        <div class="col-md-6">
            <label for="couleur" class="form-label">Couleur</label>
            <input type="text" class="form-control" id="couleur" name="couleur" value="<?php if(isset($couleur)) echo $couleur; ?>">
        </div>
        <div class="col-md-6">
            <label for="taille" class="form-label">Taille</label>
            <select id="taille" name="taille" class="form-select">

                <option value="s">S</option>

                <option value="m" <?php if(isset($taille) && $taille == 'm') echo 'selected'; ?>>M</option>

                <option value="l" <?php if(isset($taille) && $taille == 'l') echo 'selected'; ?>>L</option>

                <option value="xl" <?php if(isset($taille) && $taille == 'xl') echo 'selected'; ?>>XL</option>

            </select>
        </div>
        <div class="col-md-6">
            <label for="sexe" class="form-label">Sexe</label>
            <select id="sexe" name="sexe" class="form-select">

                <option value="homme">Homme</option>

                <option value="femme" <?php if(isset($sexe) && $sexe == 'femme') echo 'selected'; ?>>Femme</option>

                <option value="mixte" <?php if(isset($sexe) && $sexe == 'mixte') echo 'selected'; ?>>Mixte</option>

            </select>
        </div>
        <div class="col-md-6">
            <label for="photo" class="form-label">Photo</label>
            <input type="file" class="form-control" id="photo" name="photo">
        </div>

        <!-- Affichage de l'image du produit en cas de modification -->
        <?php if(isset($photo) && !empty($photo)): ?>

            <!-- on définit un champ 'caché' permettant de récupérer l'URL de l'image en BDD en cas de modification si nous ne souhaitons pas la modifier -->
            <input type="hidden" id="photo_actuelle" name="photo_actuelle" value="<?= $photo ?>">

            <div class="d-flex flex-column align-items-center">

                <small class="fst-italic mb-2">Vous pouvez uploader une nouvelle photo si vous souhaitez la modifier</small>

                <a href="<?= $photo; ?>" data-lightbox="image-modif" data-title="<?php if(isset($titre)) echo $titre; ?>" data-alt="<?php if(isset($titre)) echo $titre; ?>" class="d-flex align-items-center">
                    <img src="<?= $photo ?>" alt="" class="img-articles-bo shadow-sm">
                </a>
                <small class="text-info fst-italic">Cliquer pour agrandir</small>

            </div>

        <?php endif; ?>

        <div class="col-md-6">
            <label for="prix" class="form-label">Prix</label>
            <input type="text" class="form-control" id="prix" name="prix" value="<?php if(isset($prix)) echo $prix; ?>">
        </div>
        <div class="col-md-6">
            <label for="stock" class="form-label">Stock</label>
            <input type="text" class="form-control" id="stock" name="stock" value="<?php if(isset($stock)) echo $stock; ?>">
        </div>
        
        <div class="col-12">
            <button type="submit" class="btn btn-dark mb-4">
            <?php if($_GET['action'] == 'ajout'):?>
            Enregistrer
            <?php else: ?>
            Enregistrer les modifications  
            <?php endif; ?>
            </button>
        </div>
    </form>

<?php 
endif;
require_once '../inc/admin_inc/footer.inc.php';

