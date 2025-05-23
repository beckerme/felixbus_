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
    // Verificar se o alerta existe antes de tentar apagar
    $sql_check = "SELECT id FROM Alerta WHERE id = :id";
    $stmt_check = $ligacao->prepare($sql_check);
    $stmt_check->bindParam(':id', $alerta_id, PDO::PARAM_INT);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        $sql_delete = "DELETE FROM Alerta WHERE id = :id";
        $stmt_delete = $ligacao->prepare($sql_delete);
        $stmt_delete->bindParam(':id', $alerta_id, PDO::PARAM_INT);

        if ($stmt_delete->execute()) {
            $_SESSION['mensagem_sucesso'] = "Alerta apagado com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao apagar o alerta. Tente novamente.";
            error_log("Erro ao apagar alerta ID $alerta_id: " . implode(";", $stmt_delete->errorInfo()));
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
