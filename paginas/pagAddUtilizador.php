<?php
session_start();
require_once('../basedados/basedados.h');
// Verificar se o utilizador é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 3) { // 3 é o ID para administrador
    header("Location: semPermissao.php");
    exit();
}

// Buscar perfis para o dropdown
$perfis = [];
$sql_perfis = "SELECT id, nome FROM Perfil"; // Corrigido: nome da coluna para nome do perfil
$stmt_perfis = $ligacao->prepare($sql_perfis);
if ($stmt_perfis->execute()) {
    $perfis = $stmt_perfis->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Tratar erro, se necessário
    error_log("Erro ao buscar perfis: " . implode(";", $stmt_perfis->errorInfo()));
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Utilizador</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../estilos/style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <main class="container">
        <div class="form-container">
            <h2 class="text-center mb-4">Adicionar Novo Utilizador</h2>
            <?php
            if (isset($_SESSION['mensagem_erro'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['mensagem_erro'] . '</div>';
                unset($_SESSION['mensagem_erro']);
            }
            if (isset($_SESSION['mensagem_sucesso'])) {
                echo '<div class="alert alert-success">' . $_SESSION['mensagem_sucesso'] . '</div>';
                unset($_SESSION['mensagem_sucesso']);
            }
            ?>
            <form action="adicionarUtilizador.php" method="POST">
                <div class="form-group">
                    <label for="nome">Nome Completo:</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="telefone">Número de Telemóvel:</label> <!-- Corrigido: name para 'telefone' -->
                    <input type="tel" class="form-control" id="telefone" name="telefone" pattern="[0-9]{9}">
                </div>
                <div class="form-group">
                    <label for="perfil_id">Tipo Utilizador:</label>
                    <select class="form-control" id="perfil_id" name="perfil_id" required>
                        <option value="">Selecione o tipo de utilizador</option>
                        <?php foreach ($perfis as $perfil): ?>
                            <option value="<?php echo htmlspecialchars($perfil['id']); ?>"> <!-- Corrigido: id do perfil -->
                                <?php echo htmlspecialchars($perfil['nome']); ?> <!-- Corrigido: nome do perfil -->
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Adicionar Utilizador</button>
                <a href="gestaoUtilizador.php" class="btn btn-secondary btn-block mt-2">Cancelar</a>
            </form>
        </div>
    </main>

    <footer class="footer mt-auto py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">© 2024 FelixBus</span>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>