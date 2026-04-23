<?php
function conectarDB() {
    try {
        $db = new PDO("pgsql:host=localhost;port=5432;dbname=jserrano_db", "jserrano", "P@ssw0rd");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        die("Error conexión: " . $e->getMessage());
    }
}
?>