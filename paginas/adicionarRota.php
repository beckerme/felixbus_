<?php
session_start();
require_once('../basedados/basedados.h');

// Verificar se o utilizador é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 3) { // 3 é o ID para administrador
    header("Location: semPermissao.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $origem = trim($_POST['origem']);
    $destino = trim($_POST['destino']);
    $preco = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT);
    $capacidade = filter_input(INPUT_POST, 'capacidade', FILTER_VALIDATE_INT);
    $data_viagem = $_POST['data_viagem'] ?? null;
    $hora_viagem = $_POST['hora_viagem'] ?? null;

    // Validações básicas
    $hoje = date('Y-m-d');
    $agora = date('H:i');
    $data_invalida = false;
    if ($data_viagem < $hoje) {
        $data_invalida = true;
    } elseif ($data_viagem == $hoje && $hora_viagem <= $agora) {
        $data_invalida = true;
    }
    if (empty($origem) || empty($destino) || $preco === false || $preco < 0 || $capacidade === false || $capacidade <= 0 || empty($data_viagem) || empty($hora_viagem) || $data_invalida) {
        $_SESSION['mensagem_erro'] = "Por favor, preencha todos os campos corretamente e utilize uma data/horário futuros.";
        header("Location: pagAddRota.php?erro=1");
        exit();
    }

    try {
        $sql = "INSERT INTO Rota (origem, destino, preco, capacidade) VALUES (:origem, :destino, :preco, :capacidade)";
        $stmt = $ligacao->prepare($sql);
        $stmt->bindParam(':origem', $origem);
        $stmt->bindParam(':destino', $destino);
        $stmt->bindParam(':preco', $preco);
        $stmt->bindParam(':capacidade', $capacidade);

        if ($stmt->execute()) {
            $rota_id = $ligacao->lastInsertId();
            // Inserir o primeiro horário na tabela Viagem
            $sql_viagem = "INSERT INTO Viagem (rota_id, data, hora) VALUES (:rota_id, :data_viagem, :hora_viagem)";
            $stmt_viagem = $ligacao->prepare($sql_viagem);
            $stmt_viagem->bindParam(':rota_id', $rota_id, PDO::PARAM_INT);
            $stmt_viagem->bindParam(':data_viagem', $data_viagem);
            $stmt_viagem->bindParam(':hora_viagem', $hora_viagem);
            $stmt_viagem->execute();

            $_SESSION['mensagem_sucesso'] = "Rota e primeiro horário adicionados com sucesso!";
            header("Location: gestaoRotas.php"); // Redirecionar para a página de gestão de rotas
            exit();
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao adicionar a rota.";
            header("Location: pagAddRota.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['mensagem_erro'] = "Erro de base de dados: " . $e->getMessage();
        header("Location: pagAddRota.php");
        exit();
    }
} else {
    // Se não for POST, redirecionar para o formulário
    header("Location: pagAddRota.php");
    exit();
}
?>
