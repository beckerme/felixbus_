<?php
session_start();
require_once('../basedados/basedados.h');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 1) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carteira_id = intval($_POST['carteira_id']);
    $valor = floatval($_POST['valor']);
    $acao = $_POST['acao'];
    $user_id = $_SESSION['user_id'];
    $descricao = ($acao === 'adicionar') ? 'Adição de saldo pelo cliente' : 'Retirada de saldo pelo cliente';

    // Buscar saldo atual
    $stmt = $ligacao->prepare('SELECT saldo FROM carteira WHERE id = :cid AND utilizador_id = :uid');
    $stmt->bindParam(':cid', $carteira_id, PDO::PARAM_INT);
    $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $carteira = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$carteira) {
        $_SESSION['msg_carteira'] = 'Carteira não encontrada.';
        header('Location: carteiraCliente.php');
        exit();
    }

    if ($valor <= 0) {
        $_SESSION['msg_carteira'] = 'Valor inválido.';
        header('Location: carteiraCliente.php');
        exit();
    }

    if ($acao === 'adicionar') {
        $novo_saldo = $carteira['saldo'] + $valor;
        $tipo = 'adicionar';
        $carteira_destino = $carteira_id;
        $carteira_origem = null;
    } elseif ($acao === 'retirar') {
        if ($carteira['saldo'] < $valor) {
            $_SESSION['msg_carteira'] = 'Saldo insuficiente.';
            header('Location: carteiraCliente.php');
            exit();
        }
        $novo_saldo = $carteira['saldo'] - $valor;
        $tipo = 'retirar';
        $carteira_destino = null;
        $carteira_origem = $carteira_id;
    } else {
        $_SESSION['msg_carteira'] = 'Ação inválida.';
        header('Location: carteiraCliente.php');
        exit();
    }

    try {
        $ligacao->beginTransaction();
        $stmt = $ligacao->prepare('UPDATE carteira SET saldo = :novo_saldo WHERE id = :cid');
        $stmt->bindParam(':novo_saldo', $novo_saldo);
        $stmt->bindParam(':cid', $carteira_id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $ligacao->prepare('INSERT INTO movimentocarteira (valor, tipo, carteira_origem, carteira_destino, descricao) VALUES (:valor, :tipo, :carteira_origem, :carteira_destino, :descricao)');
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':carteira_origem', $carteira_origem);
        $stmt->bindParam(':carteira_destino', $carteira_destino);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->execute();
        $ligacao->commit();
        $_SESSION['msg_carteira'] = 'Operação realizada com sucesso!';
    } catch (PDOException $e) {
        $ligacao->rollBack();
        $_SESSION['msg_carteira'] = 'Erro: ' . $e->getMessage();
    }
    header('Location: carteiraCliente.php');
    exit();
} else {
    header('Location: carteiraCliente.php');
    exit();
}
