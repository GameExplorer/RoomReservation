<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'];
    $startTime = $data['startTime'];
    $endTime = $data['endTime'];

    $bookings = json_decode(file_get_contents('bookings.json'), true) ?? [];
    $bookings[] = [
        'username' => $username,
        'startTime' => $startTime,
        'endTime' => $endTime
    ];

    file_put_contents('bookings.json', json_encode($bookings));
    echo json_encode(['status' => 'success']);
} else {
    header('HTTP/1.1 405 Method Not Allowed');
}
