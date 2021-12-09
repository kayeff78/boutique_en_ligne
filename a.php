<?php
/*
    Exo : Espace de dialogue (tchat)
    1. Modélisation et création
        BDD : tchat
        table : commentaire
                id_commentaire      // INT(11) - PK - AI
                pseudo              // VARCHAR(255)
                date_enregistrement // DATETIME
                message             // TEXT

    2. Connexion à la BDD
    3. Création du formulaire HTML (pour l'ajout de message, pas de champs pour la date, insertion automatique dans la BDD)
    4.Contrôller en PHP que l'on receptionne bien toute les données du formulaire (print_r)
    5. Requete SQL d'enregistrement (INSERT)
    6. affichage des message (SELECT)
*/

//echo '<pre>'; print_r($_POST); echo '</pre>';

$pdo = new PDO('mysql:host=localhost;dbname=tchat', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
]);


if(isset($_POST['pseudo']) && isset($_POST['message'])){
    // htmlspecialchars fonction prédéfinis qui convertit chaque caractèrenspéciaux en entités HTML
    // strip_tags : fonction prédéfinie qui supprime complétement les balises HTML
    // 
    $_POST['pseudo'] = htmlspecialchars(strip_tags(addslashes($_POST['pseudo'])));
    $_POST['message'] = htmlspecialchars(strip_tags(addslashes($_POST['message'])));

    foreach($_POST as $key => $value)
    $_POST[$key] = htmlspecialchars(strip_tags(addslashes($value)));


    $m = $pdo->exec("INSERT INTO commentaire (pseudo, date_enregistrement, message) VALUES ('$_POST[pseudo]',NOW(), '$_POST[message]')");
    /*  
        prepare() : méthode issue de la classe PDO qui permet de préparer une requête SQL et de parer aux in jections SQL
        :pseudo et :message sont des marqueurs nominatifs, que l'on peux comparer a des boites vides permettant d'enfermer des valeurs

        bindValue() : méthode issue de la classe PDO qui permettant d'envoyer/enfermer/associer une valeurr aux marqueurs nominatifs déclarés (:pseudo :meessage)

        execute() : méthode issue de la classe PDO qui permettant d'executer une requete préparé
        Dés que l'internaute a la possibilité d'injecter une donnée dans la BDD (formulaire ou URL) il faudra à chaque fois préparer la requete SQL
    */

    $req = "INSERT INTO commentaire (pseudo, date_enregistrement, message) VALUES (':pseudo',NOW(), ':message')");
    // $i -> objet PDOStatement
    
    $i = $pdo->prepare($req);
    $i->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
    $i->bindValue(':message', $_POST['message'], PDO::PARAM_STR);
    $i->execute();
}
// 6 : Affichage des messages (SELECT)
$data = $pdo->query("SELECT pseudo, DATE_FORMAT(date_enregistrement, '%d/%m/%Y') AS dateFr, DATE_FORMAT(date_enregistrement, '%H:%i:%s') AS heureFr, message FROM commentaire");

$msg = '';
while($comments = $data->fetch(PDO::FETCH_ASSOC))
{
    //echo '<pre>'; print_r($comments); echo '</pre>';

    $msg .= '<div class="col-md-8 mx-auto alert alert-info">';

        $msg .= "<p><small class='fst-italic'>Posté par $comments[pseudo], le $comments[dateFr] à $comments[heureFr]</small></p>";

        $msg .= "<p>$comments[message]";

    $msg .= '</div>';
}

/*
    Failles XSS : cross site scripting en PHP
    C'est le fait d'insérer du code HTMl directement dans un formulaire ou dans l'URL

    <style>
    body{
        display: none;
    }
    </style>

    <script>
    var point = true;
    while(point == true)
    alert ("Je t ai eu ! ton site c est de la merde !!")
    </script>

    Injection SQL :
    ok'); DELETE FROM commentaire; (
*/
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CDN Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>09 - TCHAT - Failles de sécurités</title>
</head>
<body>
    <div class="container">

        <h1 class="display-4 text-center fst-italic">Bienvenue sur le Tchat !</h1>

        <?php
        echo  $msg; 
        
        echo "<p class='text-center'><span class='badge bg-info'>" . $data->rowCount() . "</span> messages postés sur le tchat.</p>";
        ?>

        <form method="post" class="col-md-8 mx-auto">

        <div class="row">
            <div class="mb-3 col-md-3">
                <label for="pseudo">Pseudo</label>
                <input type="text" class="form-control" id="pseudo" name="pseudo">
            </div>

            <div class="mb-3 col-md-9">
                <label for="message">Message</label>
                <textarea type="text" class="form-control" rows="10" id="message" name="message"></textarea>
            </div>
        
        </div>
        <div class="d-flex">
            <button type="submit" class="btn btn-dark col-md-2 mx-auto">Envoyer</button>
        </div>

    </form>


    </div>
</body>
</html>