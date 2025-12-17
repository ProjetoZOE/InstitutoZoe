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
        <?php include 'incluir/header.php'; ?>
    </header>

    <main style="padding: 0; overflow-x: hidden;">




        <section class="banner-container">
            <img src="img/Serviços.svg" class="banner-img" alt="Banner do Instituto Zoe" data-aos="fade-right"
                data-aos-delay="450">
        </section>

        <div class="text-center bg-primary text-white py-4" data-aos="fade-right" data-aos-delay="650">
            <h2>Agende sua Consulta para:</h2>
        </div>


        <div class="container my-5">
            <div class="servicos row gy-5" style="justify-content: space-around;">


                <div class="col-lg-5 col-md-6" data-aos="fade-down-right" data-aos-delay="0">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/nutricionista.jpg" class="w-100 card-img-uniform" alt="Nutricionista">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Nutricionista</h4>
                                <p class="card-text">Avaliação nutricional e orientação alimentar para promover saúde,
                                    energia e bem-estar.</p>
                            </div>
                        </div>
                    </a>
                </div>


                <div class="col-lg-5 col-md-6" data-aos="fade-down-left" data-aos-delay="150">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/ginecologista.jpg" class="w-100 card-img-uniform" alt="Ginecologista">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Ginecologista</h4>
                                <p class="card-text">Cuidado com a saúde da mulher em todas as fases da vida, com
                                    acolhimento e respeito.</p>
                            </div>
                        </div>
                    </a>
                </div>


                <div class="col-lg-5 col-md-6" data-aos="fade-right" data-aos-delay="0">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/neurologista.jpg" class="w-100 card-img-uniform" alt="Neurologista">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Neurologista</h4>
                                <p class="card-text">Diagnóstico e tratamento de distúrbios do sistema nervoso, com
                                    atenção
                                    especializada.</p>
                            </div>
                        </div>
                    </a>
                </div>


                <div class="col-lg-5 col-md-6" data-aos="fade-left" data-aos-delay="150">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/neuropediatra.jpg" class="w-100 card-img-uniform" alt="Neuropediatra">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Neuropediatra</h4>
                                <p class="card-text">Acompanhamento do desenvolvimento neurológico infantil com foco em
                                    intervenções precoces.</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Jiu-Jitsu -->
                <div class="col-lg-5 col-md-6" data-aos="fade-right" data-aos-delay="0">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/Psiquiatra.jpg" class="w-100 card-img-uniform" alt="Psiquiatra">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Psiquiatra</h4>
                                <p class="card-text">Atenção à saúde mental com empatia e responsabilidade, promovendo
                                    equilíbrio emocional.</p>
                            </div>
                        </div>
                    </a>
                </div>


                <div class="col-lg-5 col-md-6" data-aos="fade-left" data-aos-delay="150">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/Fonoaudiologia.jpg" class="w-100 card-img-uniform" alt="Fonoaudiologia">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Fonoaudiologia</h4>
                                <p class="card-text">Apoio ao desenvolvimento da comunicação, linguagem e funções orais
                                    com
                                    técnicas modernas.</p>
                            </div>
                        </div>
                    </a>
                </div>


                <div class="col-lg-5 col-md-6" data-aos="fade-right" data-aos-delay="0">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/Musicoterapia.jpg" class="w-100 card-img-uniform" alt="Musicoterapia">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Musicoterapia</h4>
                                <p class="card-text">Terapia por meio da música que estimula a expressão, comunicação e
                                    bem-estar emocional.</p>
                            </div>
                        </div>
                    </a>
                </div>



                <div class="col-lg-5 col-md-6" data-aos="fade-left" data-aos-delay="150">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/Massoterapia.jpg" class="w-100 card-img-uniform" alt="Massoterapia">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Massoterapia</h4>
                                <p class="card-text">Técnicas de massagem para alívio de dores, relaxamento muscular e
                                    qualidade de vida.</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-5 col-md-6" data-aos="fade-right" data-aos-delay="0">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/Psicopedagogia.jpg" class="w-100 card-img-uniform" alt="Psicopedagogia">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Psicopedagogia</h4>
                                <p class="card-text">Apoio ao processo de aprendizagem e superação de dificuldades
                                    escolares
                                    e cognitivas.</p>
                            </div>
                        </div>
                    </a>
                </div>


                <div class="col-lg-5 col-md-6" data-aos="fade-left" data-aos-delay="150">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/Neuropsicopedagogia.jpg" class="w-100 card-img-uniform"
                                alt="Neuropsicopedagogia">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Neuropsicopedagogia</h4>
                                <p class="card-text">Integração entre neurologia, psicologia e pedagogia para
                                    compreender e
                                    desenvolver o potencial de cada indivíduo.</p>
                            </div>
                        </div>
                    </a>
                </div>


                <div class="col-lg-5 col-md-6" data-aos="fade-right" data-aos-delay="0">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/Psicólogo.jpg" class="w-100 card-img-uniform" alt="Psicólogo">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Psicólogo</h4>
                                <p class="card-text">Acompanhamento psicológico com acolhimento e escuta ativa para
                                    promoção
                                    da saúde emocional.</p>
                            </div>
                        </div>
                    </a>
                </div>


                <div class="col-lg-5 col-md-6" data-aos="fade-left" data-aos-delay="150">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/Terapias em ABA.jpg" class="w-100 card-img-uniform" alt="Terapias em ABA">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Terapias em ABA</h4>
                                <p class="card-text">Aplicação da Análise do Comportamento para desenvolver habilidades
                                    sociais e cognitivas, especialmente no TEA.</p>
                            </div>
                        </div>
                    </a>
                </div>


                <div class="col-lg-5 col-md-6" data-aos="fade-right" data-aos-delay="0">
                    <a href="index-agend.html">
                        <div class="card shadow rounded-4 overflow-hidden h-100">
                            <img src="img/Psicomotricidade.jpg" class="w-100 card-img-uniform" alt="Psicomotricidade">
                            <div class="card-body bg-light p-3">
                                <h4 class="card-title text-primary">Psicomotricidade</h4>
                                <p class="card-text">Trabalho corporal que estimula o desenvolvimento motor, emocional e
                                    cognitivo.</p>
                            </div>
                        </div>
                    </a>
                </div>


            </div>
        </div>





    </main>


    <?php include 'incluir/footer.php'; ?>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>


    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>


    <script src="mobile-navbar.js"></script>


    <script src="mobile-navbar.js"></script>



</body>

</html>