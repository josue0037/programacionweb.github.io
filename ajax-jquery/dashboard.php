<?php
session_start();

// ¿Existe la sesión? Si no, fuera de aquí.
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./wwwroot/css/bootstrap-icons.min.css">
    <script src="./wwwroot/js/jquery-4.0.0.min.js"></script>
    <script src="./wwwroot/js/script.js"></script>
  </head>
  <body>
    <div class="col-lg-5 mx-auto p-4 py-md-5">
      <header class="d-flex align-items-center pb-3 mb-5 border-bottom">
	<h2 class="text-success">Dashboard</h2>
      </header>

      <main id="main">
	
        <h3>Bienvenidos a la biblioteca digital</h3>
        <a href='/ajax-jquery/logout.php'>Salir</a>
	
      </main>
    </div>
    


    <script src="./js/bootstrap.bundle.min.js"></script>
  </body>
</html>

