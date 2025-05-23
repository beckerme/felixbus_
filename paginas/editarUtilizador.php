<?php
session_start();
include_once("../basedados/basedados.h");

// Verifica se o utilizador é admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 3) {
    header("Location: semPermissao.php");
    exit();
}

$idUtilizadorEditar = null;
$utilizador = null;
$mensagem_erro = '';
$mensagem_sucesso = '';

// Verifica se o ID do utilizador foi passado via GET
if (isset($_GET['id'])) {
    $idUtilizadorEditar = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if ($idUtilizadorEditar) {
        try {
            $sql = "SELECT id, nome, email, telefone, perfil_id FROM Utilizador WHERE id = :id";
            $stmt = $ligacao->prepare($sql);
            $stmt->bindParam(':id', $idUtilizadorEditar, PDO::PARAM_INT);
            $stmt->execute();
            $utilizador = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$utilizador) {
                $_SESSION['mensagem_erro'] = "Utilizador não encontrado.";
                header("Location: gestaoUtilizador.php");
                exit();
            }
        } catch (PDOException $e) {
            $mensagem_erro = "Erro ao buscar dados do utilizador: " . $e->getMessage();
            // Considerar logar o erro em vez de exibir diretamente em produção
        }
    } else {
        $_SESSION['mensagem_erro'] = "ID de utilizador inválido.";
        header("Location: gestaoUtilizador.php");
        exit();
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Processar o formulário de edição
    $idUtilizadorEditar = filter_input(INPUT_POST, 'id_utilizador', FILTER_SANITIZE_NUMBER_INT);
    $nome = trim(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $telefone = trim(filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING));
    $perfil_id = filter_input(INPUT_POST, 'perfil_id', FILTER_SANITIZE_NUMBER_INT);
    // Validação básica (pode ser mais extensa)
    if (empty($nome) || empty($email) || empty($perfil_id)) {
        $mensagem_erro = "Nome, email e perfil são obrigatórios.";
        // Recarregar dados do utilizador para exibir o formulário novamente
        if ($idUtilizadorEditar) {
            $sql = "SELECT id, nome, email, telefone, perfil_id FROM Utilizador WHERE id = :id";
            $stmt = $ligacao->prepare($sql);
            $stmt->bindParam(':id', $idUtilizadorEditar, PDO::PARAM_INT);
            $stmt->execute();
            $utilizador = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem_erro = "Formato de email inválido.";
        if ($idUtilizadorEditar) { // Recarregar dados do utilizador
             $sql_reload = "SELECT id, nome, email, telefone, perfil_id FROM Utilizador WHERE id = :id";
             $stmt_reload = $ligacao->prepare($sql_reload);
             $stmt_reload->bindParam(':id', $idUtilizadorEditar, PDO::PARAM_INT);
             $stmt_reload->execute();
             $utilizador = $stmt_reload->fetch(PDO::FETCH_ASSOC);
        }
    } else {
        try {
            $sql = "UPDATE Utilizador SET nome = :nome, email = :email, telefone = :telefone, perfil_id = :perfil_id WHERE id = :id";
            $stmt = $ligacao->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':perfil_id', $perfil_id, PDO::PARAM_INT);
            $stmt->bindParam(':id', $idUtilizadorEditar, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $_SESSION['mensagem_sucesso'] = "Dados do utilizador atualizados com sucesso.";
                header("Location: gestaoUtilizador.php");
                exit();
            } else {
                $mensagem_erro = "Erro ao atualizar os dados do utilizador.";
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Código para violação de constraint UNIQUE (ex: email duplicado)
                $mensagem_erro = "Erro ao atualizar: O email fornecido já está em uso por outro utilizador.";
            } else {
                $mensagem_erro = "Erro na base de dados ao atualizar: " . $e->getMessage();
            }
            // Recarregar dados do utilizador para exibir o formulário novamente em caso de erro
            if ($idUtilizadorEditar) {
                $sql_reload_err = "SELECT id, nome, email, telefone, perfil_id FROM Utilizador WHERE id = :id";
                $stmt_reload_err = $ligacao->prepare($sql_reload_err);
                $stmt_reload_err->bindParam(':id', $idUtilizadorEditar, PDO::PARAM_INT);
                $stmt_reload_err->execute();
                $utilizador = $stmt_reload_err->fetch(PDO::FETCH_ASSOC);
                // Manter os dados que o utilizador tentou submeter, exceto se for o email duplicado
                if ($utilizador) {
                    $utilizador['nome'] = $nome;
                    if ($e->getCode() != 23000) {
                         $utilizador['email'] = $email; // Não repopular se for erro de email duplicado
                    }
                    $utilizador['telefone'] = $telefone;
                    $utilizador['perfil_id'] = $perfil_id;
                }
            }
        }
    }
} else {
    // Se não houver ID na URL (GET) nem submissão de formulário (POST), redirecionar
    $_SESSION['mensagem_erro'] = "Acesso inválido à página de edição.";
    header("Location: gestaoUtilizador.php");
    exit();
}

// Buscar perfis para o select
$perfis = [];
try {
    $stmtPerfis = $ligacao->query("SELECT id, nome FROM Perfil");
    $perfis = $stmtPerfis->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensagem_erro .= " Erro ao buscar perfis: " . $e->getMessage(); 
}

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Utilizador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet"> 
    <style>
        body {
            background-color: #f8f9fa; /* Cor de fundo suave */
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 30px;
            background-color: #fff; /* Fundo branco para o conteúdo principal */
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        footer {
            margin-top: auto; /* Empurra o footer para o final */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid px-5">
            <a class="navbar-brand" href="areaPessoal.php">Felix Buss - Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="gestaoUtilizador.php">Gestão de Utilizadores</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Editar Utilizador</h2>

        <?php if (!empty($mensagem_erro)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($mensagem_erro); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($mensagem_sucesso)): // Embora o sucesso redirecione, pode ser útil para debug ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($mensagem_sucesso); ?>
            </div>
        <?php endif; ?>

        <?php if ($utilizador): ?>
        <form action="editarUtilizador.php" method="POST">
            <input type="hidden" name="id_utilizador" value="<?php echo htmlspecialchars($utilizador['id']); ?>">
            
            <div class="mb-3">
                <label for="nome" class="form-label">Nome:</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($utilizador['nome']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($utilizador['email']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone:</label>
                <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo htmlspecialchars($utilizador['telefone'] ?? ''); ?>">
            </div>
            
            <div class="mb-3">
                <label for="perfil_id" class="form-label">Perfil:</label>
                <select class="form-select" id="perfil_id" name="perfil_id" required>
                    <?php foreach ($perfis as $perfil): ?>
                        <option value="<?php echo htmlspecialchars($perfil['id']); ?>"
                            <?php if ($utilizador['perfil_id'] == $perfil['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars(ucfirst($perfil['nome'])); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Guardar Alterações</button>
            <a href="gestaoUtilizador.php" class="btn btn-secondary">Cancelar</a>
        </form>
        <?php elseif (empty($mensagem_erro)): // Se $utilizador for null e não houver erro de busca inicial (já tratado com redirecionamento)
            echo "<p class='alert alert-warning'>Não foi possível carregar os dados do utilizador para edição.</p>";
        endif; ?>
    </div>

    <footer class="py-5 bg-dark text-white text-center">
        <div class="container px-4 px-lg-5">
            <p class="m-0">Copyright &copy; Felix Buss <?php echo date("Y"); ?></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
