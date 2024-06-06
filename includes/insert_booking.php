<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['event-title'];
    $eventDate = $_POST['event-date'];
    $startTime = $_POST['event-hour'];
    $endTime = $_POST['endTime'];

    // Get the current date
    $currentDate = (new DateTime())->format('Y-m-d');

    //Insert data into the database
    $query = "INSERT INTO reserva_de_habitacion (nombre, hora_inicio, hora_finalizacion, reserva_creada, reserva_reservada) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $name, $startTime, $endTime, $currentDate, $eventDate);

    if ($stmt->execute()) {
        echo 'La habitación está reservada';
    } else {
        echo 'Error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
