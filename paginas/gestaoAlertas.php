<?php
session_start();
require_once('../basedados/basedados.h');

// Verificar se o utilizador é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 3) { // 3 é o ID para administrador
    header("Location: semPermissao.php");
    exit();
}

// Buscar todos os alertas
$sql = "SELECT id, titulo, mensagem, data_inicio, data_fim, ativo FROM Alerta ORDER BY id DESC";
$stmt = $ligacao->prepare($sql);
$stmt->execute();
$alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Alertas</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../estilos/style.css"> 
    <style>
        .table-actions {
            white-space: nowrap;
        }
        .table-actions a, .table-actions button {
            margin-right: 5px;
            margin-bottom: 5px; /* Para melhor espaçamento em mobile */
        }
        .icon-img {
            width: 20px;
            height: 20px;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="index.php">BusOnline</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="areaPessoal.php">Área Pessoal</a>
                    </li>
                    <?php if ($_SESSION['user_perfil'] == 3): ?>
                        <li class="nav-item active">
                            <a class="nav-link" href="gestaoAlertas.php">Gestão de Alertas <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="gestaoUtilizador.php">Gestão de Utilizadores</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="container mt-4">
        <h2 class="mb-4">Gestão de Alertas, Informações e Promoções</h2>

        <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['mensagem_sucesso']; unset($_SESSION['mensagem_sucesso']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['mensagem_erro'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['mensagem_erro']; unset($_SESSION['mensagem_erro']); ?></div>
        <?php endif; ?>

        <a href="pagAddAlerta.php" class="btn btn-primary mb-3">Adicionar Novo Alerta</a>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Mensagem</th>
                        <th>Data Início</th>
                        <th>Data Fim</th>
                        <th>Estado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($alertas) > 0): ?>
                        <?php foreach ($alertas as $alerta): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($alerta['id']); ?></td>
                                <td><?php echo htmlspecialchars($alerta['titulo']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars(substr($alerta['mensagem'], 0, 100))) . (strlen($alerta['mensagem']) > 100 ? '...' : ''); ?></td>
                                <td><?php echo htmlspecialchars($alerta['data_inicio'] ? date('d/m/Y', strtotime($alerta['data_inicio'])) : '-'); ?></td>
                                <td><?php echo htmlspecialchars($alerta['data_fim'] ? date('d/m/Y', strtotime($alerta['data_fim'])) : '-'); ?></td>
                                <td><?php echo $alerta['ativo'] ? '<span class="badge badge-success">Ativo</span>' : '<span class="badge badge-danger">Inativo</span>'; ?></td>
                                <td class="table-actions">
                                    <a href="pagEditarAlerta.php?id=<?php echo $alerta['id']; ?>" class="btn btn-sm btn-info">
                                        <img src="pencil.png" alt="Editar" class="icon-img">
                                    </a>
                                    <a href="apagarAlerta.php?id=<?php echo $alerta['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem a certeza que deseja apagar este alerta?');">
                                        <img src="apagar.png" alt="Apagar" class="icon-img">
                                    </a>
                                    <a href="toggleAlerta.php?id=<?php echo $alerta['id']; ?>" class="btn btn-sm btn-<?php echo $alerta['ativo'] ? 'warning' : 'success'; ?>">
                                        <?php echo $alerta['ativo'] ? 'Desativar' : 'Ativar'; ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Nenhum alerta encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="footer mt-auto py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">© <?php echo date("Y"); ?> BusOnline</span>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
