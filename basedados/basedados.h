<?php
$host = 'localhost';
$db = 'felixbus';
$user = 'root';
$pass = ''; 

try {
    $ligacao = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro na ligação à base de dados: " . $e->getMessage();
    exit;
}
?>
