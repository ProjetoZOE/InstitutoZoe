<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "instituto_zoe";


$sucesso = false;


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
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
        <?php include 'menu.php'; ?>
    </header>

    <main style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
        <section id="contatos" class="texto" style="padding: 20px; max-width: 600px; margin-top: 120px; position: relative; z-index: 1;">
            <h1 style="color: #004ba8; font-size: 30px; text-align: center; margin-bottom: 20px;">Fale Conosco</h1>
            <?php if ($sucesso): ?>
                <p style="color: green; text-align: center; margin-bottom: 20px;">Mensagem enviada com sucesso!</p>
            <?php endif; ?>
            <form method="POST" action="" style="display: flex; flex-direction: column; gap: 15px;">
                <div>
                    <label for="nome" style="color: #004ba8; font-weight: bold; margin-bottom: 10px; font-size: 18px;">Nome:</label>
                    <input type="text" id="nome" name="nome" required style="padding: 10px; border: 2px solid #004ba8; border-radius: 5px; font-size: 16px; width: 100%;">
                </div>
                <div>
                    <label for="email" style="color: #004ba8; font-weight: bold; margin-bottom: 10px; font-size: 18px;">E-mail:</label>
                    <input type="email" id="email" name="email" required style="padding: 10px; border: 2px solid #004ba8; border-radius: 5px; font-size: 16px; width: 100%;">
                </div>
                <div>
                    <label for="mensagem" style="color: #004ba8; font-weight: bold; margin-bottom: 10px; font-size: 18px;">Mensagem:</label>
                    <textarea id="mensagem" name="mensagem" required style="padding: 15px; border: 2px solid #004ba8; border-radius: 5px; font-size: 16px; width: 100%; min-height: 200px; resize: vertical;"></textarea>
                </div>
                <button type="submit" style="background-color: #004ba8; color: #f8f8f8; padding: 15px 30px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s; width: 200px; align-self: center; text-align: center;">
                    Enviar
                </button>
            </form>
        </section>
    </main>

    <?php include 'footer.php'; ?>

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

$conn->close();
?>