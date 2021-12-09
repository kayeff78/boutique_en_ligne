<?php 
// FONCTION INTERNAUTE CONNECTE
// Fonction permettant de savoir si l'internaute est authentifié sur le site
function connect()
{
    // Si l'indice 'user' N'EST PAS DEFINIT dans la session, cela veut dire l'internaute n'est pas passé par la page connexion, donc qu'il n'est pas authentifié sur le site
    if(!isset($_SESSION['user']))
        return false;
    else 
        return true;
}

// FONCTION ADMINISTRATEUR CONNECTE
// fonction permettant de savoir si'linternaute est connecté et si son statut dans la session donc dans la BDD est bien 'admin'
function adminConnect()
{
    //  Si l'indice 'user' EST DEFINIT dans la session et que l'indice 'statut' a pour valeur 'admin', alors on entre dans le IF
    if(connect() && $_SESSION['user']['statut'] == 'admin')
        return true;
    else 
        return false;
}

// FONCTION CREATION PANNIER DANS LA SESSION
function createPanier()
{
    // Si l'indice 'panier' dans la session N'EST PAS définit, alors on entre dans le IF et on crée les différents tableaux dans le fichier de session de l'utilisateur
    if(!isset($_SESSION['panier']))
    {
        // Ces tableaux permettent de stocker les données d'un produit ajouté au panier
        $_SESSION['panier'] = [];
        $_SESSION['panier']['id_article'] = [];
        $_SESSION['panier']['photo'] = [];
        $_SESSION['panier']['titre'] = [];
        $_SESSION['panier']['quantite'] = [];
        $_SESSION['panier']['stock'] = [];
        $_SESSION['panier']['prix'] = [];
    }
}

// FONCTION AJOUT PRODUIT PANIER DANS LA SESSION
//                  10          http://  tee-shirt
function addPanier($id_article, $photo, $titre, $quantite, $stock, $prix)
{
    // On vérifie si l'indice 'panier' est crée dans la session ou pas
    createPanier();

    // array_search() : fonction prédéfinie permettant de savoir à quel indice se trouve un élément dans un tableau ARRAY
    // On tente de trouver si le produit que l'on ajoute au panier est déjà existant dans le tableau array $_SESSION['panier']['id_article'] dans la session
    $positionProduit = array_search($id_article, $_SESSION['panier']['id_article']);
    // echo '<pre>'; var_dump($positionProduit); echo '</pre>';

    // Si la valeur de $positionProduit est différent de false, cela veut dire que l'id_article est présent dans le panier de la session, alors on entre dans le IF et on modifie seulement la quantité du  produit à l'indice correspondant
    if($positionProduit !== false)
    {
        $_SESSION['panier']['quantite'][$positionProduit] += $quantite;
    }
    else // Sinon, l'id_article n'exsite pas dans la session $_SESSION['panier']['id_article'], on ajoute l'article normalement dans le panier
    {
        // On stock les données selectionnés en BDD du produit ajouté au panier dans les différents tableaux du panier dans la session
        $_SESSION['panier']['id_article'][] = $id_article;
        $_SESSION['panier']['photo'][] = $photo;
        $_SESSION['panier']['titre'][] = $titre;
        $_SESSION['panier']['quantite'][] = $quantite;
        $_SESSION['panier']['stock'][] = $stock;
        $_SESSION['panier']['prix'][] = $prix;
    }
}

/*
    ['user'] => array(
        ['id_user'] => 1
        ['prenom'] => Grégory
    )

    ['panier'] => array (

        ['id_article'] => array (
            0 => 1
            1 => 12
            2 => 56
        )

        ['photo'] => array (
            0 => http://localhost/PHP/10-boutique/assets/img/tee-shirt.jpg
            1 => http://localhost/PHP/10-boutique/assets/img/pull.jpg
            2 => http://localhost/PHP/10-boutique/assets/img/manteau.jpg
        )
    )
*/

// FONCTION CALCUL MONTANT TOTAL PANIER
function montantTotal()
{
    $total = 0;
    // La boucle FOR tourne autant de fois qu'il y a d'id_article dans la session du panier, en gros elle tourne autant qu'il y a d'articles dans le panier
    //             < 3 
    for($i = 0; $i < count($_SESSION['panier']['id_article']); $i++)
    {
        $total += $_SESSION['panier']['quantite'][$i]*$_SESSION['panier']['prix'][$i];
    }
    return round($total,2);
}

// FONCTION SUPPRESSION ARTICLE DANS PANIER SESSION
function deletePanier($idArticle)
{
    // On cherche à quel indice du tableau array ['id_article'] se trouve l'article a supprimer dans le panier
    $positionProduit = array_search($idArticle, $_SESSION['panier']['id_article']);

    // Si $positionProduit retourne un indice, on entre dans le IF
    if($positionProduit !== false)
    {
        // array_splice() supprime les éléments d'un tableau à l'indice correspondant mais il va ré-organiser le tableau ARRAY, tout les éléments stockés aux indices inférieur vont remonter aux indices supérieur 
        // ex : l'article stocké à l'indice [2] de l'array va remonter à l'indice [1] pour eviter d'avoir des indices vide dans l'array
        array_splice($_SESSION['panier']['id_article'], $positionProduit, 1);
        array_splice($_SESSION['panier']['titre'], $positionProduit, 1);
        array_splice($_SESSION['panier']['photo'], $positionProduit, 1);
        array_splice($_SESSION['panier']['quantite'], $positionProduit, 1);
        array_splice($_SESSION['panier']['stock'], $positionProduit, 1);
        array_splice($_SESSION['panier']['prix'], $positionProduit, 1);
    }
}
