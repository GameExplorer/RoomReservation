<?php
include 'connection.php';

if (isset($_POST['day'])) {
    $day = $_POST['day'];

    $sql = "SELECT hora_inicio, hora_finalizacion FROM reserva_de_habitacion WHERE reserva_reservada = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $day);
    $stmt->execute();
    $result = $stmt->get_result();

    $bookedHours = [];

    while ($row = $result->fetch_assoc()) {
        $startHour = (int) explode(':', $row['hora_inicio'])[0];
        $endHour = (int) explode(':', $row['hora_finalizacion'])[0];
        $bookedHours[] = "$startHour-$endHour"; // Store booking range as "start-end"
    }

    $stmt->close();
    $conn->close();

    echo json_encode($bookedHours);
} else {
    echo json_encode([]);
}
