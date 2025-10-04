<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/logo-Icone.png" type="image/x-icon">
    <title>Instituto Zoe - Login e Cadastro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background-color: #fff;
        }
        main {
            padding: 0;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 80px;
        }
        .login-section {
            padding: 40px 0;
            background-color: #f8f9fa;
            width: 100%;
            text-align: center;
        }
        .login-form {
            max-width: 400px;
            margin: 0 auto;
        }
        .form-input {
            margin: 10px 0;
            padding: 8px;
            border: 2px solid #004ba8;
            border-radius: 5px;
            width: 100%;
            font-size: 16px;
        }
        .form-button {
            background-color: #004ba8;
            color: #fff;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-button:hover {
            background-color: #003580;
        }
        .tab-button {
            background: none;
            border: none;
            color: #004ba8;
            font-size: 18px;
            margin: 0 10px;
            cursor: pointer;
        }
        .tab-button.active {
            font-weight: bold;
            text-decoration: underline;
        }
        .error-message {
            color: #dc3545;
            font-size: 14px;
            display: none;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <header>
        <?php include 'menu.php'; ?>
    </header>
    <main>
        <section class="login-section">
            <div class="container">
                <h1 class="text-center" style="color: #004ba8; font-size: 32px; margin-bottom: 20px;">Acesse ou Cadastre-se</h1>
                <p class="text-center" style="color: #333; font-size: 18px; margin-bottom: 20px;">Gerencie sua conta Instituto Zoe.</p>

                <div class="mb-4">
                    <button class="tab-button active" onclick="showTab('login')">Login</button>
                    <button class="tab-button" onclick="showTab('cadastro')">Cadastro</button>
                </div>

                
                <div id="login-form" class="login-form">
                    <form>
                        <div class="mb-3">
                            <label for="login-email" class="form-label" style="color: #004ba8; font-weight: bold;">E-mail:</label>
                            <input type="email" class="form-input" id="login-email" required>
                        </div>
                        <div class="mb-3">
                            <label for="login-password" class="form-label" style="color: #004ba8; font-weight: bold;">Senha:</label>
                            <input type="password" class="form-input" id="login-password" required>
                        </div>
                        <button type="submit" class="form-button">Entrar</button>
                    </form>
                </div>

               
                <div id="cadastro-form" class="login-form" style="display: none;">
                    <form id="cadastro-form-submit">
                        <div class="mb-3">
                            <label for="cadastro-nome" class="form-label" style="color: #004ba8; font-weight: bold;">Nome:</label>
                            <input type="text" class="form-input" id="cadastro-nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="cadastro-email" class="form-label" style="color: #004ba8; font-weight: bold;">E-mail:</label>
                            <input type="email" class="form-input" id="cadastro-email" required>
                        </div>
                        <div class="mb-3">
                            <label for="cadastro-password" class="form-label" style="color: #004ba8; font-weight: bold;">Senha:</label>
                            <input type="password" class="form-input" id="cadastro-password" required>
                        </div>
                        <div class="mb-3">
                            <label for="cadastro-confirm-password" class="form-label" style="color: #004ba8; font-weight: bold;">Confirmação de Senha:</label>
                            <input type="password" class="form-input" id="cadastro-confirm-password" required>
                            <div id="password-error" class="error-message">As senhas não coincidem.</div>
                        </div>
                        <button type="submit" class="form-button" id="cadastro-button">Cadastrar</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <script src="mobile-navbar.js"></script>
    <script>
       
        function showTab(tab) {
            if (tab === 'login') {
                document.getElementById('login-form').style.display = 'block';
                document.getElementById('cadastro-form').style.display = 'none';
                document.querySelector('.tab-button[onclick="showTab(\'login\')"]').classList.add('active');
                document.querySelector('.tab-button[onclick="showTab(\'cadastro\')"]').classList.remove('active');
            } else {
                document.getElementById('login-form').style.display = 'none';
                document.getElementById('cadastro-form').style.display = 'block';
                document.querySelector('.tab-button[onclick="showTab(\'cadastro\')"]').classList.add('active');
                document.querySelector('.tab-button[onclick="showTab(\'login\')"]').classList.remove('active');
            }
        }

       
        document.getElementById('cadastro-form-submit').addEventListener('submit', function(event) {
            event.preventDefault(); 
            const password = document.getElementById('cadastro-password').value;
            const confirmPassword = document.getElementById('cadastro-confirm-password').value;
            const errorMessage = document.getElementById('password-error');

            if (password !== confirmPassword) {
                errorMessage.style.display = 'block';
                document.getElementById('cadastro-button').disabled = true;
            } else {
                errorMessage.style.display = 'none';
                document.getElementById('cadastro-button').disabled = false;
                alert('Cadastro enviado com sucesso! (Simulação)');
                
                this.reset();
            }
        });

       
        document.getElementById('cadastro-confirm-password').addEventListener('input', function() {
            const password = document.getElementById('cadastro-password').value;
            const confirmPassword = this.value;
            const errorMessage = document.getElementById('password-error');
            const button = document.getElementById('cadastro-button');

            if (password !== confirmPassword && confirmPassword) {
                errorMessage.style.display = 'block';
                button.disabled = true;
            } else {
                errorMessage.style.display = 'none';
                button.disabled = false;
            }
        });
    </script>
</body>

</html>