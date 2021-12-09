            <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-0" aria-label="Eighth navbar example">
                <div class="container">
                    <a class="navbar-brand" href="<?= URL ?>index.php"><img src="<?= URL ?>assets/img/boutique.gif" alt="logo-gif" class="logo-gif"></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample07" aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                    </button>
            
                    <div class="collapse navbar-collapse" id="navbarsExample07">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                            <!-- lien page home -->
                            <li class="nav-item">
                                <!-- chemin absolu pour tout les liens de la nav : http://localhost/PHP/10-boutique/index.php -->
                                <a class="nav-link active" aria-current="page" href="<?= URL ?>index.php"><i class="bi bi-house-fill"></i></a>
                            </li>

                            <?php if(connect()): // lien accordé à un utilisateur authentifié sur le site mais NON ADMIN ?>

                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page" href="<?= URL ?>profil.php">Mon compte</a>
                                </li>

                            <?php else: // lien accordé à l'utilisateur lambda NON AUTHENTIFIE ?>

                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page" href="<?= URL ?>inscription.php">Inscription</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page" href="<?= URL ?>connexion.php">Connexion</a>
                                </li>

                            <?php endif; ?>

                            <!-- liens commun accordé à n'importe quel utilisateur -->
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="<?= URL ?>boutique.php">Boutique<i class="bi bi-cart4"></i></a>
                            </li>

                            <?php 
                            // Si l'indice 'panier' est définit dans la session, alors on entre dans le IF et on calcul la somme des quantités de produits dans le panier
                            if(isset($_SESSION['panier']))
                                $calc = array_sum($_SESSION['panier']['quantite']);
                            else 
                                $calc = 0;
                            ?>

                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="<?= URL ?>panier.php">Panier <span class="badge bg-success"><?= $calc; ?></span></a>
                            </li>
                            
                            <?php if(adminConnect()): // liens accordé à l'administrateur du site, statut 'admin' dans la BDD donc dans la session ?>

                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="dropdown07" data-bs-toggle="dropdown" aria-expanded="false">BACKOFFICE</a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdown07">
                                    <li><a class="dropdown-item" href="<?= URL ?>admin/gestion_boutique.php">Gestion Boutique</a></li>
                                    <li><a class="dropdown-item" href="<?= URL ?>admin/gestion_user.php">Gestion Utilisateur</a></li>
                                    <li><a class="dropdown-item" href="<?= URL ?>admin/gestion_commande.php">Gestion Commande</a></li>
                                    </ul>
                                </li>

                            <?php endif; ?>
                        </ul>

                        <?php if(connect()): ?>

                            <span class="d-flex flex-column justify-content-center align-items-center ">

                            <span class="fst-italic text-white">Bonjour <span class="text-warning"><?= $_SESSION['user']['pseudo']; ?></span> !</span>


                            <a href="<?= URL ?>boutique.php?action=deconnexion" class="btn btn-success text-white"><i class="bi bi-box-arrow-right text-white"></i> Déconnexion</a>

                            </span>
                        <?php endif; ?>

                        <!-- <form class="pl-2">
                            <input class="form-control" type="text" placeholder="Rechercher" aria-label="Search">
                        </form> -->
                    </div>
                </div>
            </nav>

            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="<?= URL ?>assets/img/slider3.jpg" class="d-block w-100" alt="slider 3">
                    </div>
                    <div class="carousel-item">
                        <img src="<?= URL ?>assets/img/slider4.jpg" class="d-block w-100" alt="slider 4">
                    </div>
                    <div class="carousel-item">
                        <img src="<?= URL ?>assets/img/slider1.jpg" class="d-block w-100" alt="slider 1">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </header>

        <main class="container zone-main">