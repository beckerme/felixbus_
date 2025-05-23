<?php
session_start();
require_once('../basedados/basedados.h');

// Verificar se o utilizador é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 3) {
    header("Location: semPermissao.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['rota_id'], $_POST['data_viagem'], $_POST['hora_viagem']) || 
        !filter_var($_POST['rota_id'], FILTER_VALIDATE_INT)) {
        $_SESSION['mensagem_erro'] = "Dados inválidos.";
        header("Location: " . (isset($_POST['rota_id']) ? "gestaoHorarios.php?rota_id=" . $_POST['rota_id'] : "gestaoRotas.php"));
        exit();
    }

    $rota_id = $_POST['rota_id'];
    $data_viagem_input = $_POST['data_viagem'];
    $hora_viagem_input = $_POST['hora_viagem'];

    // Validar data e hora (simples validação, pode ser mais robusta)
    if (empty($data_viagem_input) || empty($hora_viagem_input)) {
        $_SESSION['mensagem_erro'] = "Data e hora são obrigatórias.";
        header("Location: gestaoHorarios.php?rota_id=" . $rota_id);
        exit();
    }

    // Validação de data/hora passada
    $data_hora_viagem_input = new DateTime($data_viagem_input . ' ' . $hora_viagem_input);
    $agora = new DateTime();

    if ($data_hora_viagem_input < $agora) {
        $_SESSION['mensagem_erro'] = "Não é possível adicionar um horário para uma data ou hora que já passou.";
        header("Location: gestaoHorarios.php?rota_id=" . $rota_id);
        exit();
    }

    // Verificar se já existe um horário igual para a mesma rota, data e hora
    try {
        $sql_check = "SELECT id FROM Viagem WHERE rota_id = :rota_id AND data_viagem = :data_viagem AND hora_viagem = :hora_viagem"; // Alterado de data para data_viagem e hora para hora_viagem
        $stmt_check = $ligacao->prepare($sql_check);
        $stmt_check->bindParam(':rota_id', $rota_id, PDO::PARAM_INT);
        $stmt_check->bindParam(':data_viagem', $data_viagem_input); // Alterado de data_viagem para data_viagem_input
        $stmt_check->bindParam(':hora_viagem', $hora_viagem_input); // Alterado de hora_viagem para hora_viagem_input
        $stmt_check->execute();

        if ($stmt_check->fetch()) {
            $_SESSION['mensagem_erro'] = "Já existe uma viagem cadastrada para esta rota, data e hora.";
            header("Location: gestaoHorarios.php?rota_id=" . $rota_id);
            exit();
        }

        $sql = "INSERT INTO Viagem (rota_id, data_viagem, hora_viagem) VALUES (:rota_id, :data_viagem, :hora_viagem)"; // Alterado de data para data_viagem e hora para hora_viagem
        $stmt = $ligacao->prepare($sql);
        $stmt->bindParam(':rota_id', $rota_id, PDO::PARAM_INT);
        $stmt->bindParam(':data_viagem', $data_viagem_input); // Alterado de data_viagem para data_viagem_input
        $stmt->bindParam(':hora_viagem', $hora_viagem_input); // Alterado de hora_viagem para hora_viagem_input

        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Horário adicionado com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao adicionar o horário.";
        }
    } catch (PDOException $e) {
        $_SESSION['mensagem_erro'] = "Erro de base de dados: " . $e->getMessage();
    }
    header("Location: gestaoHorarios.php?rota_id=" . $rota_id);
    exit();

} else {
    header("Location: gestaoRotas.php");
    exit();
}
?>
