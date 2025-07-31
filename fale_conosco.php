<?php
// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "instituto_zoe";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Processar o formulário de contato
$sucesso = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $mensagem = $_POST['mensagem'];

    if (!empty($nome) && !empty($email) && !empty($mensagem)) {
        // Inserir no banco de dados
        $sql = "INSERT INTO contatos (nome, email, mensagem) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nome, $email, $mensagem);
        if ($stmt->execute()) {
            $sucesso = true;

            // Enviar e-mail básico (opcional)
            $to = "seuemail@example.com"; // Substitua pelo seu e-mail
            $subject = "Nova mensagem de contato - Instituto Zoe";
            $body = "Nome: $nome\nE-mail: $email\nMensagem: $mensagem";
            $headers = "From: no-reply@institutozoe.com";
            mail($to, $subject, $body, $headers); // Função mail() do PHP
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/logo-Icone.png" type="image/x-icon">
    <title>Fale Conosco - Instituto Zoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>

<body>
    <header>
        <nav>
            <a class="logo" href="index.html" style="display: flex;align-items: center;"> <img src="img/logo.png" alt="Logo Instituto Zoe"></a>
            <ul class="nave-list">
                <li><a href="/">Campanhas</a></li>
                <li>
                    <button class="Drop btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Serviços
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Atividades</a></li>
                        <li><a class="dropdown-item" href="#">Saúde</a></li>
                    </ul>
                </li>
                <li><a href="/">Agendamento</a></li>
                <li><a href="/">Seja Apoiador(a)</a></li>
            </ul>
            <form id="searchForm">
                <input class="barra" type="text" id="searchInput" list="sugestoes" placeholder=" Pesquisar">
                <datalist id="sugestoes">
                    <option value="Quem somos">
                    <option value="O que fazemos">
                    <option value="Para quem fazemos">
                    <option value="Como pode ajudar">
                </datalist>
                <button type="submit" class="botao"><i class="bi bi-search" style="color: #004ba8;"></i></button>
            </form>
            <div class="social-icons nave-list">
                <a href="https://www.instagram.com/instituicao.zoe/" target="_blank"><i class="bi bi-instagram" style="font-size: 3vh;"></i></a>
                <a href="https://www.youtube.com/channel/UC7ONgI1ulSOE8iYjwWE3Kww" target="_blank"><i class="bi bi-youtube" style="font-size: 4vh;"></i></a>
                <a href="https://wa.me/5581973410768" target="_blank"><i class="bi bi-whatsapp" style="font-size: 3vh;"></i></a>
            </div>
            <div class="mobile-menu">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </div>
        </nav>
    </header>

    <main style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
        <section id="contato" class="texto" style="padding: 20px; max-width: 600px; margin-top: 80px;">
            <h1 style="color: #004ba8; font-size: 30px; text-align: center; margin-bottom: 20px;">Fale Conosco</h1>
            <?php if ($sucesso): ?>
                <p style="color: green; text-align: center; margin-bottom: 20px;">Mensagem enviada com sucesso!</p>
            <?php endif; ?>
            <form method="POST" action="" style="display: flex; flex-direction: column; gap: 15px; max-width: 600px; margin: 0 auto;">
                <div>
                    <label for="nome" style="color: #004ba8; font-weight: bold; margin-bottom: 5px; display: block;">Nome:</label>
                    <input type="text" id="nome" name="nome" required style="padding: 10px; border: 2px solid #004ba8; border-radius: 5px; font-size: 16px; width: 100%;">
                </div>
                <div>
                    <label for="email" style="color: #004ba8; font-weight: bold; margin-bottom: 5px; display: block;">E-mail:</label>
                    <input type="email" id="email" name="email" required style="padding: 10px; border: 2px solid #004ba8; border-radius: 5px; font-size: 16px; width: 100%;">
                </div>
                <div>
                    <label for="mensagem" style="color: #004ba8; font-weight: bold; margin-bottom: 5px; display: block;">Mensagem:</label>
                    <textarea id="mensagem" name="mensagem" required style="padding: 10px; border: 2px solid #004ba8; border-radius: 5px; font-size: 16px; width: 100%; min-height: 100px; resize: vertical;"></textarea>
                </div>
                <button type="submit" style="background-color: #004ba8; color: #f8f8f8; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s; width: fit-content; align-self: center;">
                    Enviar
                </button>
            </form>
        </section>
    </main>

    <footer class="footer text-center text-lg-start">
        <div class="container p-4">
            <div class="row align-items-center text-center text-md-start">
                <div class="col-md-4 mb-3 mb-md-0">
                    <p class="mb-0">&copy; 2025 - Instituto Zoe - Todos os direitos reservados</p>
                </div>
                <div class="col-md-4 mb-3 mb-md-0 d-flex justify-content-center">
                    <a class="logo d-flex align-items-center gap-2 text-decoration-none" href="#">
                        <img src="img/logo.png" alt="" style="height: 21vh;">
                    </a>
                </div>
                <div class="col-md-4 mb-3 mb-md-0 d-flex justify-content-md-end justify-content-center gap-3">
                    <a href="https://www.instagram.com/instituicao.zoe/" target="_blank" class="text-decoration-none social-link"><i class="bi bi-instagram fs-4" style="color: rgb(251, 251, 251);"></i></a>
                    <a href="https://www.youtube.com/channel/UC7ONgI1ulSOE8iYjwWE3Kww" target="_blank" class="text-decoration-none social-link"><i class="bi bi-youtube fs-4" style="color: rgb(251, 251, 251);"></i></a>
                    <a href="https://wa.me/5581973410768" target="_blank" class="text-decoration-none social-link"><i class="bi bi-whatsapp fs-4" style="color: rgb(251, 251, 251);"></i></a>
                </div>
                <div class="social-icons mt-3">
                    <a href="https://facebook.com"><i class="bi bi-facebook"></i></a>
                    <a href="fale_conosco.php" target="_blank"><i class="bi bi-youtube"></i></a>
                </div>
                <p class="mt-2"><a href="fale_conosco.php" target="_blank">Fale Conosco</a></p>
            </div>
        </div>
        <a href="#" class="scroll-to-top" id="scrollToTopBtn">
            <i class="bi bi-arrow-up-circle-fill"></i>
        </a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <script src="mobile-navbar.js"></script>
    <script>
        document.getElementById("searchForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const input = document.getElementById("searchInput").value.trim().toLowerCase();
            const palavrasChave = {
                "quem somos": "quem somos",
                "o que fazemos": "o que fazemos",
                "para quem fazemos": "para quem fazemos",
                "como pode ajudar": "como pode ajudar"
            };
            const destino = palavrasChave[input];
            if (destino) {
                const elemento = document.getElementById(destino);
                if (elemento) {
                    elemento.scrollIntoView({ behavior: "smooth" });
                }
            } else {
                alert("AVISO: PALAVRA-CHAVE NÃO ENCONTRADA.");
            }
        });
    </script>
</body>
</html>

<?php
// Fechar conexão
$conn->close();
?>