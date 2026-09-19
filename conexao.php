<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "sistema_login";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
