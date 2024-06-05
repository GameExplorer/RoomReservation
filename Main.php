<?php
session_start();
include 'includes/connection.php';

// Définir la semaine sélectionnée
if (isset($_GET['week'])) {
    $weekOffset = (int) $_GET['week'];
} else {
    $weekOffset = 0;
}

// Date actuelle
$currentDate = new DateTime();
$currentDate->setTime(0, 0, 0);
$currentDate->modify("$weekOffset week");


// Début et fin de la semaine actuelle
$startDate = clone $currentDate;
$endDate = clone $currentDate;
$startDate->modify('-' . $startDate->format('N') . ' days +1 day'); // Lundi de la semaine
$endDate->modify('+' . (7 - $endDate->format('N')) . ' days'); // Dimanche de la semaine

// Tableau des heures de 8h à 17h
$hours = range(8, 16);
$daysOfWeek = array('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes');

$days = [];
$current = clone $startDate;
while ($current <= $endDate) {
    $dayOfWeek = $current->format('N'); // 1 (for Monday) to 7 (for Sunday)
    if ($dayOfWeek < 6) { // Exclude Saturdays (6) and Sundays (7)
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


function generateRandomColor()
{
    $excludedColors = ['#ffffff', '#000000', '#808080'];
    $hex = '#';
    $characters = '0123456789ABCDEF';
    do {
        for ($i = 0; $i < 3; $i++) {
            $hex .= $characters[rand(2, 15)];
        }
    } while (in_array($hex, $excludedColors));
    return $hex;
}

while ($row = $result->fetch_assoc()) {
    $row['color'] = generateRandomColor();
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="style.css">
        <style>
            .current-day {
                background-color: white;
                border: 2px red solid;
            }

            .bookBtn {
                padding: 20px;
                background-color: #f8f9fa;
                border: none;
            }

            .bookBtn:hover {
                background-color: #dfe0e1;
            }

            .booked {
                color: white;
                font-size: 1.25em;
                font-weight: 600;
                text-shadow: #000 1px 0 5px;
                border-radius: 12px;
                cursor: pointer;
            }

            .booked:hover {
                transform: scale(1.05);
                transition: 0.3s ease-in-out;
            }

            .librobtn {
                font-size: 1.1em;
                font-weight: 500;
                text-transform: uppercase;
                padding: 5px 12px;
                border-radius: 7px;
            }

            .removebtn {
                border-radius: 7px;
                width: 40px;
            }

            .past-day {
                background-color: #f0f0f0;
                pointer-events: none;
                cursor: not-allowed;
                opacity: 0.5;
            }

            .past-day-event {
                pointer-events: none;
                cursor: not-allowed;
                opacity: 0.45;
                border-radius: 15px;
            }

            .current-time-line {
                position: absolute;
                width: 100%;
                border-top: 2px solid red;
                z-index: 10;
            }

            .table {
                position: relative;
            }
        </style>
    </head>

    <body>
        <nav class="navbar justify-content-center">
            <h1 class="text-center my-4 pr-5">Reserva de la sala de reuniones</h1>
            <img src="assets/logo2.png" alt="Central Uniformes" height="96" class="float-left">
            <h2 class="my-4"><span style="color:#94c564">Central</span><span style="color:#dc4021">Uniformes</span></h2>
        </nav>
        <div class="container">
            <div class="d-flex justify-content-between mb3">
                <a href="?week=<?php echo $weekOffset - 1; ?>" class="weekbtn btn btn-success"><i
                        class="px-1 fa-solid fa-arrow-left"></i>Semana Pasada</a>
                <a href="?week=<?php echo $weekOffset + 1; ?>" class="weekbtn btn btn-success">Próxima Semana<i
                        class="px-1 fa-solid fa-arrow-right"></i></a>
            </div>
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th class="hours">Hora</th>
                        <?php foreach ($days as $day): ?>
                            <th class="days">
                                <?php echo $daysOfWeek[(new DateTime($day))->format('N') - 1] . '<br>' . (new DateTime($day))->format('d/m/Y'); ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hours as $hour): ?>
                        <tr>
                            <td class="hours"><?php echo $hour . ':00'; ?></td>
                            <?php foreach ($days as $day): ?>
                                <?php
                                $isCurrentDay = ($day == (new DateTime())->format('Y-m-d'));
                                $isPastDay = (new DateTime($day)) < (new DateTime())->setTime(0, 0, 0);
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
                                        $eventClass = $isPastDay ? 'past-day-event' : '';
                                        echo "<td class='booked $eventClass' rowspan='$rowSpan'
                                            style='background-color: {$booking['color']};' 
                                            data-toggle='modal' data-target='#editBookingModal'
                                            data-id='{$booking['id_ticket']}' data-name='{$booking['nombre']}' 
                                            data-start='{$booking['hora_inicio']}' data-end='{$booking['hora_finalizacion']}' 
                                            data-date='{$booking['reserva_reservada']}'>";
                                        echo "<span class='booking-info'>{$booking['nombre']}<br>{$booking['hora_inicio']} - {$booking['hora_finalizacion']}</span>";
                                        echo "</td>";

                                    }
                                } elseif (!$skipCell) {
                                    $cellClass = $isCurrentDay ? 'current-day' : ($isPastDay ? 'past-day' : '');
                                    echo "<td class='$cellClass'>";
                                    if (!$isPastDay) {
                                        echo "<button type='button' class='bookBtn btn btn-calendar' 
                                        ' data-toggle='modal' data-target='#exampleModal' data-day='$day' data-hour='$hour'></button>";
                                    }
                                    echo "</td>";
                                }
                                ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="current-time-line" id="current-time-line"></div>
        </div>

        <!-- Add Booking Modal -->
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
                                <select class="form-control" id="endTime" name="endTime" required></select>
                            </div>
                            <button type="submit" class="lbrobtn btn btn-success">Libro</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Booking Modal -->
        <div class="modal fade" id="editBookingModal" tabindex="-1" aria-labelledby="editBookingModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBookingModalLabel">Editar reserva</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editEventForm">
                            <input type="hidden" id="edit-event-id" name="event-id">
                            <div class="form-group">
                                <label for="edit-event-title">Nombre</label>
                                <input type="text" class="form-control" id="edit-event-title" name="event-title"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="edit-event-date">Fecha:</label>
                                <select class="form-control" id="edit-event-date" name="event-date" required>
                                    <?php foreach ($days as $day): ?>
                                        <?php
                                        $dayDateTime = new DateTime($day);
                                        if ($dayDateTime >= $currentDate) { ?>
                                            <option value="<?php echo $day; ?>" <?php echo ($day == $currentDate->format('Y-m-d')) ? 'selected' : ''; ?>>
                                                <?php echo $daysOfWeek[$dayDateTime->format('N') - 1] . ' ' . $dayDateTime->format('d/m/Y'); ?>
                                            </option>
                                        <?php } ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit-event-start">Hora de inicio:</label>
                                <select class="form-control" id="edit-event-start" name="event-start" required></select>
                            </div>
                            <div class="form-group">
                                <label for="edit-endTime">Hora de finalización:</label>
                                <select class="form-control" id="edit-endTime" name="endTime" required></select>
                            </div>
                            <button type="submit" class="librobtn btn btn-success" style="margin-right: 45%;">Guardar
                                cambios</button>
                            <button type="button" class="removebtn btn btn-danger"><i class="fa-solid fa-trash"
                                    id="deleteBookingButton"></i></button>
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
                function updateCurrentTimeLine() {
                    const now = new Date();
                    const currentHour = now.getHours();
                    const currentMinute = now.getMinutes();
                    const totalMinutes = (currentHour - 8) * 60 + currentMinute; // Assuming calendar starts at 8:00

                    const table = document.querySelector('.table');
                    const hourCell = table.querySelector('td.hours');
                    const cellHeight = hourCell ? hourCell.offsetHeight : 50; // Default to 50px if not found
                    const topPosition = totalMinutes * (cellHeight / 60);

                    const currentTimeLine = document.getElementById('current-time-line');
                    currentTimeLine.style.top = `${topPosition}px`;
                }

                function initializeCurrentTimeLine() {
                    updateCurrentTimeLine();
                    setInterval(updateCurrentTimeLine, 60000); // Update every minute
                }

                document.addEventListener('DOMContentLoaded', initializeCurrentTimeLine);
                // Add Booking Modal
                $('#exampleModal').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget);
                    var day = button.data('day');
                    var hour = button.data('hour');
                    var formattedHour = ('0' + hour).slice(-2) + ':00';

                    var modal = $(this);
                    modal.find('#event-date').val(day);
                    modal.find('#event-hour').val(formattedHour);

                    const startTimeSelect = document.getElementById('event-hour');
                    const endTimeSelect = document.getElementById('endTime');
                    endTimeSelect.innerHTML = ''; // Clear existing options

                    var availableEndTimes = getAvailableEndTimes(day, hour);
                    availableEndTimes.forEach(function (endHour) {
                        const timeString = `${endHour.toString().padStart(2, '0')}:00`;
                        const option = document.createElement('option');
                        option.value = timeString;
                        option.text = timeString;
                        endTimeSelect.add(option);
                    });

                    startTimeSelect.addEventListener('change', function () {
                        var selectedStartTime = parseInt(this.value.split(':')[0]);
                        endTimeSelect.innerHTML = ''; // Clear existing options

                        var availableEndTimes = getAvailableEndTimes(day, selectedStartTime);
                        availableEndTimes.forEach(function (endHour) {
                            const timeString = `${endHour.toString().padStart(2, '0')}:00`;
                            const option = document.createElement('option');
                            option.value = timeString;
                            option.text = timeString;
                            endTimeSelect.add(option);
                        });
                    });

                    if (endTimeSelect.options.length === 0) {
                        modal.find('button[type="submit"]').prop('disabled', true);
                    } else {
                        modal.find('button[type="submit"]').prop('disabled', false);
                    }
                });

                $('#eventForm').on('submit', function (event) {
                    event.preventDefault();

                    var formData = $(this).serialize();

                    $.ajax({
                        url: 'includes/insert_booking.php',
                        type: 'POST',
                        data: formData,
                        success: function (response) {
                            alert(response);
                            $('#exampleModal').modal('hide');
                            location.reload();
                        },
                        error: function (xhr, status, error) {
                            alert('Error: ' + xhr.responseText);
                        }
                    });
                });

                $('#editBookingModal').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget);
                    var id = button.data('id');
                    var name = button.data('name');
                    var date = button.data('date');
                    var start = button.data('start');
                    var end = button.data('end');

                    var modal = $(this);
                    modal.find('#edit-event-id').val(id);
                    modal.find('#edit-event-title').val(name);
                    modal.find('#edit-event-date').val(date);

                    populateStartTimes(date, start, end);

                    modal.find('#edit-event-date').on('change', function () {
                        var selectedDate = $(this).val();
                        populateStartTimes(selectedDate, start, end);
                    });

                    function populateStartTimes(date, start, end) {
                        const startTimeSelect = modal.find('#edit-event-start')[0];
                        const endTimeSelect = modal.find('#edit-endTime')[0];

                        startTimeSelect.innerHTML = '';
                        endTimeSelect.innerHTML = '';

                        var availableStartTimes = getAvailableStartTimes(date);
                        availableStartTimes.forEach(function (startHour) {
                            const timeString = `${startHour.toString().padStart(2, '0')}:00`;
                            const option = document.createElement('option');
                            option.value = timeString;
                            option.text = timeString;
                            startTimeSelect.add(option);
                        });

                        if (start) {
                            startTimeSelect.value = start;
                        }

                        populateEndTimes(date, start ? parseInt(start.split(':')[0]) : availableStartTimes[0]);

                        startTimeSelect.addEventListener('change', function () {
                            var selectedStartTime = parseInt(this.value.split(':')[0]);
                            populateEndTimes(date, selectedStartTime);
                        });

                        if (endTimeSelect.options.length === 0) {
                            modal.find('button[type="submit"]').prop('disabled', true);
                        } else {
                            modal.find('button[type="submit"]').prop('disabled', false);
                        }
                    }

                    function populateEndTimes(date, startHour) {
                        const endTimeSelect = modal.find('#edit-endTime')[0];
                        endTimeSelect.innerHTML = ''; // Clear existing options

                        var availableEndTimes = getAvailableEndTimes(date, startHour);
                        availableEndTimes.forEach(function (endHour) {
                            const timeString = `${endHour.toString().padStart(2, '0')}:00`;
                            const option = document.createElement('option');
                            option.value = timeString;
                            option.text = timeString;
                            endTimeSelect.add(option);
                        });

                        if (end) {
                            endTimeSelect.value = end;
                        }
                    }

                    $('#editEventForm').on('submit', function (event) {
                        event.preventDefault();

                        var formData = $(this).serialize();

                        $.ajax({
                            url: 'includes/edit_booking.php',
                            type: 'POST',
                            data: formData,
                            success: function (response) {
                                alert(response);
                                $('#editBookingModal').modal('hide');
                                location.reload();
                            },
                            error: function (xhr, status, error) {
                                alert('Error: ' + xhr.responseText);
                            }
                        });
                    });
                });
            });

            function getAvailableStartTimes(date) {
                var startTimes = [];
                for (let hour = 8; hour <= 16; hour++) { // Available start times from 08:00 to 16:00
                    if (!isBooked(date, hour)) {
                        startTimes.push(hour);
                    }
                }
                return startTimes;
            }

            function getAvailableEndTimes(date, startHour) {
                var endTimes = [];
                for (let hour = startHour + 1; hour <= 17; hour++) {
                    if (!isBooked(date, hour)) {
                        endTimes.push(hour);
                    } else {
                        endTimes.push(hour);
                        break;
                    }
                }
                return endTimes;
            }

            function isBooked(date, hour) {
                var bookings = <?php echo json_encode($structuredBookings); ?>;
                return bookings[date] && bookings[date][hour];
            }


            $('#deleteBookingButton').click(function () {
                var bookingId = $('#edit-event-id').val();

                if (confirm('¿Estás seguro de que deseas eliminar esta reserva?')) {
                    $.ajax({
                        url: 'includes/delete_booking.php',
                        type: 'POST',
                        data: { id: bookingId },
                        success: function (response) {
                            if (response === 'success') {
                                //alert('Reserva eliminada correctamente.');
                                $('#editBookingModal').modal('hide');
                                location.reload();
                            } else {
                                alert('Error al eliminar la reserva.');
                            }
                        },
                        error: function (xhr, status, error) {
                            alert('Error: ' + xhr.responseText);
                        }
                    });
                }
            });

        </script>
    </body>

</html>