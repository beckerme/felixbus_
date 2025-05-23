<?php
session_start();
require_once('../basedados/basedados.h');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 1) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Buscar saldo atual
$stmt = $ligacao->prepare('SELECT c.id, c.saldo FROM carteira c WHERE c.utilizador_id = :uid');
$stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
$stmt->execute();
$carteira = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$carteira) {
    die('Carteira não encontrada.');
}

// Mensagem de feedback
$msg = '';
if (isset($_SESSION['msg_carteira'])) {
    $msg = $_SESSION['msg_carteira'];
    unset($_SESSION['msg_carteira']);
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Minha Carteira</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Minha Carteira</h2>
    <?php if ($msg): ?>
        <div class="alert alert-info text-center"><?php echo $msg; ?></div>
    <?php endif; ?>
    <div class="card mb-4">
        <div class="card-body">
            <h4>Saldo atual: <span class="text-success">€ <?php echo number_format($carteira['saldo'], 2, ',', '.'); ?></span></h4>
        </div>
    </div>
    <form action="operacaoCarteira.php" method="post" class="mb-3">
        <input type="hidden" name="carteira_id" value="<?php echo $carteira['id']; ?>">
        <div class="row mb-2">
            <div class="col-md-6">
                <input type="number" name="valor" class="form-control" placeholder="Valor (€)" min="0.01" step="0.01" required>
            </div>
            <div class="col-md-6 d-flex gap-2">
                <button type="submit" name="acao" value="adicionar" class="btn btn-success">Adicionar Saldo</button>
                <button type="submit" name="acao" value="retirar" class="btn btn-danger">Retirar Saldo</button>
            </div>
        </div>
    </form>
    <a href="areaPessoal.php" class="btn btn-secondary">Voltar</a>
    <hr>
    <h5>Movimentos da Carteira</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Data</th>
                <th>Tipo</th>
                <th>Valor (€)</th>
                <th>Descrição</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $stmt = $ligacao->prepare('SELECT data, tipo, valor, descricao FROM movimentocarteira WHERE carteira_origem = :cid OR carteira_destino = :cid ORDER BY data DESC');
        $stmt->bindParam(':cid', $carteira['id'], PDO::PARAM_INT);
        $stmt->execute();
        $movs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($movs) {
            foreach ($movs as $mov) {
                echo '<tr>';
                echo '<td>' . date('d/m/Y H:i', strtotime($mov['data'])) . '</td>';
                echo '<td>' . ucfirst($mov['tipo']) . '</td>';
                echo '<td>' . number_format($mov['valor'], 2, ',', '.') . '</td>';
                echo '<td>' . htmlspecialchars($mov['descricao']) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="4" class="text-center">Sem movimentos.</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
