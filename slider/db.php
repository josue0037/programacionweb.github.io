<?php

// PostgreSQL
$db = new PDO("pgsql:host=localhost;dbname=tu_db", "user", "password");

// MariaDB (comenta el de arriba si usas este)
//$db = new PDO("mysql:host=localhost;dbname=jserrano_db", "jserrano", "P@ssw0rd");

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>