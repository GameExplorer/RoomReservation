<?php
session_start();
include 'includes/connection.php';

// Date actuelle
$currentDate = new DateTime();
$startDate = clone $currentDate;
$endDate = clone $currentDate;
$startDate->modify('-3 days');
$endDate->modify('+3 days');

// Tableau des heures de 8h à 17h
$hours = range(8, 17);
$daysOfWeek = array('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes');

// Générer les jours à afficher, en excluant les samedis et dimanches
$days = [];
$current = clone $startDate;
while ($current <= $endDate) {
    $dayOfWeek = $current->format('N'); // 1 (pour Lundi) à 7 (pour Dimanche)
    if ($dayOfWeek < 6) { // Exclure les samedis (6) et dimanches (7)
        $days[] = $current->format('Y-m-d');
    }
    $current->modify('+1 day');
}

// Fetch existing bookings
$bookings = [];
$sql = "SELECT id_ticket, nombre, hora_inicio, hora_finalizacion, reserva_reservada FROM reserva_de_habitacion WHERE DATE(reserva_reservada) BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$startDateString = $startDate->format('Y-m-d');
$endDateString = $endDate->format('Y-m-d');
$stmt->bind_param('ss', $startDateString, $endDateString);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

// Structure bookings data by date and hour
$structuredBookings = [];
foreach ($bookings as $booking) {
    $date = $booking['reserva_reservada'];
    $startHour = (int) explode(':', $booking['hora_inicio'])[0];
    $endHour = (int) explode(':', $booking['hora_finalizacion'])[0];

    if (!isset($structuredBookings[$date])) {
        $structuredBookings[$date] = [];
    }

    for ($hour = $startHour; $hour < $endHour; $hour++) {
        if (!isset($structuredBookings[$date][$hour])) {
            $structuredBookings[$date][$hour] = [];
        }
        $structuredBookings[$date][$hour][] = $booking;
    }
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Calendar</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
            rel="stylesheet">

        <style>
            .current-day {
                background-color: grey;
            }

            .btn-calendar {
                width: 100%;
                height: 100%;
            }

            .booked {
                background-color: red;
                color: white;
                border-radius: 5px;
                padding: 4px 8px;
            }

            h1 {
                font-weight: 500;
            }

            h2 {
                font-family: "Lato", sans-serif;
                font-size: 2.5em;
                font-weight: 700;

            }
        </style>
    </head>

    <body>
        <nav class="navbar justify-content-center">
            <h1 class="text-center my-4 pr-5">Reserva de la sala de reuniones</h1>
            <img src="assets/logo2.png" alt="Central Uniformes" height="96" class=" float-left">
            <h2 class="my-4"><span style="color:#94c564">Central</span><span style="color:#dc4021">Uniformes</span></h2>
        </nav>
        <div class="container">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Hora</th>
                        <?php foreach ($days as $day): ?>
                            <th>
                                <?php echo $daysOfWeek[(new DateTime($day))->format('N') - 1] . '<br>' . (new DateTime($day))->format('d/m/Y'); ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hours as $hour): ?>
                        <tr>
                            <td><?php echo $hour . ':00'; ?></td>
                            <?php foreach ($days as $day): ?>
                                <?php
                                $isCurrentDay = ($day == $currentDate->format('Y-m-d'));
                                $skipCell = false;

                                // Check if the current cell should be skipped
                                if (isset($skipCells[$day][$hour])) {
                                    $skipCell = true;
                                }

                                if (isset($structuredBookings[$day][$hour]) && !$skipCell) {
                                    $booking = $structuredBookings[$day][$hour][0];
                                    $startHour = (int) explode(':', $booking['hora_inicio'])[0];
                                    $endHour = (int) explode(':', $booking['hora_finalizacion'])[0];
                                    $rowSpan = $endHour - $startHour;

                                    // Mark subsequent cells to be skipped due to rowspan
                                    for ($h = $startHour + 1; $h < $endHour; $h++) {
                                        $skipCells[$day][$h] = true;
                                    }

                                    if ($hour == $startHour) {
                                        echo "<td class='booked' rowspan=$rowSpan>";
                                        echo "{$booking['nombre']}<br>{$booking['hora_inicio']} - {$booking['hora_finalizacion']}";
                                        echo "</td>";
                                    }
                                } elseif (!$skipCell) {
                                    echo "<td>";
                                    echo "<button type='button' class='btn btn-light btn-calendar " .
                                        ($isCurrentDay ? 'current-day' : '') . "' data-toggle='modal' data-target='#exampleModal' data-day='$day' data-hour='$hour'></button>";
                                    echo "</td>";
                                }
                                ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar un evento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="eventForm">
                            <div class="form-group">
                                <label for="event-title">Nombre</label>
                                <input type="text" class="form-control" id="event-title" name="event-title" required>
                            </div>
                            <div class="form-group">
                                <label for="event-date">Fecha:</label>
                                <input type="text" class="form-control" id="event-date" name="event-date" readonly>
                            </div>
                            <div class="form-group">
                                <label for="event-hour">Hora de inicio:</label>
                                <input type="text" class="form-control" id="event-hour" name="event-hour" readonly>
                            </div>
                            <div class="form-group">
                                <label for="endTime">Hora de finalización:</label>
                                <input type="time" class="form-control" id="endTime" name="endTime" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Libro</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#exampleModal').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget);
                    var day = button.data('day');
                    var hour = button.data('hour');
                    var formattedHour = ('0' + hour).slice(-2) + ':00';

                    var modal = $(this);
                    modal.find('#event-date').val(day);
                    modal.find('#event-hour').val(formattedHour);
                });

                $('#eventForm').on('submit', function (event) {
                    event.preventDefault();

                    var formData = $(this).serialize();

                    $.ajax({
                        url: 'insert_booking.php',
                        type: 'POST',
                        data: formData,
                        success: function (response) {
                            alert(response);
                            $('#exampleModal').modal('hide');
                            location.reload(); // Reload to see the updated bookings
                        },
                        error: function (xhr, status, error) {
                            alert('Error: ' + xhr.responseText);
                        }
                    });
                });
            });
        </script>
    </body>

</html>