<?php
include_once("../basedados/basedados.h"); // conexão com o PDO
session_start();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Felix Buss</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="styles.css" rel="stylesheet">
    <style>
        .utilizadores-tabela{
            margin: 20px 0px;
        }
        .btn-validar{
            width: 50px;
            height: 50px;
        }
        .btn-apagar{
            width: 50px;
            height: 50px;
        }
        .table th{
            text-align: center;
        }
        .table td{
            text-align: center;
        }
        body{
            background-color: #7394e963;
        }
        .btnAdicionar{
            text-align: right;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .editar{
            width: 50px;
            height: 50px;
        }
    </style>

</head>
<body>
    <!-- Responsive navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container px-5">
                <a class="navbar-brand" href="areaPessoal.php">Felix Buss</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" aria-current="page" href="areaPessoal.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        
    <div class="container">
        <?php 
        if (isset($_SESSION['mensagem_sucesso'])):
        ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensagem_sucesso']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php 
            unset($_SESSION['mensagem_sucesso']); // Limpa a mensagem da sessão
        endif;
        
        if (isset($_SESSION['mensagem_erro'])):
        ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensagem_erro']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php 
            unset($_SESSION['mensagem_erro']); // Limpa a mensagem da sessão
        endif;

        // Lógica da Pesquisa
        $termo_pesquisa = isset($_GET['search']) ? trim($_GET['search']) : '';
        ?>
        <div class="row">
            <div class="col-md-3" style="margin-left: auto; margin-right: 0; margin-top: 10px;">
                <form action="gestaoUtilizador.php" method="GET">
                    <div class="input-group">
                        <input type="search" name="search" class="form-control rounded" placeholder="Pesquisar por nome ou email" aria-label="Search" aria-describedby="search-addon" value="<?php echo htmlspecialchars($termo_pesquisa); ?>" />
                        <button type="submit" class="btn btn-outline-primary" data-mdb-ripple-init>Pesquisar</button>
                    </div>
                </form>    
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped utilizadores-tabela">
                    <thead>
                        <tr>
                            <th scope="col">Email</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Telefone</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Validar</th>
                            <th scope="col">Editar</th>
                            <th scope="col">Apagar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sql = "SELECT id, email, password, nome, telefone, perfil_id FROM Utilizador";
                            $params = [];
                            if (!empty($termo_pesquisa)) {
                                $sql .= " WHERE nome LIKE :termo_pesquisa OR email LIKE :termo_pesquisa";
                                $params[':termo_pesquisa'] = '%' . $termo_pesquisa . '%';
                            }
                            $stmt = $ligacao->prepare($sql);
                            $stmt->execute($params);
                            
                            if ($stmt->rowCount() > 0) {
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                                    echo "<tr>";
                                    echo "<td>".htmlspecialchars($row['email'])."</td>";
                                    echo "<td>".htmlspecialchars($row['nome'])."</td>";
                                    echo "<td>".htmlspecialchars($row['telefone'])."</td>";
                                    // Determinar o tipo de utilizador com base no perfil_id
                                    $tipoUtilizador = 'Desconhecido';
                                    if ($row['perfil_id'] == 1) {
                                        $tipoUtilizador = 'Cliente';
                                    } elseif ($row['perfil_id'] == 2) {
                                        $tipoUtilizador = 'Funcionário'; // Corrigido de 'Cliente' para 'Funcionário'
                                    } elseif ($row['perfil_id'] == 3) {
                                        $tipoUtilizador = 'Admin';
                                    }
                                    echo "<td>".htmlspecialchars($tipoUtilizador)."</td>";
                                    echo "<td><a href='validarUtilizador.php?id=".htmlspecialchars($row['id'])."'><img src='validar.png' alt='Validar' style='width:32px; height:32px; max-width:100%;'></a></td>";
                                    echo "<td><a href='editarUtilizador.php?id=".htmlspecialchars($row['id'])."'><img src='pencil.png' alt='Editar' style='width:24px; height:24px; max-width:100%;'></a></td>";
                                    echo "<td><a href='apagarUtilizador.php?id=".htmlspecialchars($row['id'])."'><img src='apagar.png' alt='Apagar' style='width:24px; height:24px; max-width:100%;'></a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo '<tr><td colspan="7" class="text-center">Nenhum utilizador encontrado.</td></tr>';
                            }
                        ?>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 btnAdicionar">
                <a class="btn btn-primary" href="pagAddUtilizador.php">Adicionar Utilizador</a>
            </div>                          
        </div>
    </div>
    <!-- Footer-->
    <footer class="py-5 bg-dark">
        <div class="container px-4 px-lg-5"><p class="m-0 text-center text-white">Copyright &copy; Your Website 2023</p></div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>