<?php
session_start();
require_once('../basedados/basedados.h');

// Verificar se o utilizador é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 3) {
    header("Location: semPermissao.php");
    exit();
}

if (!isset($_GET['rota_id']) || !filter_var($_GET['rota_id'], FILTER_VALIDATE_INT)) {
    $_SESSION['mensagem_erro'] = "ID da rota inválido.";
    header("Location: gestaoRotas.php");
    exit();
}

$rota_id = $_GET['rota_id'];

// Buscar detalhes da rota
$sql_rota = "SELECT origem, destino FROM Rota WHERE id = :rota_id";
$stmt_rota = $ligacao->prepare($sql_rota);
$stmt_rota->bindParam(':rota_id', $rota_id, PDO::PARAM_INT);
$stmt_rota->execute();
$rota = $stmt_rota->fetch(PDO::FETCH_ASSOC);

if (!$rota) {
    $_SESSION['mensagem_erro'] = "Rota não encontrada.";
    header("Location: gestaoRotas.php");
    exit();
}

// Buscar horários (viagens) para esta rota
$sql_viagens = "SELECT id, data, hora FROM Viagem WHERE rota_id = :rota_id ORDER BY data, hora";
$stmt_viagens = $ligacao->prepare($sql_viagens);
$stmt_viagens->bindParam(':rota_id', $rota_id, PDO::PARAM_INT);
$stmt_viagens->execute();
$viagens = $stmt_viagens->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Horários - <?php echo htmlspecialchars($rota['origem']) . ' - ' . htmlspecialchars($rota['destino']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../estilos/style.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa; /* Cor de fundo suave */
        }
        .content-wrap {
            flex: 1;
            padding-bottom: 60px; /* Espaço para o footer */
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 1rem 0;
            position: relative; /* Alterado de fixed para relative ou pode ser sticky */
            bottom: 0;
            width: 100%;
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
                    <li class="nav-item"><a class="nav-link" href="gestaoRotas.php">Gestão de Rotas</a></li>
                    <!-- Outros links de admin -->
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container content-wrap mt-4">
        <h2 class="mb-4">Gestão de Horários para a Rota: <br><?php echo htmlspecialchars($rota['origem']); ?> <small class="text-muted">para</small> <?php echo htmlspecialchars($rota['destino']); ?></h2>

        <?php if (isset($_SESSION['mensagem_sucesso'])) : ?>
            <div class="alert alert-success"><?php echo $_SESSION['mensagem_sucesso']; unset($_SESSION['mensagem_sucesso']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['mensagem_erro'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['mensagem_erro']; unset($_SESSION['mensagem_erro']); ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">
                Adicionar Novo Horário
            </div>
            <div class="card-body">
                <form action="adicionarHorario.php" method="POST">
                    <input type="hidden" name="rota_id" value="<?php echo $rota_id; ?>">
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label for="data_viagem" class="form-label">Data da Viagem</label>
                            <input type="date" class="form-control" id="data_viagem" name="data_viagem" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="hora_viagem" class="form-label">Hora da Viagem</label>
                            <input type="time" class="form-control" id="hora_viagem" name="hora_viagem" required>
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Adicionar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <h3 class="mt-5 mb-3">Horários Existentes</h3>
        <?php if (count($viagens) > 0) : ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($viagens as $viagem) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($viagem['data']))); ?></td>
                                <td><?php echo htmlspecialchars(date('H:i', strtotime($viagem['hora']))); ?></td>
                                <td>
                                    <a href="pagEditarHorario.php?id=<?php echo $viagem['id']; ?>&rota_id=<?php echo $rota_id; ?>" class="btn btn-sm btn-info">
                                        <img src="pencil.png" alt="Editar" class="icon-img">
                                    </a>
                                    <a href="apagarHorario.php?id=<?php echo $viagem['id']; ?>&rota_id=<?php echo $rota_id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem a certeza que deseja apagar este horário?');">
                                        <img src="apagar.png" alt="Apagar" class="icon-img">
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <p class="text-center">Nenhum horário cadastrado para esta rota.</p>
        <?php endif; ?>
        <div class="mt-4">
            <a href="gestaoRotas.php" class="btn btn-secondary">Voltar para Gestão de Rotas</a>
        </div>
    </main>

    <footer class="footer mt-auto py-3 bg-dark">
        <div class="container text-center">
            <span class="text-muted">© <?php echo date("Y"); ?> Felix Buss</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
