<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'db.php';

if ($_GET['action'] === 'read') {

    $query = $db->query("SELECT * FROM slider WHERE activo = true");
    $data = $query->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if ($_GET['action'] === 'mostrar_inicial') {

    $query = $db->query("
        SELECT id, ruta 
        FROM slider 
        WHERE activo = true 
        ORDER BY id ASC 
        LIMIT 1
    ");

    $data = $query->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');

    if ($data) {

        echo json_encode([
            "status" => "ok",
            "id" => $data['id'],
            "ruta" => $data['ruta']
        ]);

    } else {

        echo json_encode([
            "status" => "vacio"
        ]);
    }

    exit;
}

if ($_GET['action'] === 'mostrar') {

    $id = intval($_GET['id']);
    $dir = $_GET['dir'];

    if ($dir === 'next') {
        $query = $db->prepare("SELECT id, ruta FROM slider WHERE id > :id AND activo = true ORDER BY id ASC LIMIT 1");
    } else {
        $query = $db->prepare("SELECT id, ruta FROM slider WHERE id < :id AND activo = true ORDER BY id DESC LIMIT 1");
    }

    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();

    $data = $query->fetch(PDO::FETCH_ASSOC);

    // SI NO HAY RESULTADO → HACER LOOP
    if (!$data) {

        if ($dir === 'next') {
            // ir al primero
            $query = $db->query("SELECT id, ruta FROM slider WHERE activo = true ORDER BY id ASC LIMIT 1");
        } else {
            // ir al último
            $query = $db->query("SELECT id, ruta FROM slider WHERE activo = true ORDER BY id DESC LIMIT 1");
        }

        $data = $query->fetch(PDO::FETCH_ASSOC);
    }

    header('Content-Type: application/json');

    if ($data) {

        echo json_encode([
            "status" => "ok",
            "id" => $data['id'],
            "ruta" => $data['ruta']
        ]);

    } else {

        echo json_encode([
            "status" => "vacio"
        ]);
    }

    exit;
}



session_start();

// NO redirecciones en AJAX
if(!isset($_SESSION["username"])){
    echo "no_sesion";
    exit;
}

// SOLO proteger acciones sensibles
$action = $_GET['action'] ?? '';

if (in_array($action, ['create', 'update', 'delete'])) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        echo "no_autorizado";
        exit;
    }
}

//  CREAR
$action = $_GET['action'] ?? '';

if ($action === 'create') {

    $nombre = trim($_POST['nombre'] ?? '');
    $imagen = $_FILES['imagen'] ?? null;

    if (!$imagen || $imagen['error'] !== 0) {
        echo "error_archivo";
        exit;
    }

    if ($nombre === '') {
        echo "nombre_vacio";
        exit;
    }

    // VALIDAR MIME REAL
    $permitidos = ['image/jpeg', 'image/png', 'image/webp'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $imagen['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $permitidos)) {
        echo "tipo_no_valido";
        exit;
    }

    if ($imagen['size'] > 2 * 1024 * 1024) {
        echo "archivo_grande";
        exit;
    }

    // EXTENSION SEGURA
    $extensiones = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    $extension = $extensiones[$mime];

    $hash = md5_file($imagen['tmp_name']);
    
    $nombreArchivo = $hash . "." . $extension;

    $rutaServidor = __DIR__ . "/img/" . $hash . "." . $extension;// ruta física
    $rutaBD = "img/" . $nombreArchivo; // ruta para HTML

    if (file_exists($rutaServidor)) {
        echo "imagen_duplicada";
        exit;
    }

    // BUSCAR SI YA EXISTE
    $sqlCheck = "SELECT id, activo FROM slider WHERE ruta = :ruta LIMIT 1";
    $queryCheck = $db->prepare($sqlCheck);
    $queryCheck->execute(['ruta' => $rutaBD]);

    $existe = $queryCheck->fetch(PDO::FETCH_ASSOC);

    if ($existe && $existe['activo']) {
        echo "imagen_duplicada";
        exit;
    }

    // EXISTE PERO INACTIVA → REACTIVAR
    if ($existe && !$existe['activo']) {

        $sql = "UPDATE slider SET activo = true, nombre = :nombre WHERE id = :id";
        $query = $db->prepare($sql);
        $query->execute([
            'nombre' => $nombre,
            'id' => $existe['id']
        ]);

        echo "reactivada";
        exit;
    }

    // CREAR CARPETA
    if (!file_exists(__DIR__ . "/img")) {
        mkdir(__DIR__ . "/img", 0755, true);
    }

    // GUARDAR ARCHIVO
    if (!file_exists($rutaServidor)) {
        if (!move_uploaded_file($imagen['tmp_name'], $rutaServidor)) {
            echo "error_guardado";
            exit;
        }
    }

    // INSERTAR BD
    $sql = "INSERT INTO slider (nombre, ruta, activo) VALUES (:nombre, :ruta, true)";
    $query = $db->prepare($sql);
    $query->execute([
        'nombre' => $nombre,
        'ruta' => $rutaBD
    ]);

    echo "ok";
    exit;
}


if ($_GET['action'] === 'delete') {

    $id = $_POST['id'] ?? null;
    $modo = $_POST['modo'] ?? 'soft'; // soft | hard

    if (!$id) {
        echo "error_id";
        exit;
    }

    // Obtener datos
    $sql = "SELECT ruta FROM slider WHERE id = :id";
    $query = $db->prepare($sql);
    $query->execute(['id' => $id]);
    $img = $query->fetch(PDO::FETCH_ASSOC);

    if (!$img) {
        echo "no_existe";
        exit;
    }

    // HARD DELETE (borra TODO)
    if ($modo === 'hard') {

        // borrar archivo físico
        if (isset($img['ruta']) && file_exists($img['ruta'])) {
            unlink($img['ruta']);
        }

        // borrar de BD
        $sql = "DELETE FROM slider WHERE id = :id";
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);

        echo "eliminado_total";
        exit;
    }

    // SOFT DELETE (solo desactiva)
    if ($modo === 'soft') {

        $sql = "UPDATE slider SET activo = false WHERE id = :id";
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);

        echo "desactivado";
        exit;
    }
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