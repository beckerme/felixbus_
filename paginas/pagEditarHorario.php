<!-- filepath: c:\xampp\htdocs\EWERTONSILVA_JOAOCRUZ\paginas\pagEditarHorario.php -->
<?php
session_start();
require_once('../basedados/basedados.h');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 3) { // Apenas Admin
    header("Location: semPermissao.php");
    exit();
}

$viagem_pk_id = isset($_GET['id']) ? (int)$_GET['id'] : 0; // Alterado de viagem_id para viagem_pk_id para clareza, representa o ID da Viagem
$rota_id = isset($_GET['rota_id']) ? (int)$_GET['rota_id'] : 0; 
$horario = null;

if ($viagem_pk_id > 0) {
    $sql = "SELECT rota_id, data, hora FROM Viagem WHERE id = :viagem_pk_id"; // Alterado de id_viagem para id
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':viagem_pk_id', $viagem_pk_id, PDO::PARAM_INT);
    $stmt->execute();
    $horario = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($horario) {
        $rota_id = $horario['rota_id']; 
    }
}

if (!$horario) {
    $_SESSION['message'] = "Horário não encontrado!";
    $_SESSION['message_type'] = "danger";
    header("Location: gestaoHorarios.php?rota_id=" . $rota_id); // Redireciona para a gestão de horários da rota
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data_viagem_input = $_POST['data_viagem'];
    $hora_viagem_input = $_POST['hora_viagem'];

    // Validação de data/hora passada
    $data_hora_viagem_input = new DateTime($data_viagem_input . ' ' . $hora_viagem_input);
    $agora = new DateTime();

    if ($data_hora_viagem_input < $agora) {
        $_SESSION['message'] = "Não é possível definir um horário para uma data ou hora que já passou.";
        $_SESSION['message_type'] = "warning";
    } else {
        // Verificar duplicados para a mesma rota, data e hora (excluindo o próprio horário)
        $check_sql = "SELECT id FROM Viagem WHERE rota_id = :rota_id AND data_viagem = :data_viagem AND hora_viagem = :hora_viagem AND id != :viagem_pk_id"; // Alterado de id_viagem para id
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bindParam(':rota_id', $rota_id, PDO::PARAM_INT);
        $check_stmt->bindParam(':data_viagem', $data_viagem_input);
        $check_stmt->bindParam(':hora_viagem', $hora_viagem_input);
        $check_stmt->bindParam(':viagem_pk_id', $viagem_pk_id, PDO::PARAM_INT);
        $check_stmt->execute();

        if ($check_stmt->fetch()) {
            $_SESSION['message'] = "Já existe um horário com esta data e hora para esta rota.";
            $_SESSION['message_type'] = "warning";
        } else {
            $update_sql = "UPDATE Viagem SET data_viagem = :data_viagem, hora_viagem = :hora_viagem WHERE id = :viagem_pk_id"; // Alterado de id_viagem para id
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bindParam(':data_viagem', $data_viagem_input);
            $update_stmt->bindParam(':hora_viagem', $hora_viagem_input);
            $update_stmt->bindParam(':viagem_pk_id', $viagem_pk_id, PDO::PARAM_INT);

            if ($update_stmt->execute()) {
                $_SESSION['message'] = "Horário atualizado com sucesso!";
                $_SESSION['message_type'] = "success";
                header("Location: gestaoHorarios.php?rota_id=" . $rota_id);
                exit();
            } else {
                $_SESSION['message'] = "Erro ao atualizar o horário.";
                $_SESSION['message_type'] = "danger";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Horário - Felix Buss</title>
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
                    <li class="nav-item"><a class="nav-link" href="gestaoRotas.php">Gestão de Rotas</a></li>
                    <li class="nav-item"><a class="nav-link active" href="gestaoHorarios.php?rota_id=<?php echo $rota_id; ?>">Gestão de Horários</a></li>
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
                                <h2 class="text-uppercase text-center mb-5">Editar Horário</h2>

                                <?php if (isset($_SESSION['message'])): ?>
                                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                                        <?php echo $_SESSION['message']; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                                <?php endif; ?>

                                <form action="pagEditarHorario.php?id=<?php echo $viagem_pk_id; ?>&rota_id=<?php echo $rota_id; ?>" method="post">
                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="data_viagem">Data da Viagem</label>
                                        <input type="date" id="data_viagem" class="form-control form-control-lg" name="data_viagem" value="<?php echo htmlspecialchars($horario['data_viagem']); ?>" required />
                                    </div>

                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="hora_viagem">Hora da Viagem</label>
                                        <input type="time" id="hora_viagem" class="form-control form-control-lg" name="hora_viagem" value="<?php echo htmlspecialchars($horario['hora_viagem']); ?>" required />
                                    </div>

                                    <div class="d-flex justify-content-center">
                                        <button type="submit" class="btn btn-primary btn-block btn-lg">Atualizar Horário</button>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="gestaoHorarios.php?rota_id=<?php echo $rota_id; ?>" class="btn btn-secondary btn-lg">Voltar</a>
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
        <div class="container px-4 px-lg-5"><p class="m-0 text-center text-white">Copyright &copy; Felix Buss <?php echo date("Y"); ?></p></div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
