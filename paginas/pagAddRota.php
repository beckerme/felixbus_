<?php
session_start();
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Felix Buss</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="css/styles.css">

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container px-5">
            <a class="navbar-brand" href="paginaAdmin.php">Felix Buss</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" aria-current="page" href="paginaAdmin.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="pagGestao.php">Gerir</a></li>
                     <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>

                        
                </ul>
            </div>
        </div>
    </nav>
    <section class="vh-100 bg-image"
        style="background-color:#7394e963; ">
        <div class="mask d-flex align-items-center h-100 gradient-custom-3">
            <div class="container h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                        <div class="card" style="border-radius: 15px;">
                            <div class="card-body p-5">
                                <h2 class="text-uppercase text-center mb-5">Adicionar Nova Rota</h2>

                                <?php
                                if (isset($_SESSION['mensagem_erro'])) {
                                    echo '<div class="alert alert-danger text-center">' . $_SESSION['mensagem_erro'] . '</div>';
                                    unset($_SESSION['mensagem_erro']);
                                }
                                ?>

                                <form action="adicionarRota.php" method="post">
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="form-outline">
                                                <input type="text" id="origem" class="form-control form-control-lg" name="origem" required maxlength="100"/>
                                                <label class="form-label" for="origem">Origem</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="form-outline">
                                                <input type="text" id="destino" class="form-control form-control-lg" name="destino" required maxlength="100" />
                                                <label class="form-label" for="destino">Destino</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="form-outline">
                                                <input type="number" id="preco" class="form-control form-control-lg" name="preco" required min="0" step="0.01" />
                                                <label class="form-label" for="preco">Preço (€)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="form-outline">
                                                <input type="number" id="capacidade" class="form-control form-control-lg" name="capacidade" required min="1" />
                                                <label class="form-label" for="capacidade">Capacidade do Autocarro</label>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                    <h4 class="text-uppercase text-center mb-3">Primeiro Horário da Rota</h4>

                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="form-outline">
                                                <input type="date" id="data_viagem" class="form-control form-control-lg" name="data_viagem" required />
                                                <label class="form-label" for="data_viagem">Data da Primeira Viagem</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="form-outline">
                                                <input type="time" id="hora_viagem" class="form-control form-control-lg" name="hora_viagem" required />
                                                <label class="form-label" for="hora_viagem">Hora da Primeira Viagem</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-center">
                                        <button type="submit"
                                            class="btn btn-success btn-block btn-lg gradient-custom-4 text-body" style="background-color:#7394e963; border-color:#7394e963;">Adicionar Rota</button>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="gestaoRotas.php" class="btn btn-secondary btn-lg">Voltar</a>
                                    </div>
                                    <br>
                                    

                                </form>
                                

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="py-5 bg-dark">
        <div class="container px-4 px-lg-5"><p class="m-0 text-center text-white">Copyright &copy; Your Website 2023</p></div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>





</body>

</html>