<?php
// Date actuelle
$currentDate = new DateTime();

// Cloner la date actuelle pour ne pas la modifier
$startDate = clone $currentDate;
$endDate = clone $currentDate;

// Ajouter et soustraire 3 jours pour définir la période d'affichage
$startDate->modify('-3 days');
$endDate->modify('+3 days');

// Tableau des heures de 8h à 17h
$hours = range(8, 17);

// Tableau des jours de la semaine
$daysOfWeek = array('Lun', 'Mar', 'Mer', 'Jeu', 'Ven');

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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calendar Room Reservation</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .current-day {
            background-color: grey; /* Color of the main day  */
        }
        .btn-calendar {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center my-4">Calendar</h1>
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Heure</th>
                    <?php foreach ($days as $day) : ?>
                        <th><?php echo $daysOfWeek[(new DateTime($day))->format('N') - 1] . '<br>' . (new DateTime($day))->format('d/m/Y'); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hours as $hour) : ?>
                    <tr>
                        <td><?php echo $hour . ':00'; ?></td>
                        <?php foreach ($days as $day) : ?>
                            <?php $isCurrentDay = ($day == $currentDate->format('Y-m-d')); ?>
                            <td>
                                <button type="button" class="btn btn-light btn-calendar <?php echo $isCurrentDay ? 'current-day' : ''; ?>" data-toggle="modal" data-target="#exampleModal" data-day="<?php echo $day; ?>" data-hour="<?php echo $hour; ?>">
                                    <!-- Vous pouvez ajouter ici le contenu du bouton, par exemple, un événement ou un rendez-vous -->
                                </button>
                            </td>
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
                    <h5 class="modal-title" id="exampleModalLabel">Ajouter un événement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="eventForm">
                        <div class="form-group">
                            <label for="event-title">Name</label>
                            <input type="text" class="form-control" id="event-title">
                        </div>
                        <div class="form-group">
                            <label for="event-date">Date</label>
                            <input type="text" class="form-control" id="event-date" readonly>
                        </div>
                        <div class="form-group">
                            <label for="event-hour">Start time</label>
                            <input type="text" class="form-control" id="event-hour" readonly>
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var day = button.data('day') // Extract info from data-* attributes
            var hour = button.data('hour')
            var modal = $(this)
            modal.find('#event-date').val(day)
            modal.find('#event-hour').val(hour + ':00')
        })

        $('#eventForm').on('submit', function (event) {
            event.preventDefault()
            // Logique pour enregistrer l'événement peut être ajoutée ici
            // Par exemple, une requête AJAX pour sauvegarder les données dans la base de données
            alert('Événement enregistré pour ' + $('#event-date').val() + ' à ' + $('#event-hour').val())
            $('#exampleModal').modal('hide')
        })
    </script>
</body>
</html>
