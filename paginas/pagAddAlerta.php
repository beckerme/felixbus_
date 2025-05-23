<?php
session_start();
require_once('../basedados/basedados.h');

// Verificar se o utilizador é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 3) {
    header("Location: semPermissao.php");
    exit();
}

$titulo = '';
$mensagem = '';
$data_inicio = '';
$data_fim = '';
$ativo = 1; // Por defeito, o alerta é criado como ativo

$page_title = "Adicionar Novo Alerta";
$form_action = "pagAddAlerta.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
    $mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_STRING);
    $data_inicio = filter_input(INPUT_POST, 'data_inicio');
    $data_fim = filter_input(INPUT_POST, 'data_fim');
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    if (empty($titulo) || empty($mensagem)) {
        $_SESSION['mensagem_erro'] = "Título e Mensagem são obrigatórios.";
    } else {
        // Validar datas se fornecidas
        if (!empty($data_inicio) && !DateTime::createFromFormat('Y-m-d', $data_inicio)) {
            $_SESSION['mensagem_erro'] = "Formato da Data de Início inválido.";
        } elseif (!empty($data_fim) && !DateTime::createFromFormat('Y-m-d', $data_fim)) {
            $_SESSION['mensagem_erro'] = "Formato da Data de Fim inválido.";
        } elseif (!empty($data_inicio) && !empty($data_fim) && $data_fim < $data_inicio) {
            $_SESSION['mensagem_erro'] = "A Data de Fim não pode ser anterior à Data de Início.";
        } else {
            $sql = "INSERT INTO Alerta (titulo, mensagem, data_inicio, data_fim, ativo) VALUES (:titulo, :mensagem, :data_inicio, :data_fim, :ativo)";
            $stmt = $ligacao->prepare($sql);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':mensagem', $mensagem);
            $stmt->bindParam(':data_inicio', $data_inicio, PDO::PARAM_STR);
            $stmt->bindParam(':data_fim', $data_fim, PDO::PARAM_STR);
            $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $_SESSION['mensagem_sucesso'] = "Alerta adicionado com sucesso!";
                header("Location: gestaoAlertas.php");
                exit();
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao adicionar alerta. Tente novamente.";
                error_log("Erro ao inserir alerta: " . implode(";", $stmt->errorInfo()));
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../estilos/style.css">
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
                     <li class="nav-item">
                        <a class="nav-link" href="gestaoAlertas.php">Gestão de Alertas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="container mt-4">
        <h2><?php echo $page_title; ?></h2>

        <?php if (isset($_SESSION['mensagem_erro'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['mensagem_erro']; unset($_SESSION['mensagem_erro']); ?></div>
        <?php endif; ?>

        <form action="<?php echo $form_action; ?>" method="POST">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo htmlspecialchars($titulo); ?>" required>
            </div>
            <div class="form-group">
                <label for="mensagem">Mensagem:</label>
                <textarea class="form-control" id="mensagem" name="mensagem" rows="5" required><?php echo htmlspecialchars($mensagem); ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="data_inicio">Data de Início (opcional):</label>
                    <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?php echo htmlspecialchars($data_inicio); ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="data_fim">Data de Fim (opcional):</label>
                    <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?php echo htmlspecialchars($data_fim); ?>">
                </div>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="ativo" name="ativo" value="1" <?php echo $ativo ? 'checked' : ''; ?>>
                <label class="form-check-label" for="ativo">Ativo</label>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Alerta</button>
            <a href="gestaoAlertas.php" class="btn btn-secondary">Cancelar</a>
        </form>
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
