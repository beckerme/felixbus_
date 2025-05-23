<?php
session_start();
require_once('../basedados/basedados.h'); // <--- ADICIONADO ESTA LINHA

// Verifica se está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// (Opcional) Verifica o perfil para redirecionamento inicial, embora a exibição dos cards seja controlada abaixo
// Se desejar um redirecionamento estrito caso o perfil não seja nem cliente nem admin (ex: um perfil inválido)
// if ($_SESSION['user_perfil'] !== 1 && $_SESSION['user_perfil'] !== 3) { // Assumindo 1 para cliente, 3 para admin
//     header("Location: semPermissao.php");
//     exit();
// }

$user_perfil = $_SESSION['user_perfil']; // 1 para Cliente, 3 para Admin (ajuste conforme sua base de dados)

?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Área Pessoal do Utilizador" />
    <meta name="author" content="" />
    <title>Felix Buss - Área Pessoal</title>

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
            overflow-x: hidden;
            /* Impede o overflow horizontal */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content-wrap {
            flex: 1;
        }

        /* Estilo para o título principal */
        h1,
        h2 {
            color: #343a40;
            font-weight: bold;
        }

        /* Estilo para as cards */
        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
            height: 100%; /* Garante que todos os cards na mesma linha tenham a mesma altura */
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card-body {
            padding: 2rem; /* Ajustado para melhor visualização */
            flex-grow: 1; /* Permite que o corpo do card cresça */
        }

        .card-title {
            font-size: 1.5rem;
            color: #007bff;
            margin-bottom: 1rem;
        }

        .card-text {
            font-size: 1rem;
            color: #555;
            margin-bottom: 1.5rem; /* Adicionado espaço abaixo do texto */
        }

        .card-footer {
            background-color: #fff;
            border-top: 1px solid #ddd;
            padding: 1rem;
            text-align: center;
            margin-top: auto; /* Empurra o footer para o final do card */
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

        .user-welcome {
            background-color: transparent; /* Alterado de #d0e7ff para transparent */
            color: black;
            border-radius: 8px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: center;
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
                font-size: 1.25rem; /* Ajustado para consistência */
            }
        }
         footer {
            width: 100%;
            bottom: 0;
        }
    </style>
</head>

