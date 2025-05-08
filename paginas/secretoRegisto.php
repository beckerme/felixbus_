<?php
session_start();
require '../basedados/basedados.h'; // conexão com o PDO

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtenção e validação dos dados
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $telefone = trim($_POST['telefone'] ?? '');

    // Verificação de campos obrigatórios
    if (empty($nome) || empty($email) || empty($password)) {
        $_SESSION['registo_status'] = "Preencha todos os campos obrigatórios.";
        header("Location: register.php");
        exit;
    }

    try {
        // Verifica se o email já existe
        $stmt = $ligacao->prepare("SELECT id FROM Utilizador WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->fetch()) {
            $_SESSION['registo_status'] = "Este email já está registado.";
            header("Location: register.php");
            exit;
        }

        // Obtém o ID do perfil 'cliente'
        $perfilQuery = $ligacao->prepare("SELECT id FROM Perfil WHERE nome = 'cliente' LIMIT 1");
        $perfilQuery->execute();
        $perfil = $perfilQuery->fetch();

        if (!$perfil) {
            $_SESSION['registo_status'] = "Erro ao obter o perfil de cliente.";
            header("Location: register.php");
            exit;
        }

        $perfil_id = $perfil['id'];

        // Encripta a senha
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);

        // Inserção do utilizador
        $stmt = $ligacao->prepare("INSERT INTO Utilizador (nome, email, password, perfil_id) VALUES (:nome, :email, :password, :perfil_id)");
        $stmt->execute([
            'nome' => $nome,
            'email' => $email,
            'password' => $hashPassword,
            'perfil_id' => $perfil_id
        ]);

        $utilizador_id = $ligacao->lastInsertId();

        // Criação da carteira do utilizador
        $stmt = $ligacao->prepare("INSERT INTO Carteira (utilizador_id, saldo) VALUES (:utilizador_id, 0.00)");
        $stmt->execute(['utilizador_id' => $utilizador_id]);

        $_SESSION['registo_status'] = "Registo concluído com sucesso. Faça login!";
        header("Location: login.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['registo_status'] = "Erro no registo: " . $e->getMessage();
        header("Location: register.php");
        exit;
    }
} else {
    header("Location: register.php");
    exit;
}
?>