<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/logo-Icone.png" type="image/x-icon">
    <title>Instituto Zoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
        .titulo-principal {
            margin-top: 13vh;
            background-color: #f28705;
            text-align: center;
            display: flex;
            justify-content: space-around;
            gap: 10px;

        }

        .titulo-principal h1 {

            text-align: center;
        }

        .titulo-principal img {
            margin: 0;
        }

        .card-img-uniform {
            height: 350px;
            object-fit: cover;
        }

        .card-body {
            text-align: center;

        }

        .servicos {
            margin: 0;
            gap: 3px;
        }

        .card {
            transition: 0.6s;
        }

        .card:hover {
            scale: 1.1;
            border: 5px solid #ff991d;
        }
    </style>

</head>

<body>

    <header>
        <?php include 'includes/navbar.php'; ?>
    </header>

    <main style="padding: 0; overflow-x: hidden;">


        <section class="banner-container">
            <img src="img/Atividades.svg" class="banner-img" alt="Banner do Instituto Zoe" data-aos="fade-right"
                data-aos-delay="450">
        </section>


        <div class="text-center bg-primary text-white py-4" data-aos="fade-right"
                data-aos-delay="650">
            <h2>Agende sua Avaliação para:</h2>

        </div>

        <div class="container my-5">
            <div class="servicos row gy-5" style="justify-content: space-around;">

                <!-- Fisioterapia -->
                <div class="col-lg-5 col-md-6" data-aos="fade-down-right" data-aos-delay="0">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/fisioterapia.jpg" class="w-100 card-img-uniform" alt="Fisioterapia">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Fisioterapia</h4>
                                <p class="card-text">Atendimento especializado com foco em reabilitação, prevenção e
                                    bem-estar físico, promovendo autonomia e qualidade de vida.</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Funcional -->
                <div class="col-lg-5 col-md-6" data-aos="fade-down-left" data-aos-delay="150">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/funcional.jpg" class="w-100 card-img-uniform" alt="Funcional">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Funcional</h4>
                                <p class="card-text">Exercícios personalizados que desenvolvem força, equilíbrio e
                                    resistência, promovendo saúde integral.</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Hidroginástica -->
                <div class="col-lg-5 col-md-6" data-aos="fade-right" data-aos-delay="0">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/hidro.jpg" class="w-100 card-img-uniform" alt="Hidroginástica">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Hidroginástica</h4>
                                <p class="card-text">Atividade física realizada na água, excelente para mobilidade,
                                    relaxamento e fortalecimento muscular.</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Ballet -->
                <div class="col-lg-5 col-md-6" data-aos="fade-left" data-aos-delay="150">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/ballet.png" class="w-100 card-img-uniform" alt="Ballet">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Ballet</h4>
                                <p class="card-text">Aulas que estimulam coordenação motora, expressão corporal e
                                    disciplina
                                    com amor e inclusão.</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Jiu-Jitsu -->
                <div class="col-lg-5 col-md-6" data-aos="fade-right" data-aos-delay="0">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/jiu-jitsu.png" class="w-100 card-img-uniform" alt="Jiu-Jitsu">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Jiu-Jitsu</h4>
                                <p class="card-text">Arte marcial que trabalha o corpo e a mente, incentivando o
                                    autocontrole, respeito e autoestima.</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Natação -->
                <div class="col-lg-5 col-md-6" data-aos="fade-left" data-aos-delay="150">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/natação.png" class="w-100 card-img-uniform" alt="Natação">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Natação</h4>
                                <p class="card-text">Aulas adaptadas com foco em segurança, coordenação e superação de
                                    limites individuais.</p>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>










    </main>


    <?php include 'include/footer.php'; ?>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>


    <script src="mobile-navbar.js"></script>




</body>

</html>