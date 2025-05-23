<?php
session_start();
require '../basedados/basedados.h'; // conexão com o PDO

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Verifica se os campos foram preenchidos
    if (empty($email) || empty($password)) {
        $_SESSION['erro_login'] = "Preencha todos os campos.";
        header("Location: login.php");
        exit();
    }

    // Consulta ao utilizador
    $stmt = $ligacao->prepare("SELECT * FROM utilizador WHERE email = :email LIMIT 1");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se encontrou utilizador e se a password confere
    if ($user && password_verify($password, $user['password'])) {
        // Iniciar sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nome'] = $user['nome'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_perfil'] = $user['perfil_id'];

        // Redireciona para a área pessoal unificada
        header("Location: areaPessoal.php");
        exit();
    } else {
        $_SESSION['erro_login'] = "Credenciais inválidas.";
        header("Location: [bbb]erro.php");
        exit();
    }
}
