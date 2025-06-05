<?php

$host ="localhost";
$username = "root";
$password = "";
$database = "ipss_et";

// Crear conexión a la base de datos
$conn = new mysqli($host, $username, $password, $database);

// verificar conección
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Configurar la codificación de caracteres
header("Content-Type: text/html; charset=UTF-8");

$metodo = $_SERVER['REQUEST_METHOD'];
print_r($metodo);
?>