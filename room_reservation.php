<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Calendar Room Reservation</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <style>
            .calendar {
                margin-top: 20px;
            }

            .day {
                padding: 10px;
                border: 1px solid #ddd;
            }

            .time-slot {
                height: 30px;
                border: 1px solid #ddd;
                cursor: pointer;
            }

            .booked {
                background-color: #d4edda;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="calendar">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Monday</th>
                            <th>Tuesday</th>
                            <th>Wednesday</th>
                            <th>Thursday</th>
                            <th>Friday</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tbody>
                        <tr>
                            <td>8</td>
                            <td class="empty-cell"></td>
                            <!-- Add more empty cells for other days -->
                            <td class="empty-cell"></td>
                            <td class="empty-cell"></td>
                        </tr>
                        <tr>
                            <td>8:15</td>
                            <td>Marc</td>
                            <!-- Add more names for other days -->
                            <td class="empty-cell"></td>
                            <td class="empty-cell"></td>
                        </tr>
                        <tr>
                            <td>8:30</td>
                            <td class="empty-cell"></td>
                            <td class="empty-cell"></td>
                            <td class="empty-cell"></td>
                        </tr>
                        <tr>
                            <td>8:45</td>
                            <td class="empty-cell"></td>
                            <td class="empty-cell"></td>
                            <td class="empty-cell"></td>
                        </tr>
                        <!-- Add more rows for other time slots -->
                    </tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Booking Modal -->
        <div class="modal" id="bookingModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Book a Room</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="bookingForm">
                            <div class="form-group">
                                <label for="username">Name:</label>
                                <input type="text" class="form-control" id="username" required>
                            </div>
                            <div class="form-group">
                                <label for="startTime">Start Time:</label>
                                <input type="time" class="form-control" id="startTime" required readonly>
                            </div>
                            <div class="form-group">
                                <label for="endTime">End Time:</label>
                                <input type="time" class="form-control" id="endTime" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Book</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="calendar.js"></script>
    </body>

</html>