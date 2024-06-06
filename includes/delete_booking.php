<?php

include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $idTicket = intval($_POST['id']);
    $sql = "DELETE FROM reserva_de_habitacion WHERE id_ticket = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idTicket);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
}

$conn->close();