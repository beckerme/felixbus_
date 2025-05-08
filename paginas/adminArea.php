<?php
session_start();

// Verifica se está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// (Opcional) Verifica o perfil
if ($_SESSION['user_perfil'] !== 3) { // Chave corrigida aqui
    header("Location: semPermissao.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Felix Buss</title>
        
        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link href="Stylesm.css" rel="stylesheet" />
        
        <style>
            /* Definições gerais de estilo */
            body {
                font-family: 'Arial', sans-serif;
               background-color: #7394e963;
                color: #333;
            }

            /* Estilo para o título principal */
            h1, h2 {
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
                padding: 3rem;
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
			.admin-welcome {
                background-color: #d0e7ff;
                color: black;
                border-radius: 8px;
                padding: 2rem;
                margin: 2rem 0;
                text-align: center;
            }
			
        </style>
    </head>
    
    <body>
        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container px-5">
                <a class="navbar-brand" href="index.php">Felix Buss</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Welcome Text for Administrators -->
        <div class="row gx-4 gx-lg-5">
            <div class="col-12">
                <div class="admin-welcome">
    <h2>Bem-vindo, <?php echo $_SESSION['user_nome']?>!</h2>
</div>
            </div>
        </div>
		<!-- Content Row  -->
        <div class="row gx-4 gx-lg-5">
    <div class="col-md-3 mb-5">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="card-title">Gestão de Utilizadores</h2>
                <p class="card-text">Gerencie todos os utilizadores da plataforma, incluindo criação, edição, remoção e visualização de dados.</p>
            </div>
            <div class="card-footer"><a class="btn btn-primary btn-sm" href="gestaoUtilizador.php">Aceder Pagina</a></div>
			
        </div>
    </div>

    <div class="col-md-3 mb-5">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="card-title">Gestão de Alertas/Informações/Promoções</h2>
                <p class="card-text">Controle alertas, informações e promoções exibidas para os utilizadores, otimizando a comunicação e promoções.</p>
            </div>
            <div class="card-footer"><a class="btn btn-primary btn-sm" href="gestaoInformacoes">Aceder Pagina</a></div>
        </div>
    </div>

    <div class="col-md-3 mb-5">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="card-title">Visualizar e Editar Dados Pessoais</h2>
                <p class="card-text">Permita que os utilizadores visualizem e editem suas informações pessoais de forma segura e prática.</p>
            </div>
            <div class="card-footer"><a class="btn btn-primary btn-sm" href="dadosPessoal">Aceder Pagina</a></div>
        </div>
    </div>
	<div class="col-md-3 mb-5">
    <div class="card h-100">
        <div class="card-body">
            <h2 class="card-title">Gestão de Rotas</h2>
            <p class="card-text">Adicione, edite ou remova rotas disponíveis, definindo horários, destinos e autocarros associados.</p>
        </div>
        <div class="card-footer"><a class="btn btn-primary btn-sm" href="gestaoRotas">Aceder Pagina</a></div>
    </div>
</div>

        <!-- Footer-->
        <footer class="py-5 bg-dark fixed-bottom">
            <div class="container px-4 px-lg-5"><p class="m-0 text-center text-white ">Copyright &copy; Felix Buss 2023</p></div>
			
        </footer>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    </body>
</html>