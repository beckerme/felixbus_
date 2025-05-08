<?php
// Iniciar a sessão
session_start();

// Destruir todas as variáveis de sessão
session_unset();

// Destruir a sessão
session_destroy();

// Redirecionar para a página de login ou inicial
header("Location: index.php");
exit();
?>