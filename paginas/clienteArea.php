<?php
session_start();
require '../basedados/basedados.h';  // Conexão com o banco de dados

// Redireciona se o cliente não estiver logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$query = $ligacao->prepare("SELECT nome, email FROM utilizador WHERE id = ?");
$query->execute([$userId]);
$cliente = $query->fetch();
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Perfil do Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background-color: #7394e963;" class="text-dark">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container px-5">
            <a class="navbar-brand" href="index.php">Felix Buss</a>
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

    <div class="container mt-5">
        <h2 class="mb-4">Perfil Pessoal</h2>

        <?php if (isset($_GET['erro']) && $_GET['erro'] === 'email'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Erro:</strong> Este email já está em uso.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['atualizado'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Dados atualizados com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>

        <div class="card bg-secondary text-white mb-4">
            <div class="card-body">
                <h5 class="card-title">Dados Pessoais</h5>
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($cliente['nome']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($cliente['email']); ?></p>
                <!-- Adicione mais dados se necessário -->
                <button class="btn btn-light mt-3" data-bs-toggle="modal" data-bs-target="#editarModal">Editar Dados</button>
            </div>
        </div>
    </div>

    <!-- Modal para edição -->
    <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content bg-dark text-white" method="POST" action="atualizar_dados.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarModalLabel">Editar Dados</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?php echo $userId; ?>">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" name="nome" id="nome" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
                    </div>
                    <!-- Outros campos conforme necessidade -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Alterações</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
