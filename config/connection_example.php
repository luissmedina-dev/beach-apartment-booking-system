<?php

$host = "localhost";
$dbname = "database_name";
$user = "username";
$pass = "password";

// Conexao PDO
$conn = new PDO("mysql:dbname=". $db_name. ";host=". $db_host, $db_user, $db_pass);


// Mensagens de erros no codigo
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
?>