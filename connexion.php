<?php 
require_once 'inc/init.inc.php';

// Si l'internaute est connecté / authentifié sur le site, il n'a rien à faire sur la page connexion, on le redirige vers sa page profil
if(connect())
{
    header('location: profil.php');
}

// echo '<pre>'; print_r($_SESSION); echo '</pre>';

if(isset($_POST['pseudo_email'], $_POST['password']))
{
    // Afin de contrôler si le pseudo ou email est connu en BDD nous executons une requete de selection
    // SELECTIONNE TOUT EN BDD A CONDITION QUE le pseudo OU l'email SOIT EGALE aux colonnes pseudo/email de la BDD
    //                                                                GregFormateur            azdazdazd
    $verifCredentials = $bdd->prepare("SELECT * FROM user WHERE pseudo = :pseudo OR email = :email");
    $verifCredentials->bindValue(':pseudo', $_POST['pseudo_email'], PDO::PARAM_STR);
    $verifCredentials->bindValue(':email', $_POST['pseudo_email'], PDO::PARAM_STR);
    $verifCredentials->execute();

    // rowCount() retourne le nombre de résultats suite à la requete SQL 
    // Si la condition IF retourne TRUE, cela veut que le pseudo/email saisi dans le formulaire correspond à une ligne de résultat de la BDD, alors on entre dans le IF
    if($verifCredentials->rowCount())
    {
        // On entre dans la condition IF seulement dans le cas où l'internaute a saisi un email/pseudo connu en BDD
        // echo "Pseudo / email existant en BDD";

        // on execute fetch() sur l'objet PDOStatement afin de récupérer sous forme de tableau ARRAY les informations en BDD de l'utilisateur qui a saisi le bon email/pseudo dans le formulaire
        $user = $verifCredentials->fetch(PDO::FETCH_ASSOC);
        // echo '<pre>'; print_r($user); echo '</pre>';

        // Comparaison des mots de passe en clair :
        // $_POST['password'] == $user['password']

        // Si le mots de passe sont cryptés en BDD, nous pouvons les vérifier avec la fonction : 
        // password_verify() : fonction prédéfinie permettant de comparer une clé de hachage (mot de passe crypté en BDD) à une chaine de caractères
        // Si le mot de passe saisi dans le formulaire correspond à la clé de hachage stockée en BDD, on entre dans la condition IF

        //                             toto78, $2y$10$0I4M.ZeAsY8/mmR09
        if(password_verify($_POST['password'], $user['password']))
        {
            // On entre dans la condition seulement dans le cas où l'internaute a saisi le bon email/pseudo et le bon mot de passe, donc il a saisi les bons identifiants d'authentification

            // echo "Mot de passe valide";

            // La boucle foreach permet de passer en revue les données de l'utilisateur selectionné en BDD
            //      ARRAY  [id_user] => 7
            foreach($user as $key => $value)
            {
                // on ne conserve jamais le mot de passe dans la session
                if($key != 'password')
                    // $_SESSION['user']['pseudo'] = Greg
                    $_SESSION['user'][$key] = $value;

                // Pour chaque tour de boucle foreach, on stock les données de l'utilisateur dans son fichier de session, dans un tableau multidimensionnel à l'indice $_SESSION['user']
                // La session est accessible sur n'importe quelle page du site, c'est ce qui permettra à l'utilisateur d'être authentifié sur n'importe quelle page du site
            }
            
            // Une fois l'internaute authentifié et ses données dans son fichier de session, on le redirige vers sa page profil
            header('location: profil.php');
        }
        else
        {
            $error = "Identifiants invalide.";
        }
    }
    else // Sinon le pseudo/email saisie dans le formulaire ne retourne aucun résultat de la BDD, on  entre dans la condition ELSE
    {
        $error = "Identifiants invalide.";
    }
}

require_once 'inc/header.inc.php';
require_once 'inc/nav.inc.php';
?>

    <!-- Si l'indice 'validation_inscription' est définit dans la session de l'utilisateur, alors on entre dans le IF et on affiche le message de validation d'inscription -->
    <?php if(isset($_SESSION['validation_inscription'])): ?>
        <p class="bg-success col-md-5 mx-auto p-3 text-center text-white mt-3"><?= $_SESSION['validation_inscription']; ?></p>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <p class="bg-danger col-md-3 mx-auto p-3 text-center text-white mt-3"><?= $error; ?></p>
    <?php endif; ?>

    <h1 class="text-center my-5">Identifiez-vous</h1>

    <form action="" method="post" class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4 mx-auto">
        <div class="mb-3">
            <label for="pseudo_email" class="form-label">Nom d'utilisateur / Email</label>
            <input type="text" class="form-control" id="pseudo_email" name="pseudo_email" placeholder="Saisir votre Email ou votre nom d'utilisateur">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Saisir votre mot de passe">
        </div>
        <div>
            <p class="text-end mb-0"><a href="" class="alert-link text-dark">Pas encore de compte ? Cliquez ici</a></p>
            <p class="text-end m-0 p-0"><a href="" class="alert-link text-dark">Mot de passe oublié ?</a></p>
        </div>
        <input type="submit" name="submit" value="Continuer" class="btn btn-dark mb-4">
    </form>

<?php 
// On supprime l'indice 'validation_inscription' dans la session juste après l'avoir affiché sur la page web
unset($_SESSION['validation_inscription']);
require_once 'inc/footer.inc.php'; 