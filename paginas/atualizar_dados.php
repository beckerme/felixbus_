<?php
session_start();
require '../basedados/basedados.h';  // Conexão com o banco de dados

// Verificar se o cliente está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$nome = $_SESSION['user_nome'];

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $novo_nome = $_POST['nome'];
    $novo_email = $_POST['email'];

    // Verificar se o novo email já existe para outro utilizador
    $sql_verifica = "SELECT id FROM utilizador WHERE email = :email AND id != :id";
    $stmt_verifica = $ligacao->prepare($sql_verifica);
    $stmt_verifica->execute([':email' => $novo_email, ':id' => $userId]);

    if ($stmt_verifica->rowCount() > 0) {
        // Se o email já existir, redireciona com erro
        header("Location: clienteArea.php?erro=email");
        exit();
    }

    // Atualizar os dados na base de dados
    $sql = "UPDATE utilizador SET nome = :nome, email = :email WHERE id = :id";
    $stmt = $ligacao->prepare($sql);
    $stmt->execute([':nome' => $novo_nome, ':email' => $novo_email, ':id' => $userId]);

    // Atualizar a sessão com o novo nome e email
    $_SESSION['user_nome'] = $novo_nome;
    $_SESSION['user_email'] = $novo_email;

    // Redirecionar para a página com mensagem de sucesso
    header("Location: clienteArea.php?atualizado=1");
    exit();
}
?>
