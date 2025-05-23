<!-- filepath: c:\xampp\htdocs\EWERTONSILVA_JOAOCRUZ\paginas\apagarHorario.php -->
<?php
session_start();
require_once('../basedados/basedados.h');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 3) { // Apenas Admin
    header("Location: semPermissao.php");
    exit();
}

$viagem_pk_id = isset($_GET['id']) ? (int)$_GET['id'] : 0; // Alterado de viagem_id para viagem_pk_id, representa o ID da Viagem
$rota_id = isset($_GET['rota_id']) ? (int)$_GET['rota_id'] : 0; 

if ($viagem_pk_id > 0) {
    // Opcional: Verificar se existem bilhetes associados a esta viagem antes de apagar.
    // $sql_check_bilhetes = "SELECT COUNT(*) FROM Bilhete WHERE viagem_id = :viagem_pk_id"; // Assumindo que a FK em Bilhete é viagem_id
    // $stmt_check_bilhetes = $conn->prepare($sql_check_bilhetes);
    // $stmt_check_bilhetes->bindParam(':viagem_pk_id', $viagem_pk_id, PDO::PARAM_INT);
    // $stmt_check_bilhetes->execute();
    // $num_bilhetes = $stmt_check_bilhetes->fetchColumn();
    // if ($num_bilhetes > 0) {
    //     $_SESSION['message'] = "Não é possível apagar este horário pois existem bilhetes associados.";
    //     $_SESSION['message_type'] = "danger";
    //     header("Location: gestaoHorarios.php?rota_id=" . $rota_id);
    //     exit();
    // }

    $sql = "DELETE FROM Viagem WHERE id = :viagem_pk_id"; // Alterado de id_viagem para id
    $stmt = $ligacao->prepare($sql);
    $stmt->bindParam(':viagem_pk_id', $viagem_pk_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Horário apagado com sucesso!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Erro ao apagar o horário.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "ID do horário inválido.";
    $_SESSION['message_type'] = "warning";
}

if ($rota_id > 0) {
    header("Location: gestaoHorarios.php?rota_id=" . $rota_id);
} else {
    header("Location: gestaoRotas.php"); 
}
exit();
?>
