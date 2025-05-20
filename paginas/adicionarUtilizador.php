<?php
session_start();
require_once('../basedados/basedados.h');

// Verificar se o utilizador é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 3) { // 3 é o ID para administrador
    $_SESSION['mensagem_erro'] = "Acesso negado.";
    header("Location: login.php"); 
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar e validar inputs
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; 
    $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING); // Corrigido: para 'telefone'
    $perfil_id = filter_input(INPUT_POST, 'perfil_id', FILTER_VALIDATE_INT);

    // Validar campos obrigatórios
    if (empty($nome) || empty($email) || empty($password) || $perfil_id === false) {
        $_SESSION['mensagem_erro'] = "Todos os campos obrigatórios (Nome, Email, Password, Tipo Utilizador) devem ser preenchidos.";
        header("Location: pagAddUtilizador.php");
        exit();
    }

    // Validar formato do email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['mensagem_erro'] = "Formato de email inválido.";
        header("Location: pagAddUtilizador.php");
        exit();
    }

    // Validar número de telemóvel (opcional, mas se preenchido, validar formato)
    if (!empty($telefone) && !preg_match("/^[0-9]{9}$/", $telefone)) { // Corrigido: para $telefone
        $_SESSION['mensagem_erro'] = "Número de telemóvel inválido. Deve conter 9 dígitos.";
        header("Location: pagAddUtilizador.php");
        exit();
    }

    // Verificar se o email já existe
    $sql_check_email = "SELECT id FROM Utilizador WHERE email = :email"; // Corrigido: nome da coluna id_utilizador para id
    $stmt_check_email = $ligacao->prepare($sql_check_email);
    $stmt_check_email->bindParam(':email', $email);
    $stmt_check_email->execute();
    if ($stmt_check_email->rowCount() > 0) {
        $_SESSION['mensagem_erro'] = "Este email já está registado.";
        header("Location: pagAddUtilizador.php");
        exit();
    }

    // Hash da password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Inserir utilizador na tabela Utilizador
    // Corrigido: nomes das colunas para palavra_passe, n_telemovel, status_utilizador para password, telefone, ativo
    $sql_insert_user = "INSERT INTO Utilizador (nome, email, password, telefone, perfil_id, ativo) 
                        VALUES (:nome, :email, :password, :telefone, :perfil_id, 1)"; // ativo = 1 (ativo) por defeito
    $stmt_insert_user = $ligacao->prepare($sql_insert_user);
    $stmt_insert_user->bindParam(':nome', $nome);
    $stmt_insert_user->bindParam(':email', $email);
    $stmt_insert_user->bindParam(':password', $hashed_password);
    $stmt_insert_user->bindParam(':telefone', $telefone);
    $stmt_insert_user->bindParam(':perfil_id', $perfil_id, PDO::PARAM_INT);

    if ($stmt_insert_user->execute()) {
        $novo_user_id = $ligacao->lastInsertId();

        // Criar entrada na tabela Carteira para o novo utilizador
        // Corrigido: nome da coluna id_utilizador para utilizador_id
        $sql_insert_carteira = "INSERT INTO Carteira (utilizador_id, saldo) VALUES (:utilizador_id, 0.00)";
        $stmt_insert_carteira = $ligacao->prepare($sql_insert_carteira);
        $stmt_insert_carteira->bindParam(':utilizador_id', $novo_user_id, PDO::PARAM_INT);
        
        if ($stmt_insert_carteira->execute()) {
            $_SESSION['mensagem_sucesso'] = "Utilizador adicionado com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Utilizador adicionado, mas ocorreu um erro ao criar a carteira. Contacte o suporte.";
             error_log("Erro ao criar carteira para user ID $novo_user_id: " . implode(";", $stmt_insert_carteira->errorInfo()));
        }
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao adicionar utilizador. Tente novamente.";
        error_log("Erro ao inserir utilizador: " . implode(";", $stmt_insert_user->errorInfo()));
    }
    header("Location: gestaoUtilizador.php");
    exit();

} else {
    header("Location: pagAddUtilizador.php");
    exit();
}
?>