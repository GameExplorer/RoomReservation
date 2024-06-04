<?php
// Database connection
$host = 'localhost';
$dbuser = 'root';
$dbpassword = '';
$database = 'reserva_de_habitacion';

$conn = new mysqli($host, $dbuser, $dbpassword, $database);

if ($conn->connect_error) {
    die("Conexión fallada: " . $conn->connect_error);
}
