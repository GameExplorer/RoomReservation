<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['event-id'];
    $name = $_POST['event-title'];
    $date = $_POST['event-date'];
    $start = $_POST['event-start'];
    $end = $_POST['endTime'];

    // Validate inputs
    if (empty($id) || empty($name) || empty($date) || empty($start) || empty($end)) {
        echo "Todos los campos son obligatorios.";
        exit();
    }

    // Update booking in the database
    $sql = "UPDATE reserva_de_habitacion SET nombre = ?, reserva_reservada = ?, hora_inicio = ?, hora_finalizacion = ? WHERE id_ticket = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $date, $start, $end, $id);

    if ($stmt->execute()) {
        echo "Reserva actualizada correctamente.";
    } else {
        echo "Error al actualizar la reserva: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Método de solicitud no válido.";
}
