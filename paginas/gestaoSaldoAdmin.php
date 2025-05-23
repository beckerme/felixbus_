<?php
session_start();
require_once('../basedados/basedados.h');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 3) {
    header('Location: login.php');
    exit();
}

// Buscar carteira da FelixBus (utilizador_id IS NULL)
$stmt = $ligacao->prepare('SELECT id, saldo FROM carteira WHERE utilizador_id IS NULL LIMIT 1');
$stmt->execute();
$carteira = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$carteira) {
    // Criar carteira da FelixBus se não existir
    $stmt = $ligacao->prepare('INSERT INTO carteira (utilizador_id, saldo) VALUES (NULL, 0.00)');
    $stmt->execute();
    $carteira_id = $ligacao->lastInsertId();
    $carteira = ['id' => $carteira_id, 'saldo' => 0.00];
}

// Mensagem de feedback
$msg = '';
if (isset($_SESSION['msg_carteira_admin'])) {
    $msg = $_SESSION['msg_carteira_admin'];
    unset($_SESSION['msg_carteira_admin']);
}

// Movimentos da carteira FelixBus
$stmt = $ligacao->prepare('SELECT data, tipo, valor, carteira_origem, carteira_destino, descricao FROM movimentocarteira WHERE carteira_destino = :cid OR carteira_origem = :cid ORDER BY data DESC');
$stmt->bindParam(':cid', $carteira['id'], PDO::PARAM_INT);
$stmt->execute();
$movs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Carteira FelixBus (Admin)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../estilos/style.css">
    <style>
        body { background: #7394e963; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container px-5">
        <a class="navbar-brand" href="areaPessoal.php">Felix Buss</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="areaPessoal.php">Área Pessoal</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container py-4">
    <h2 class="mb-4 text-center">Carteira FelixBus (Administração)</h2>
    <?php if ($msg): ?>
        <div class="alert alert-info text-center"><?php echo $msg; ?></div>
    <?php endif; ?>
    <div class="card mb-4">
        <div class="card-body">
            <h4>Saldo atual da FelixBus: <span class="text-success">€ <?php echo number_format($carteira['saldo'], 2, ',', '.'); ?></span></h4>
        </div>
    </div>
    <hr>
    <h5>Movimentos da Carteira FelixBus</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Data</th>
                <th>Tipo</th>
                <th>Valor (€)</th>
                <th>Carteira Origem</th>
                <th>Carteira Destino</th>
                <th>Descrição</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($movs) {
            foreach ($movs as $mov) {
                echo '<tr>';
                echo '<td>' . date('d/m/Y H:i', strtotime($mov['data'])) . '</td>';
                echo '<td>' . ucfirst($mov['tipo']) . '</td>';
                echo '<td>' . number_format($mov['valor'], 2, ',', '.') . '</td>';
                echo '<td>' . ($mov['carteira_origem'] ? $mov['carteira_origem'] : '-') . '</td>';
                echo '<td>' . ($mov['carteira_destino'] ? $mov['carteira_destino'] : '-') . '</td>';
                echo '<td>' . htmlspecialchars($mov['descricao']) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6" class="text-center">Sem movimentos.</td></tr>';
        }
        ?>
        </tbody>
    </table>
    <a href="areaPessoal.php" class="btn btn-secondary">Voltar</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
