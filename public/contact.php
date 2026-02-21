
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/images/logo-Icone.png" type="image/x-icon">
    <title>Fale Conosco - Instituto Zoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

</head>

<body>
    <header>
        <?php include '../includes/menu.php'; ?>
    </header>

    <main style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
        <section id="contatos" class="texto" 
            style="
                padding: 20px; 
                max-width: 600px; 
                margin-top: 90px; 
                position: relative; 
                z-index: 1; 
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
                border-radius: 10px;
                
            ">
            
            <h1 style="color: #004ba8; font-size: 30px; text-align: center; margin-bottom: 20px;">Fale Conosco</h1>

            <form method="POST" action="" style="display: flex; flex-direction: column; gap: 15px;">
                 <div>
                    <label for="nome" style="color: #004ba8; font-weight: bold; margin-bottom: 10px; font-size: 18px;">Nome:</label>
                    <input type="text" id="nome" name="nome" required 
                        style="padding: 10px; border: 2px solid #004ba8; border-radius: 5px; font-size: 16px; width: 100%;">
                </div>

                <div>
                    <label for="assunto" style="color: #004ba8; font-weight: bold; margin-bottom: 10px; font-size: 18px;">Assunto:</label>
                    <input type="text" id="assunto" name="assunto" required 
                        style="padding: 10px; border: 2px solid #004ba8; border-radius: 5px; font-size: 16px; width: 100%;">
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
    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <script src="../assets/js/navbar.js"></script>
</body>
</html>