<body>
    <!-- Responsive navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container px-5">
            <a class="navbar-brand" href="index.php">Felix Buss</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link active" aria-current="page" href="areaPessoal.php">Área Pessoal</a></li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                        <?php if (isset($_SESSION['user_perfil']) && $_SESSION['user_perfil'] == 3): ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php">Registo</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php 
        // Buscar alertas ativos para exibir aos clientes e funcionários
        // Apenas se não for admin, ou se for admin mas também para ver como os outros veem.
        // Para este exemplo, vamos mostrar a todos os utilizadores logados.
        // Poderia adicionar uma condição como: if($_SESSION['user_perfil'] == 1 || $_SESSION['user_perfil'] == 2)
        
        $sql_alertas_ativos = "SELECT titulo, mensagem, data_inicio, data_fim FROM Alerta WHERE ativo = 1 AND (data_inicio IS NULL OR data_inicio <= CURDATE()) AND (data_fim IS NULL OR data_fim >= CURDATE()) ORDER BY id DESC LIMIT 5";
        $stmt_alertas_ativos = $ligacao->prepare($sql_alertas_ativos);
        $stmt_alertas_ativos->execute();
        $alertas_para_exibir = $stmt_alertas_ativos->fetchAll(PDO::FETCH_ASSOC);

        if (count($alertas_para_exibir) > 0) {
            echo '<div class="alertas-container mb-4">';
            echo '<h5>Alertas e Novidades:</h5>';
            foreach ($alertas_para_exibir as $alerta_exibir) {
                // Adiciona um ID único para cada alerta para o JavaScript poder encontrá-lo
                $alerta_id_dom = 'alerta-' . uniqid(); 
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert" id="' . $alerta_id_dom . '">';
                echo '<strong>' . htmlspecialchars($alerta_exibir['titulo']) . '</strong><br>';
                echo nl2br(htmlspecialchars($alerta_exibir['mensagem']));
                if ($alerta_exibir['data_inicio'] || $alerta_exibir['data_fim']) {
                    echo '<small class="form-text text-muted">';
                    if ($alerta_exibir['data_inicio']) {
                        echo ' Válido de: ' . htmlspecialchars(date('d/m/Y', strtotime($alerta_exibir['data_inicio']))) . ' ';
                    }
                    if ($alerta_exibir['data_fim']) {
                        echo ' até: ' . htmlspecialchars(date('d/m/Y', strtotime($alerta_exibir['data_fim']))) . ' ';
                    }
                    echo '</small>';
                }
                // Botão de fechar do Bootstrap
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>

        <div class="container px-4 px-lg-5 content-wrap">
            <!-- Welcome Text -->
            <div class="row gx-4 gx-lg-5">
                <div class="col-12">
                    <div class="user-welcome">
                        <h2>Bem-vindo(a) à sua área pessoal, <?php echo htmlspecialchars($_SESSION['user_nome']); ?>!</h2>
                    </div>
                </div>
            </div>

            <!-- Content Row for Cards -->
            <div class="row gx-4 gx-lg-5">

                <!-- Card Comum: Visualizar e Editar Dados Pessoais -->
                <div class="col-md-4 mb-5">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="card-title">Os Seus Dados</h2>
                            <p class="card-text">Visualize e edite as suas informações pessoais de forma segura e prática.</p>
                        </div>
                        <div class="card-footer"><a class="btn btn-primary btn-sm" href="editarDadosPag.php">Aceder Página</a>
                        </div>
                    </div>
                </div>

                <?php if ($user_perfil === 3): // Perfil Administrador ?>
                    <!-- Cards Específicos do Administrador -->
                    <div class="col-md-4 mb-5">
                        <div class="card h-100">
                            <div class="card-body">
                                <h2 class="card-title">Gestão de Utilizadores</h2>
                                <p class="card-text">Gerencie todos os utilizadores da plataforma (criação, edição, remoção).</p>
                            </div>
                            <div class="card-footer"><a class="btn btn-primary btn-sm" href="gestaoUtilizador.php">Aceder Página</a></div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-5">
                        <div class="card h-100">
                            <div class="card-body">
                                <h2 class="card-title">Gestão de Alertas</h2>
                                <p class="card-text">Controle alertas, informações e promoções exibidas para os utilizadores.</p>
                            </div>
                            <div class="card-footer"><a class="btn btn-primary btn-sm" href="gestaoAlertas.php">Aceder Página</a></div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-5">
                        <div class="card h-100">
                            <div class="card-body">
                                <h2 class="card-title">Gestão de Rotas</h2>
                                <p class="card-text">Adicione, edite ou remova rotas, horários e autocarros associados.</p>
                            </div>
                            <div class="card-footer"><a class="btn btn-primary btn-sm" href="gestaoRotas.php">Aceder Página</a></div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-5">
                        <div class="card h-100">
                            <div class="card-body">
                                <h2 class="card-title">Gestão de Saldo (Admin)</h2>
                                <p class="card-text">Administre o saldo dos utilizadores e transações.</p>
                            </div>
                            <div class="card-footer"><a class="btn btn-primary btn-sm" href="gestaoSaldo.php">Aceder Página</a></div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-5">
                        <div class="card h-100">
                            <div class="card-body">
                                <h2 class="card-title">Gestão de Bilhetes (Admin)</h2>
                                <p class="card-text">Visualize e administre os bilhetes comprados pelos utilizadores.</p>
                            </div>
                            <div class="card-footer"><a class="btn btn-primary btn-sm" href="gestaoBilhetes.php">Aceder Página</a></div>
                        </div>
                    </div>

                <?php elseif ($user_perfil === 1 || $user_perfil === 2): // Perfil Cliente (assumindo 1 ou 2 para cliente) ?>
                    <!-- Cards Específicos do Cliente -->
                    <div class="col-md-4 mb-5">
                        <div class="card h-100">
                            <div class="card-body">
                                <h2 class="card-title">Comprar Bilhetes</h2>
                                <p class="card-text">Consulte horários, rotas e compre os seus bilhetes de forma fácil.</p>
                            </div>
                            <div class="card-footer"><a class="btn btn-primary btn-sm" href="comprarBilhetes.php">Aceder Página</a></div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-5">
                        <div class="card h-100">
                            <div class="card-body">
                                <h2 class="card-title">A Sua Carteira</h2>
                                <p class="card-text">Consulte o seu saldo, carregue a carteira e veja o histórico de transações.</p>
                            </div>
                            <div class="card-footer"><a class="btn btn-primary btn-sm" href="carteiraCliente.php">Aceder Página</a></div>
                        </div>
                    </div>
                     <div class="col-md-4 mb-5">
                        <div class="card h-100">
                            <div class="card-body">
                                <h2 class="card-title">Os Seus Bilhetes</h2>
                                <p class="card-text">Visualize os seus bilhetes comprados e o histórico de viagens.</p>
                            </div>
                            <div class="card-footer"><a class="btn btn-primary btn-sm" href="meusBilhetes.php">Aceder Página</a></div>
                        </div>
                    </div>


                <?php else: ?>
                    <div class="col-12">
                        <p class="text-center">Perfil de utilizador não reconhecido. Contacte o suporte.</p>
                    </div>
                <?php endif; ?>

            </div> <!-- Fim da Content Row for Cards -->
        </div> <!-- Fim do .content-wrap -->

    <!-- Footer-->
    <footer class="py-5 bg-dark">
        <div class="container px-4 px-lg-5">
            <p class="m-0 text-center text-white">Copyright &copy; Felix Buss <?php echo date("Y"); ?></p>
        </div>
    </footer>

    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <!-- Core theme JS-->
    <script src="js/scripts.js"></script> <!-- Se tiver um js/scripts.js, senão pode remover -->
</body>

</html>
