<?php
session_start();
include_once("../basedados/basedados.h");

// Verifica se o utilizador é admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 3) {
    header("Location: semPermissao.php");
    exit();
}

// Verifica se o ID do utilizador a ser apagado foi passado via GET
if (isset($_GET['id'])) {
    $idUtilizadorApagar = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if ($idUtilizadorApagar) {
        try {
            // Não permitir que o admin se apague a si mesmo
            if ($idUtilizadorApagar == $_SESSION['user_id']) {
                $_SESSION['mensagem_erro'] = "Não pode apagar a sua própria conta de administrador.";
                header("Location: gestaoUtilizador.php");
                exit();
            }

            // Prepara a declaração para apagar o utilizador
            // Primeiro, apagar registos relacionados em Carteira (se houver)
            // Esta etapa é crucial se houver uma foreign key constraint
            // Se não houver foreign key ou se a ação ON DELETE CASCADE estiver definida, pode não ser necessário
            $sqlCarteira = "DELETE FROM Carteira WHERE utilizador_id = :id";
            $stmtCarteira = $ligacao->prepare($sqlCarteira);
            $stmtCarteira->bindParam(':id', $idUtilizadorApagar, PDO::PARAM_INT);
            $stmtCarteira->execute(); // Executa mesmo que não haja carteira, não dará erro

            // Depois, apagar o utilizador
            $sqlUtilizador = "DELETE FROM Utilizador WHERE id = :id";
            $stmtUtilizador = $ligacao->prepare($sqlUtilizador);
            $stmtUtilizador->bindParam(':id', $idUtilizadorApagar, PDO::PARAM_INT);

            if ($stmtUtilizador->execute()) {
                if ($stmtUtilizador->rowCount() > 0) {
                    $_SESSION['mensagem_sucesso'] = "Utilizador apagado com sucesso.";
                } else {
                    $_SESSION['mensagem_erro'] = "Utilizador não encontrado ou já apagado.";
                }
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao apagar o utilizador.";
            }
        } catch (PDOException $e) {
            // Em produção, logar o erro em vez de exibi-lo diretamente
            $_SESSION['mensagem_erro'] = "Erro na base de dados ao tentar apagar o utilizador. Detalhes: " . $e->getMessage();
        }
    } else {
        $_SESSION['mensagem_erro'] = "ID de utilizador inválido.";
    }
} else {
    $_SESSION['mensagem_erro'] = "ID de utilizador não especificado.";
}

// Redireciona de volta para a página de gestão de utilizadores
header("Location: gestaoUtilizador.php");
exit();
?>