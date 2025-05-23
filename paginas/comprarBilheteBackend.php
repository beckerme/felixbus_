<?php
session_start();
require_once('../basedados/basedados.h');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 1) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['viagem_id'], $_POST['preco'])) {
    header('Location: comprarBilhetes.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$viagem_id = intval($_POST['viagem_id']);
$preco = floatval($_POST['preco']);

// Buscar carteira do cliente
$stmt = $ligacao->prepare('SELECT id, saldo FROM carteira WHERE utilizador_id = :uid');
$stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
$stmt->execute();
$carteira = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$carteira) {
    $_SESSION['msg_bilhete'] = 'Carteira não encontrada.';
    header('Location: comprarBilhetes.php');
    exit();
}

// Buscar carteira da FelixBus (utilizador_id NULL)
$stmt = $ligacao->prepare('SELECT id, saldo FROM carteira WHERE utilizador_id IS NULL LIMIT 1');
$stmt->execute();
$carteira_felix = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$carteira_felix) {
    $_SESSION['msg_bilhete'] = 'Carteira da FelixBus não encontrada.';
    header('Location: comprarBilhetes.php');
    exit();
}

// Verificar saldo
if ($carteira['saldo'] < $preco) {
    $_SESSION['msg_bilhete'] = 'Saldo insuficiente.';
    header('Location: comprarBilhetes.php');
    exit();
}

// Verificar lugares disponíveis
$stmt = $ligacao->prepare('SELECT r.capacidade, (SELECT COUNT(*) FROM bilhete b WHERE b.viagem_id = v.id) as vendidos FROM Viagem v JOIN Rota r ON v.rota_id = r.id WHERE v.id = :vid');
$stmt->bindParam(':vid', $viagem_id, PDO::PARAM_INT);
$stmt->execute();
$viagem = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$viagem || ($viagem['capacidade'] - $viagem['vendidos']) <= 0) {
    $_SESSION['msg_bilhete'] = 'Viagem esgotada.';
    header('Location: comprarBilhetes.php');
    exit();
}

try {
    $ligacao->beginTransaction();
    // Debitar cliente
    $novo_saldo_cliente = $carteira['saldo'] - $preco;
    $stmt = $ligacao->prepare('UPDATE carteira SET saldo = :s WHERE id = :cid');
    $stmt->bindParam(':s', $novo_saldo_cliente);
    $stmt->bindParam(':cid', $carteira['id']);
    $stmt->execute();
    // Creditar FelixBus
    $novo_saldo_felix = $carteira_felix['saldo'] + $preco;
    $stmt = $ligacao->prepare('UPDATE carteira SET saldo = :s WHERE id = :cid');
    $stmt->bindParam(':s', $novo_saldo_felix);
    $stmt->bindParam(':cid', $carteira_felix['id']);
    $stmt->execute();
    // Registrar movimento
    $desc = 'Compra de bilhete viagem #' . $viagem_id;
    $stmt = $ligacao->prepare('INSERT INTO movimentocarteira (valor, tipo, carteira_origem, carteira_destino, descricao) VALUES (:v, "transferencia", :co, :cd, :d)');
    $stmt->bindParam(':v', $preco);
    $stmt->bindParam(':co', $carteira['id']);
    $stmt->bindParam(':cd', $carteira_felix['id']);
    $stmt->bindParam(':d', $desc);
    $stmt->execute();
    // Gerar código de validação
    $codigo = strtoupper(bin2hex(random_bytes(4)));
    // Registrar bilhete
    $stmt = $ligacao->prepare('INSERT INTO bilhete (cliente_id, viagem_id, codigo_validacao) VALUES (:cid, :vid, :cod)');
    $stmt->bindParam(':cid', $user_id);
    $stmt->bindParam(':vid', $viagem_id);
    $stmt->bindParam(':cod', $codigo);
    $stmt->execute();
    $ligacao->commit();
    $_SESSION['msg_bilhete'] = 'Bilhete comprado com sucesso! Código: ' . $codigo;
} catch (PDOException $e) {
    $ligacao->rollBack();
    $_SESSION['msg_bilhete'] = 'Erro ao comprar bilhete: ' . $e->getMessage();
}
header('Location: comprarBilhetes.php');
exit();
