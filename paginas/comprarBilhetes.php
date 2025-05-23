<?php
session_start();
require_once('../basedados/basedados.h');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 1) {
    header('Location: login.php');
    exit();
}

// Buscar saldo do cliente
$stmt = $ligacao->prepare('SELECT saldo FROM carteira WHERE utilizador_id = :uid');
$stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$carteira = $stmt->fetch(PDO::FETCH_ASSOC);
$saldo = $carteira ? $carteira['saldo'] : 0;

// Buscar alertas ativos
$alertas = [];
$stmt = $ligacao->prepare('SELECT * FROM alerta WHERE ativo = 1 AND (data_inicio IS NULL OR data_inicio <= CURDATE()) AND (data_fim IS NULL OR data_fim >= CURDATE()) ORDER BY id DESC');
$stmt->execute();
$alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar todas as origens e destinos únicos para autocomplete
$lista_origens = $ligacao->query('SELECT DISTINCT origem FROM Rota ORDER BY origem')->fetchAll(PDO::FETCH_COLUMN);
$lista_destinos = $ligacao->query('SELECT DISTINCT destino FROM Rota ORDER BY destino')->fetchAll(PDO::FETCH_COLUMN);

// Filtros de pesquisa
$origem = $_GET['origem'] ?? '';
$destino = $_GET['destino'] ?? '';
$data = $_GET['data'] ?? '';
$hora = $_GET['hora'] ?? '';

$where = [];
$params = [];
if ($origem) {
    $where[] = 'r.origem LIKE :origem';
    $params[':origem'] = "%$origem%";
}
if ($destino) {
    $where[] = 'r.destino LIKE :destino';
    $params[':destino'] = "%$destino%";
}
if ($data) {
    $where[] = 'v.data = :data';
    $params[':data'] = $data;
}
if ($hora) {
    $where[] = 'v.hora >= :hora';
    $params[':hora'] = $hora;
}
$where[] = '(v.data > CURDATE() OR (v.data = CURDATE() AND v.hora > CURTIME()))';

$sql = "SELECT r.id, r.origem, r.destino, r.preco, v.id as viagem_id, v.data, v.hora, r.capacidade,
    (SELECT COUNT(*) FROM bilhete b WHERE b.viagem_id = v.id) as vendidos
FROM Rota r
JOIN Viagem v ON v.rota_id = r.id
" . (count($where) ? 'WHERE ' . implode(' AND ', $where) : '') . "
ORDER BY v.data, v.hora, r.origem, r.destino";
$stmt = $ligacao->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->execute();
$viagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

$msg = '';
if (isset($_SESSION['msg_bilhete'])) {
    $msg = $_SESSION['msg_bilhete'];
    unset($_SESSION['msg_bilhete']);
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Comprar Bilhetes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../estilos/style.css">
    <style>
        body { background: #7394e963; }
        .bus-card { box-shadow: 0 2px 8px #0001; border-radius: 12px; margin-bottom: 2rem; }
        .bus-card .card-body { display: flex; flex-direction: column; align-items: flex-start; }
        .bus-info { font-size: 1.2rem; }
        .price { font-size: 1.3rem; font-weight: bold; color: #198754; }
        .btn-buy { font-size: 1.1rem; }
        .alerta-promocao { background: #ffe066; border-left: 5px solid #ffc107; color: #856404; margin-bottom: 1rem; }
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
                <li class="nav-item"><a class="nav-link" href="comprarBilhetes.php">Comprar Bilhetes</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container py-4">
    <h2 class="mb-4 text-center">Comprar Bilhetes</h2>
    <div class="mb-3 text-end">
        <a href="areaPessoal.php" class="btn btn-secondary">Voltar</a>
    </div>
    <div class="mb-4">
        <span class="badge bg-success">Saldo atual: € <?php echo number_format($saldo, 2, ',', '.'); ?></span>
    </div>
    <?php foreach ($alertas as $alerta): ?>
        <div class="alerta-promocao p-3 rounded">
            <b><?php echo htmlspecialchars($alerta['titulo']); ?></b><br>
            <?php echo nl2br(htmlspecialchars($alerta['mensagem'])); ?>
        </div>
    <?php endforeach; ?>
    <?php if ($msg): ?>
        <div class="alert alert-info text-center"><?php echo $msg; ?></div>
    <?php endif; ?>
    <form class="row g-3 mb-4" method="get" action="comprarBilhetes.php">
        <div class="col-md-3">
            <input list="lista_origens" type="text" class="form-control" name="origem" placeholder="Origem" value="<?php echo htmlspecialchars($origem); ?>">
            <datalist id="lista_origens">
                <?php foreach ($lista_origens as $o) { echo '<option value="' . htmlspecialchars($o) . '">'; } ?>
            </datalist>
        </div>
        <div class="col-md-3">
            <input list="lista_destinos" type="text" class="form-control" name="destino" placeholder="Destino" value="<?php echo htmlspecialchars($destino); ?>">
            <datalist id="lista_destinos">
                <?php foreach ($lista_destinos as $d) { echo '<option value="' . htmlspecialchars($d) . '">'; } ?>
            </datalist>
        </div>
        <div class="col-md-2">
            <input type="date" class="form-control" name="data" value="<?php echo htmlspecialchars($data); ?>">
        </div>
        <div class="col-md-2">
            <input type="time" class="form-control" name="hora" value="<?php echo htmlspecialchars($hora); ?>">
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary">Pesquisar</button>
        </div>
    </form>
    <?php if (count($viagens) === 0): ?>
        <div class="alert alert-warning text-center">Nenhuma viagem encontrada para os filtros selecionados.</div>
    <?php else: ?>
        <div class="row">
        <?php foreach ($viagens as $v): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card bus-card">
                    <div class="card-body">
                        <div class="bus-info mb-2">
                            <span class="fw-bold"><?php echo htmlspecialchars($v['origem']); ?></span>
                            <span class="mx-2">→</span>
                            <span class="fw-bold"><?php echo htmlspecialchars($v['destino']); ?></span>
                        </div>
                        <div class="mb-1">Data: <b><?php echo date('d/m/Y', strtotime($v['data'])); ?></b></div>
                        <div class="mb-1">Hora: <b><?php echo date('H:i', strtotime($v['hora'])); ?></b></div>
                        <div class="mb-1">Lugares disponíveis: <b><?php echo $v['capacidade'] - $v['vendidos']; ?></b></div>
                        <div class="price mb-2">€ <?php echo number_format($v['preco'], 2, ',', '.'); ?></div>
                        <form action="comprarBilheteBackend.php" method="post">
                            <input type="hidden" name="viagem_id" value="<?php echo $v['viagem_id']; ?>">
                            <input type="hidden" name="preco" value="<?php echo $v['preco']; ?>">
                            <button type="submit" class="btn btn-success btn-buy" <?php echo ($v['capacidade'] - $v['vendidos'] <= 0 || $saldo < $v['preco']) ? 'disabled' : ''; ?>>Comprar</button>
                        </form>
                        <?php if ($saldo < $v['preco']): ?>
                            <div class="text-danger mt-2">Saldo insuficiente</div>
                        <?php elseif ($v['capacidade'] - $v['vendidos'] <= 0): ?>
                            <div class="text-danger mt-2">Esgotado</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
