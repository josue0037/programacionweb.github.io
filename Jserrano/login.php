<?php

// index.php
require_once 'db.php'; // Traemos el código del otro archivo



//  Obtenemos los datos del formulario
     $email  = $_POST['email'];
     $pwd = $_POST['pwd'];
     
     // Llamamos a la función y guardamos el objeto en $db
     $db = conectarDB();
      

  try {
  

        $sql = "SELECT id, password, email FROM usuarios WHERE email = :email";
        $query = $db->prepare($sql);

        $query->execute([
            'email' => $email
        ]);

        $usuario = $query->fetch(PDO::FETCH_ASSOC);

        // Si el correo no existe
        if (!$usuario) {
            echo "correo_no_registrado";
            exit;
        }

        // Si la contraseña no coincide
        $verify = password_verify($pwd, $usuario['password']);

        if (!$verify) {
            echo "password_incorrecta";
            exit;
        }

        // Si todo está correcto
        session_start();
        $_SESSION['username'] = $usuario['email'];
        $_SESSION['id'] = $usuario['id'];

        echo "login_correcto";
        

        

        

    } catch (PDOException $e) {
        // Manejo de errores (ej. si el email ya existe y es único)
        echo "Database Error: " . $e->getMessage();

        
     
    }






?>