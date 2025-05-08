<?php
session_start();

    echo("<h1>Registo concluído com sucesso! CLIENTE: ". $_SESSION['user_nome']."</h1>");
?>