<?php
require 'db.php';

//  CREAR
if ($_GET['action'] === 'create') {

    $nombre = $_POST['nombre'];
    $imagen = $_FILES['imagen'];

    if (!$imagen || $imagen['error'] !== 0) {
        echo "error_archivo";
        exit;
    }

    $permitidos = ['image/jpeg', 'image/png', 'image/webp'];

    if (!in_array($imagen['type'], $permitidos)) {
        echo "tipo_no_valido";
        exit;
    }

    if ($imagen['size'] > 2 * 1024 * 1024) {
        echo "archivo_grande";
        exit;
    }

    $hash = md5_file($imagen['tmp_name']);
    $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);

    $ruta = "img/" . $hash . "." . $extension;

    $sqlCheck = "SELECT COUNT(*) FROM slider WHERE ruta = :ruta";
    $queryCheck = $db->prepare($sqlCheck);
    $queryCheck->execute(['ruta' => $ruta]);

    if ($queryCheck->fetchColumn() > 0) {
        echo "imagen_duplicada";
        exit;
    }

    if (!file_exists("img/")) {
        mkdir("img/", 0777, true);
    }

    move_uploaded_file($imagen['tmp_name'], $ruta);

    $sql = "INSERT INTO slider (nombre, ruta) VALUES (:nombre, :ruta)";
    $query = $db->prepare($sql);
    $query->execute([
        'nombre' => $nombre,
        'ruta' => $ruta
    ]);

    echo "ok";
    exit;
}



if ($_GET['action'] === 'read') {

    $query = $db->query("SELECT * FROM slider WHERE activo = true");
    $data = $query->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}


if ($_GET['action'] === 'delete') {

    $id = $_POST['id'];

    //  obtener ruta
    $sql = "SELECT ruta FROM slider WHERE id = :id";
    $query = $db->prepare($sql);
    $query->execute(['id' => $id]);
    $img = $query->fetch();

    //  borrar archivo físico
    if ($img && file_exists($img['ruta'])) {
        unlink($img['ruta']);
    }

    //  borrar de BD (o desactivar)
    $sql = "UPDATE slider SET activo = false WHERE id = :id";
    $query = $db->prepare($sql);
    $query->execute(['id' => $id]);

    echo "ok";
    exit;
}

if ($_GET['action'] === 'update') {

    $id = $_POST['id'];
    $nombre = $_POST['nombre'];

    $sql = "UPDATE slider SET nombre = :nombre WHERE id = :id";
    $query = $db->prepare($sql);

    $query->execute([
        'nombre' => $nombre,
        'id' => $id
    ]);

    echo "ok";
    exit;
}


?>