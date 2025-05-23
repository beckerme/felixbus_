<!-- filepath: c:\xampp\htdocs\EWERTONSILVA_JOAOCRUZ\paginas\pagEditarRota.php -->
<?php
session_start();
require_once('../basedados/basedados.h');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['perfil_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['perfil_id'] != 1) { // Somente Admin
    header("Location: semPermissao.php");
    exit();
}

$rota_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$rota = null;

if ($rota_id > 0) {
    $sql = "SELECT origem, destino, preco, capacidade FROM Rota WHERE id_rota = :rota_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':rota_id', $rota_id, PDO::PARAM_INT);
    $stmt->execute();
    $rota = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$rota) {
    echo "<p>Rota não encontrada!</p>";
    echo "<a href='gestaoRotas.php'>Voltar para Gestão de Rotas</a>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $origem = $_POST['origem'];
    $destino = $_POST['destino'];
    $preco = $_POST['preco'];
    $capacidade = $_POST['capacidade'];

    $update_sql = "UPDATE Rota SET origem = :origem, destino = :destino, preco = :preco, capacidade = :capacidade WHERE id_rota = :rota_id";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bindParam(':origem', $origem);
    $update_stmt->bindParam(':destino', $destino);
    $update_stmt->bindParam(':preco', $preco);
    $update_stmt->bindParam(':capacidade', $capacidade, PDO::PARAM_INT);
    $update_stmt->bindParam(':rota_id', $rota_id, PDO::PARAM_INT);

    if ($update_stmt->execute()) {
        $_SESSION['message'] = "Rota atualizada com sucesso!";
        $_SESSION['message_type'] = "success";
        header("Location: gestaoRotas.php");
        exit();
    } else {
        $_SESSION['message'] = "Erro ao atualizar a rota.";
        $_SESSION['message_type'] = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Rota - Felix Buss</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container px-5">
            <a class="navbar-brand" href="areaPessoal.php">Felix Buss</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="areaPessoal.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="gestaoRotas.php">Gestão de Rotas</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="vh-100 bg-image" style="background-color:#7394e963;">
        <div class="mask d-flex align-items-center h-100 gradient-custom-3">
            <div class="container h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                        <div class="card" style="border-radius: 15px;">
                            <div class="card-body p-5">
                                <h2 class="text-uppercase text-center mb-5">Editar Rota</h2>

                                <?php if (isset($_SESSION['message'])): ?>
                                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                                        <?php echo $_SESSION['message']; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                                <?php endif; ?>

                                <form action="pagEditarRota.php?id=<?php echo $rota_id; ?>" method="post">
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="form-outline">
                                                <input type="text" id="origem" class="form-control form-control-lg" name="origem" value="<?php echo htmlspecialchars($rota['origem']); ?>" required maxlength="100"/>
                                                <label class="form-label" for="origem">Origem</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="form-outline">
                                                <input type="text" id="destino" class="form-control form-control-lg" name="destino" value="<?php echo htmlspecialchars($rota['destino']); ?>" required maxlength="100" />
                                                <label class="form-label" for="destino">Destino</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="form-outline">
                                                <input type="number" id="preco" class="form-control form-control-lg" name="preco" value="<?php echo htmlspecialchars($rota['preco']); ?>" required min="0" step="0.01" />
                                                <label class="form-label" for="preco">Preço (€)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="form-outline">
                                                <input type="number" id="capacidade" class="form-control form-control-lg" name="capacidade" value="<?php echo htmlspecialchars($rota['capacidade']); ?>" required min="1" />
                                                <label class="form-label" for="capacidade">Capacidade do Autocarro</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-center">
                                        <button type="submit" class="btn btn-primary btn-block btn-lg gradient-custom-4 text-body" style="background-color:#7394e9; border-color:#7394e9;">Atualizar Rota</button>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="gestaoRotas.php" class="btn btn-secondary btn-lg">Voltar</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 bg-dark fixed-bottom">
        <div class="container px-4 px-lg-5"><p class="m-0 text-center text-white">Copyright &copy; Felix Buss 2023</p></div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
