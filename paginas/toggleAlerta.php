<?php
session_start();
require_once('../basedados/basedados.h');

// Verificar se o utilizador é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 3) {
    $_SESSION['mensagem_erro'] = "Acesso negado.";
    header("Location: login.php");
    exit();
}

$alerta_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($alerta_id) {
    // Buscar o estado atual do alerta
    $sql_select = "SELECT ativo FROM Alerta WHERE id = :id";
    $stmt_select = $ligacao->prepare($sql_select);
    $stmt_select->bindParam(':id', $alerta_id, PDO::PARAM_INT);
    $stmt_select->execute();
    $alerta = $stmt_select->fetch(PDO::FETCH_ASSOC);

    if ($alerta) {
        $novo_estado = $alerta['ativo'] ? 0 : 1; // Inverter o estado

        $sql_update = "UPDATE Alerta SET ativo = :ativo WHERE id = :id";
        $stmt_update = $ligacao->prepare($sql_update);
        $stmt_update->bindParam(':ativo', $novo_estado, PDO::PARAM_INT);
        $stmt_update->bindParam(':id', $alerta_id, PDO::PARAM_INT);

        if ($stmt_update->execute()) {
            $_SESSION['mensagem_sucesso'] = "Estado do alerta atualizado com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao atualizar o estado do alerta. Tente novamente.";
            error_log("Erro ao atualizar estado do alerta ID $alerta_id: " . implode(";", $stmt_update->errorInfo()));
        }
    } else {
        $_SESSION['mensagem_erro'] = "Alerta não encontrado.";
    }
} else {
    $_SESSION['mensagem_erro'] = "ID do alerta inválido.";
}

header("Location: gestaoAlertas.php");
exit();
?>
