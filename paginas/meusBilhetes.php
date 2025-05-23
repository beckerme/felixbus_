<?php
session_start();
require_once('../basedados/basedados.h');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 1) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Buscar bilhetes do cliente
$sql = "SELECT b.id, b.codigo_validacao, v.data, v.hora, r.origem, r.destino, r.preco
        FROM bilhete b
        JOIN Viagem v ON b.viagem_id = v.id
        JOIN Rota r ON v.rota_id = r.id
        WHERE b.cliente_id = :uid
        ORDER BY v.data DESC, v.hora DESC";
$stmt = $ligacao->prepare($sql);
$stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
$stmt->execute();
$bilhetes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar alertas ativos
$alertas = [];
$stmt = $ligacao->prepare('SELECT * FROM alerta WHERE ativo = 1 AND (data_inicio IS NULL OR data_inicio <= CURDATE()) AND (data_fim IS NULL OR data_fim >= CURDATE()) ORDER BY id DESC');
$stmt->execute();
$alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Meus Bilhetes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../estilos/style.css">
    <style>
        body { background: #7394e963; }
        .codigo-bilhete { font-family: monospace; font-size: 1.1em; }
        .table th, .table td { vertical-align: middle; }
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
    <h2 class="mb-4 text-center">Meus Bilhetes e Histórico de Viagens</h2>
    <div class="mb-3 text-end">
        <a href="areaPessoal.php" class="btn btn-secondary">Voltar</a>
    </div>
    <?php foreach ($alertas as $alerta): ?>
        <div class="alerta-promocao p-3 rounded">
            <b><?php echo htmlspecialchars($alerta['titulo']); ?></b><br>
            <?php echo nl2br(htmlspecialchars($alerta['mensagem'])); ?>
        </div>
    <?php endforeach; ?>
    <?php if (count($bilhetes) === 0): ?>
        <div class="alert alert-warning text-center">Você ainda não comprou nenhum bilhete.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>Origem</th>
                        <th>Destino</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Preço (€)</th>
                        <th>Código do Bilhete</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($bilhetes as $b): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($b['origem']); ?></td>
                        <td><?php echo htmlspecialchars($b['destino']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($b['data'])); ?></td>
                        <td><?php echo date('H:i', strtotime($b['hora'])); ?></td>
                        <td><?php echo number_format($b['preco'], 2, ',', '.'); ?></td>
                        <td class="codigo-bilhete"><?php echo htmlspecialchars($b['codigo_validacao']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
