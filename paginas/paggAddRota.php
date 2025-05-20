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
    <section class="vh-100 bg-image"
        style="background-color:#7394e963; ">
        <div class="mask d-flex align-items-center h-100 gradient-custom-3">
            <div class="container h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                        <div class="card" style="border-radius: 15px;">
                            <div class="card-body p-5">
                                <h2 class="text-uppercase text-center mb-5">Adicionar Rota</h2>

                                <form action="adicionarDespesa.php" method="post">

                                    <div class="form-outline mb-4">
                                        <input type="number" id="form3Example1cg" class="form-control form-control-lg" name="dia" min="1" max="31" />
                                        <label class="form-label" for="form3Example1cg">Dia</label>
                                    </div>

                                    <div class="form-outline mb-4">
                                        <input type="number" id="form3Example1cg" class="form-control form-control-lg" name="mes" min="1" max="12" />
                                        <label class="form-label" for="form3Example1cg">Mês</label>
                                    </div>

                                    <div class="form-outline mb-4">
                                        <input type="number" id="form3Example1cg" class="form-control form-control-lg" name="ano" min="2000" max="2100" />
                                        <label class="form-label" for="form3Example1cg">Ano</label>
                                    </div>

                                    <div class="form-outline mb-4">
                                        <input type="text" id="form3Example3cg" class="form-control form-control-lg" name="motivo" maxlength="60" />
                                        <label class="form-label" for="form3Example3cg">Destino</label>
                                    </div>

                                    <div class="form-outline mb-4">
                                        <input type="number" id="form3Example4cg"
                                            class="form-control form-control-lg" name="valor" min="0" max="100000" onkeydown="return event.keyCode !== 190 && event.keyCode !== 69" />
                                        <label class="form-label" for="form3Example4cg">Valor</label>
                                    </div>


                                    <div class="d-flex justify-content-center">
                                        <button type="button submit"
                                            class="btn btn-success btn-block btn-lg gradient-custom-4 text-body" style="background-color:#7394e963; border-color:#7394e963;">Adicionar</button>
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
        <div class="container px-4 px-lg-5">
            <p class="m-0 text-center text-white">Copyright &copy; Your Website 2023</p>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>


</body>

</html>