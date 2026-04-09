<?php

// Sesionecs
session_start();

$_SESSION["username"] = "juan";
$_SESSION["login_time"] = time();

// index.php
require_once 'db.php'; // Traemos el código del otro archivo

//require_once 'db-pgsql.php'; // Traemos el código del otro archivo


//  Obtenemos los datos del formulario
     $nombre = $_POST['nombre'];
     $email  = $_POST['email'];
     $pwd = $_POST['pwd'];
     // Llamamos a la función y guardamos el objeto en $db
     $db = conectarDB();
      

  try {
    // Verificar si ya existe el correo
    $sqlVerificar = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
    $queryVerificar = $db->prepare($sqlVerificar);
    $queryVerificar->execute([
        'email' => $email
    ]);

    $existe = $queryVerificar->fetchColumn();

    if ($existe > 0) {
        echo "correo_existente";
        exit;
    }

    // Si no existe, registrar
    $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)";
    $query = $db->prepare($sql);

    $passwordHash = password_hash($pwd, PASSWORD_DEFAULT);

    $resultado = $query->execute([
        'nombre' => $nombre,
        'email' => $email,
        'password' => $passwordHash
    ]);

    if ($resultado) {
        echo "registro_correcto";
    }

    } catch (PDOException $e) {
        // Manejo de errores (ej. si el email ya existe y es único)

        if ($e->errorInfo[1] == 1062) {
            
            echo "El email ya existe, favor de intendarlo con otro correo. <a href='index.html'>Continuar</a>";
        }else {
        // Handle other database errors
        echo "Database Error: " . $e->getMessage();
               
        
    }
     
    }






?>