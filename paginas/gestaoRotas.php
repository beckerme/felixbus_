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
        .despesas-tabela{
            margin: 20px 0px;
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
        .total{
            text-align: right;
        }
        .btnAdicionar{
            text-align: right;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .btn-apagar{
            width: 50px;
            height: 50px;
        }
        .editar{
            width: 50px;
            height: 50px;
        }
    </style>

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container px-5">
                <a class="navbar-brand" href="areaPessoal.php">Felix Buss</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" aria-current="page" href="areaPessoal.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link active" href="pagGestao.php">Gerir</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
   
    <div class="container">
        <div class="row">
            <div class="col-md-3" style="margin-left: auto; margin-right: 0; margin-top: 10px;">
                <form action="" method="GET">
                    <div class="input-group">
                        <input type="search" required name="search" class="form-control rounded" placeholder="Search" aria-label="Search" aria-describedby="search-addon" />
                        <button type="submit" class="btn btn-outline-primary" data-mdb-ripple-init>search</button>
                    </div>
                </form>    
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped despesas-tabela">
                    <thead>
                        <tr>
                            <th scope="col">Data</th>
                            <th scope="col">Destino</th>
                            <th scope="col">Valor em €</th>
                            <th scope="col">Editar</th>
                            <th scope="col">Apagar</th>
                        </tr>
                    </thead>
                    <tbody>

                    










                    </tbody>
                </table>
            </div>
        </div>
      
        <div class="row">
            <div class="col-md-12 btnAdicionar">
                <a class="btn btn-primary" href="pagAddRota.php">Adicionar Rota</a>
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