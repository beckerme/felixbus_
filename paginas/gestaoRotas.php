<?php
session_start();
require_once('../basedados/basedados.h');

// Verificar se o utilizador é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 3) { // 3 é o ID para administrador
    header("Location: semPermissao.php");
    exit();
}

// Buscar todas as rotas
$sql_rotas = "SELECT id, origem, destino, preco, capacidade FROM Rota ORDER BY origem, destino"; // Corrigido de id_rota para id
$stmt_rotas = $ligacao->prepare($sql_rotas);
$stmt_rotas->execute();
$rotas = $stmt_rotas->fetchAll(PDO::FETCH_ASSOC);

// Para cada rota, buscar seus horários
$rotas_com_horarios = [];
foreach ($rotas as $rota) {
    $sql_horarios = "SELECT data, hora FROM Viagem WHERE rota_id = :rota_id ORDER BY data, hora";
    $stmt_horarios = $ligacao->prepare($sql_horarios);
    $stmt_horarios->bindParam(':rota_id', $rota['id'], PDO::PARAM_INT); // Corrigido de id_rota para id
    $stmt_horarios->execute();
    $rota['horarios'] = $stmt_horarios->fetchAll(PDO::FETCH_ASSOC);
    $rotas_com_horarios[] = $rota;
}
$rotas = $rotas_com_horarios; // Substitui o array original
?>
<!doctype html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Felix Buss - Gestão de Rotas</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="../estilos/style.css" rel="stylesheet"> <!-- Assumindo que tem um style.css geral -->
    <style>
        .despesas-tabela {
            margin: 20px 0px;
        }
        .table th {
            text-align: center;
        }
        .table td {
            text-align: center;
        }
        body {
            background-color: #7394e963;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content-wrap {
            flex: 1;
        }
        .total {
            text-align: right;
        }
        .btnAdicionar {
            text-align: right;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .btn-apagar {
            width: 50px;
            height: 50px;
        }
        .editar {
            width: 50px;
            height: 50px;
        }
        .icon-img {
            width: 20px;
            height: 20px;
        }
    </style>

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container px-5">
            <a class="navbar-brand" href="areaPessoal.php">Felix Buss</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="areaPessoal.php">Área Pessoal</a></li>
                    <?php if ($_SESSION['user_perfil'] == 3): ?>
                        <li class="nav-item"><a class="nav-link" href="gestaoUtilizador.php">Gestão de Utilizadores</a></li>
                        <li class="nav-item active"><a class="nav-link" aria-current="page" href="gestaoRotas.php">Gestão de Rotas</a></li>
                        <li class="nav-item"><a class="nav-link" href="gestaoAlertas.php">Gestão de Alertas</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
   
    <div class="container content-wrap mt-4">
        <h2 class="mb-4 text-center">Gestão de Rotas</h2>

        <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['mensagem_sucesso']; unset($_SESSION['mensagem_sucesso']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['mensagem_erro'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['mensagem_erro']; unset($_SESSION['mensagem_erro']); ?></div>
        <?php endif; ?>

        <div class="row mb-3">
            <div class="col-md-12 text-end">
                <a class="btn btn-primary" href="pagAddRota.php">Adicionar Nova Rota</a>
            </div>                          
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover despesas-tabela">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Origem</th>
                                <th scope="col">Destino</th>
                                <th scope="col">Preço (€)</th>
                                <th scope="col">Capacidade</th>
                                <th scope="col">Horários</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($rotas) > 0): ?>
                                <?php foreach ($rotas as $rota): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($rota['id']); ?></td> <!-- Corrigido de id_rota para id -->
                                        <td><?php echo htmlspecialchars($rota['origem']); ?></td>
                                        <td><?php echo htmlspecialchars($rota['destino']); ?></td>
                                        <td><?php echo htmlspecialchars(number_format($rota['preco'], 2, ',', '.')); ?></td>
                                        <td><?php echo htmlspecialchars($rota['capacidade']); ?></td>
                                        <td>
                                            <?php if (count($rota['horarios']) > 0): ?>
                                                <ul class="list-unstyled mb-0">
                                                    <?php foreach ($rota['horarios'] as $horario): ?>
                                                        <li><?php echo htmlspecialchars(date('d/m/Y', strtotime($horario['data']))) . ' - ' . htmlspecialchars(date('H:i', strtotime($horario['hora']))); ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                Nenhum horário definido.
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="pagEditarRota.php?id=<?php echo $rota['id']; ?>" class="btn btn-sm btn-info mb-1">
                                                <img src="pencil.png" alt="Editar Rota" class="icon-img">
                                            </a>
                                            <a href="apagarRota.php?id=<?php echo $rota['id']; ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Tem a certeza que deseja apagar esta rota? Ao apagar a rota, todas as viagens associadas também serão removidas.');">
                                                <img src="apagar.png" alt="Apagar Rota" class="icon-img">
                                            </a>
                                            <a href="gestaoHorarios.php?rota_id=<?php echo $rota['id']; ?>" class="btn btn-sm btn-success">Gerir Horários</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Nenhuma rota encontrada.</td> <!-- Colspan aumentado para 7 -->
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer-->
    <footer class="py-5 bg-dark mt-auto">
        <div class="container px-4 px-lg-5"><p class="m-0 text-center text-white">Copyright &copy; Felix Buss <?php echo date("Y"); ?></p></div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>