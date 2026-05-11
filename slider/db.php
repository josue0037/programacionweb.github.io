<?php

// PostgreSQL
$db = new PDO("pgsql:host=localhost;dbname=jserrano_db", "jserrano", "P@ssw0rd");

// MariaDB (comenta el de arriba si usas este)
$db = new PDO("mysql:host=localhost;dbname=jserrano_db", "jserrano", "P@ssw0rd");

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>