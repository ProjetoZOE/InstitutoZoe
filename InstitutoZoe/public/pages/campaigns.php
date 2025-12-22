<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/images/logo-Icone.png" type="image/x-icon">
    <title>Instituto Zoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <style>
        .titulo-principal {
            background-color: #f28705;
            margin-top: 13vh;

        }

        .titulo-principal h1 {
            font-size: 3rem;
            font-weight: bold;
        }

        .card-title {
            color: #f28705;
            font-weight: bold;
        }

        .btn-vermais {
            background-color: #f28705;
            color: white;
            border: none;
        }

        .btn-vermais:hover {
            background-color: #d76f04;
        }

        .card img {
            object-fit: cover;
        }

        
    </style>

</head>

<body>
    <header>
        <?php include '../includes/menu.php'; ?>
    </header>
    <main style="padding: 0; overflow-x: hidden;">


        <div class="titulo-principal text-white text-center py-5">
            <h1>Nossas Campanhas</h1>
            <p class="lead">Projetos e eventos que transformam vidas com saúde, arte e empatia.</p>
        </div>


        <div class="container-fluid my-5">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-2 g-5 p-4">


                <div class="col">
                    <div class="card h-100 shadow border-0 rounded-4" data-aos="fade-right" data-aos-delay="0">

                        <!-- Carrossel -->
                        <div id="carousel-ballet" class="carousel slide carousel-fade" data-bs-ride="carousel">
                            <div class="carousel-inner rounded-top-4">
                                <div class="carousel-item active ">
                                    <img src="../assets/images/Ballet/ballet1.jpeg" class="d-block w-100" style="height: 70vh;"
                                        alt="...">
                                </div>
                                <div class="carousel-item">
                                    <img src="../assets/images/Ballet/Ballet.jpg" class="d-block w-100" style="height: 70vh;"
                                        alt="...">
                                </div>
                                <div class="carousel-item">
                                    <img src="../assets/images/Ballet/ballet2.jpeg" class="d-block w-100" style="height: 70vh;"
                                        alt="...">
                                </div>
                                <div class="carousel-item">
                                    <img src="../assets/images/Ballet/ballet3.jpeg" class="d-block w-100" style="height: 70vh;"
                                        alt="...">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carousel-ballet"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carousel-ballet"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </button>
                        </div>

                        <!-- Conteúdo do Card -->
                        <div class="card-body text-center p-4">
                            <h5 class="card-title texto">Espetáculo de Ballet</h5>
                            <p class="card-text">Apresentações artísticas com nossas crianças e adolescentes, promovendo
                                inclusão por meio da dança.</p>
                        </div>
                    </div>
                </div>


                <div class="col">
                    <div class="card h-100 shadow border-0 rounded-4" data-aos="fade-left" data-aos-delay="150">

                        <!-- Carrossel -->
                        <div id="carousel-mutirao" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner rounded-top-4">
                                <div class="carousel-item active ">
                                    <img src="../assets/images/Multirão/multirao.jpeg" class="d-block w-100" style="height: 70vh;"
                                        alt="...">
                                </div>

                            </div>

                        </div>

                        <!-- Conteúdo do Card -->
                        <div class="card-body text-center p-4">
                            <h5 class="card-title texto">Mutirão da Saúde</h5>
                            <p class="card-text">Consultas e exames gratuitos com profissionais voluntários, levando
                                saúde onde é mais necessário.</p>
                        </div>
                    </div>
                </div>



                <div class="col">
                    <div class="card h-100 shadow border-0 rounded-4" data-aos="fade-right" data-aos-delay="0">

                        <!-- Carrossel -->
                        <div id="carousel-autismo" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner rounded-top-4">
                                <div class="carousel-item active">
                                    <img src="../assets/images/Palestra/palestra.jpeg" class="d-block w-100" style="height: 70vh;"
                                        alt="...">
                                </div>

                            </div>

                        </div>

                        <!-- Conteúdo do Card -->
                        <div class="card-body text-center p-4">
                            <h5 class="card-title texto">Conscientização do Autismo</h5>
                            <p class="card-text">Campanhas de informação, palestras e oficinas para educar sobre o
                                espectro autista e combater o preconceito.</p>
                        </div>
                    </div>
                </div>


                <div class="col">
                    <div class="card h-100 shadow border-0 rounded-4" data-aos="fade-left" data-aos-delay="150">

                        <!-- Carrossel -->
                        <div id="carousel-datas" class="carousel slide carousel-fade" data-bs-ride="carousel">
                            <div class="carousel-inner rounded-top-4">
                                <div class="carousel-item active">
                                    <img src="../assets/images/Comemorativas/comemorativa.jpeg" class="d-block w-100"
                                        style="height: 70vh;" alt="...">
                                </div>
                                <div class="carousel-item">
                                    <img src="../assets/images/Comemorativas/comemorativa1.jpeg" class="d-block w-100"
                                        style="height: 70vh;" alt="...">
                                </div>
                                <div class="carousel-item">
                                    <img src="../assets/images/Comemorativas/comemorativa2.jpeg" class="d-block w-100"
                                        style="height: 70vh;" alt="...">
                                </div>
                                <div class="carousel-item">
                                    <img src="../assets/images/Comemorativas/comemorativa3.jpeg" class="d-block w-100"
                                        style="height: 70vh;" alt="...">
                                </div>
                                <div class="carousel-item">
                                    <img src="../assets/images/Comemorativas/comemorativa4.jpeg" class="d-block w-100"
                                        style="height: 70vh;" alt="...">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carousel-datas"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carousel-datas"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </button>
                        </div>

                        <!-- Conteúdo do Card -->
                        <div class="card-body text-center p-4">
                            <h5 class="card-title texto">Datas Comemorativas</h5>
                            <p class="card-text">Eventos como Dia das Mães, Dia das Crianças e outras datas especiais
                                para acolher e celebrar com amor.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>





    </main>


    <?php include '../includes/footer.php'; ?>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>


    <script src="../assets/js/navbar.js"></script>




</body>

</html>