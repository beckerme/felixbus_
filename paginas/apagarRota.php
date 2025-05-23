<!-- filepath: c:\xampp\htdocs\EWERTONSILVA_JOAOCRUZ\paginas\apagarRota.php -->
<?php
session_start();
require_once('../basedados/basedados.h');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_perfil'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['user_perfil'] != 3) { // Somente Admin (perfil_id = 3)
    header("Location: semPermissao.php");
    exit();
}

$rota_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($rota_id > 0) {
    try {
        $ligacao->beginTransaction();

        // Apagar horários (viagens) associados à rota
        $sql_delete_viagens = "DELETE FROM Viagem WHERE rota_id = :rota_id";
        $stmt_delete_viagens = $ligacao->prepare($sql_delete_viagens);
        $stmt_delete_viagens->bindParam(':rota_id', $rota_id, PDO::PARAM_INT);
        $stmt_delete_viagens->execute();

        // Apagar a rota
        $sql_delete_rota = "DELETE FROM Rota WHERE id = :rota_id"; // Corrigido de id_rota para id
        $stmt_delete_rota = $ligacao->prepare($sql_delete_rota);
        $stmt_delete_rota->bindParam(':rota_id', $rota_id, PDO::PARAM_INT);
        
        if ($stmt_delete_rota->execute()) {
            $ligacao->commit();
            $_SESSION['message'] = "Rota e seus horários associados foram apagados com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $ligacao->rollBack();
            $_SESSION['message'] = "Erro ao apagar a rota.";
            $_SESSION['message_type'] = "danger";
        }
    } catch (PDOException $e) {
        $ligacao->rollBack();
        $_SESSION['message'] = "Erro na base de dados ao apagar a rota: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "ID da rota inválido.";
    $_SESSION['message_type'] = "warning";
}

header("Location: gestaoRotas.php");
exit();
?>
