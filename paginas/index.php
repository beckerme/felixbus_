<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Autocarros PHP</title>
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="Stylesm.css" rel="stylesheet" />
    <style>
        body {
            background-color: #7394e963;
        }
    </style>
</head>

<body>
    <!-- Responsive navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container px-5">
            <a class="navbar-brand" href="#!">Felix Buss</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <?php if (isset($_SESSION['user_nome'])): ?>
                    <!-- Se o cliente estiver logado -->
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
                    </ul>
                    <div class="d-flex align-items-center">
                        <img src="user_icon.png" alt="Perfil" style="width: 25px; height: 25px; border-radius: 50%; margin-right: 8px;">
                        <a href="clienteArea.php"><span class="text-white me-3"><?php echo htmlspecialchars($_SESSION['user_nome']); ?></span></a>
                        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
                    </div>
                <?php else: ?>
                    <!-- Se o cliente NÃO estiver logado -->
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php">Registo</a></li>
                        <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
                    </ul>
                <?php endif; ?>
            </div>

        </div>
    </nav>
    <style>
        /* Definições gerais de estilo */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #7394e963;
            color: #333;
        }

        /* Estilo para o título principal */
        h1,
        h2 {
            color: #343a40;
            font-weight: bold;
        }

        /* Estilo para as cards de administração */
        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card-body {
            padding: 4rem;
        }

        .card-title {
            font-size: 1.5rem;
            color: #007bff;
            margin-bottom: 1rem;
        }

        .card-text {
            font-size: 1rem;
            color: #555;
        }

        .card-footer {
            background-color: #fff;
            border-top: 1px solid #ddd;
            padding: 1rem;
            text-align: center;
        }

        /* Estilo para os botões */
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        /* Estilo para a área de boas-vindas */
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-color: #bee5eb;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        /* Estilo para os links */
        a {
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Ajustes responsivos */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem;
            }

            .btn-primary {
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
            }

            h1 {
                font-size: 2rem;
            }

            h2 {
                font-size: 1.25rem;
            }
        }
    </style>
    <!-- Page Content-->
    <div class="container px-4 px-lg-5">
        <!-- Heading Row-->
        <div class="row gx-4 gx-lg-5 align-items-center my-5">
            <div class="d-flex align-items-center">
                <img src="logo.png" alt="Logo da empresa" style="max-width: 100px; height: auto;" />
                <h1 class="font-weight-light ms-3">Felix Buss</h1>
            </div>
            <p>Bem-vindo ao nosso universo de autocarros, onde a mobilidade encontra o conforto e cada viagem é uma experiência única.</p>
        </div>

        <!-- Content Row-->
        <div class="row gx-4 gx-lg-5">
            <div class="col-md-4 mb-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="card-title">Horários</h2>
                        <p class="card-text">Consulte os horários atualizados de todas as nossas linhas e planeie a sua viagem com antecedência.</p>
                    </div>
                    <div class="card-footer"><a class="btn btn-primary btn-sm" href="#!">Mais informações</a></div>
                </div>
            </div>
            <div class="col-md-4 mb-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="card-title">Bilhetes</h2>
                        <p class="card-text">Adquira bilhetes de forma rápida e segura online ou nos nossos pontos de venda físicos.</p>
                    </div>
                    <div class="card-footer"><a class="btn btn-primary btn-sm" href="#!">Mais informações</a></div>
                </div>
            </div>
            <div class="col-md-4 mb-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="card-title">Rotas</h2>
                        <p class="card-text">Descubra todas as rotas disponíveis e escolha o percurso mais conveniente para si.</p>
                    </div>
                    <div class="card-footer"><a class="btn btn-primary btn-sm" href="#!">Mais informações</a></div>
                </div>
            </div>
        </div>

        <div class="row gx-4 gx-lg-5">
            <div class="col-md-4 mb-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="card-title">Parcerias</h2>
                        <p class="card-text">Conheça as nossas parcerias com empresas e entidades para descontos e vantagens especiais.</p>
                    </div>
                    <div class="card-footer"><a class="btn btn-primary btn-sm" href="#!">Mais informações</a></div>
                </div>
            </div>
            <div class="col-md-4 mb-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="card-title">Frota</h2>
                        <p class="card-text">Explore a nossa frota moderna e confortável, preparada para oferecer a melhor experiência de viagem.</p>
                    </div>
                    <div class="card-footer"><a class="btn btn-primary btn-sm" href="#!">Mais informações</a></div>
                </div>
            </div>
            <div class="col-md-4 mb-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="card-title">Contactos</h2>
                        <p class="card-text">Fale connosco para esclarecer dúvidas, fazer sugestões ou pedir apoio ao cliente.</p>
                    </div>
                    <div class="card-footer"><a class="btn btn-primary btn-sm" href="#!">Mais informações</a></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <footer class="py-5 bg-dark">
        <div class="container px-4 px-lg-5">
            <p class="m-0 text-center text-white">Copyright &copy; Felix Buss 2023</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>