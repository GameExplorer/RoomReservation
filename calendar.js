document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.querySelector('.calendar tbody');
    const bookingForm = document.getElementById('bookingForm');
    const startTimeInput = document.getElementById('startTime');
    const endTimeInput = document.getElementById('endTime');
    let selectedSlot = null;

    function renderTimeSlots() {
        tableBody.innerHTML = '';
        for (let hour = 8; hour <= 17; hour++) {
            const row = document.createElement('tr');
            const timeSlot = document.createElement('td');
            timeSlot.textContent = hour + ':00';
            row.appendChild(timeSlot);
            for (let day = 0; day < 5; day++) {
                const cell = document.createElement('td');
                const slot = document.createElement('div');
                slot.className = 'time-slot';
                slot.dataset.time = `${hour}:00`;
                slot.textContent = `${hour}:00`;
                slot.addEventListener('click', () => {
                    selectedSlot = slot;
                    startTimeInput.value = `${hour.toString().padStart(2, '0')}:00`;
                    populateEndTimeOptions(hour, '00');
                    $('#bookingModal').modal('show');
                });
                cell.appendChild(slot);
                row.appendChild(cell);
            }
            tableBody.appendChild(row);
        }
    }

    function populateEndTimeOptions(startHour, startMinute) {
        endTimeInput.innerHTML = '';
        for (let hour = startHour; hour <= 17; hour++) {
            const option = document.createElement('option');
            option.value = `${hour.toString().padStart(2, '0')}:${startMinute}`;
            option.textContent = `${hour}:${startMinute}`;
            endTimeInput.appendChild(option);
        }
    }

    bookingForm.addEventLis tener('submit', function (e) {
        e.preventDefault();
        const username = document.getElementById('username').value;
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;

        if (selectedSlot) {
            const selectedHour = parseInt(startTime.split(':')[0]);
            const selectedMinute = parseInt(startTime.split(':')[1]);

            const slots = document.querySelectorAll('.time-slot');
            let booking = false;
            slots.forEach(slot => {
                const slotTime = slot.dataset.time;
                const slotHour = parseInt(slotTime.split(':')[0]);
                const slotMinute = parseInt(slotTime.split(':')[1]);
                if (!booking && slotHour === selectedHour && slotMinute === selectedMinute) {
                    booking = true;
                }
                if (booking) {
                    slot.classList.add('booked');
                    slot.textContent = `${username} (${startTime} - ${endTime})`;
                }
                if (slotHour === parseInt(endTime.split(':')[0]) && slotMinute === parseInt(endTime.split(':')[1])) {
                    booking = false;
                }
            });
        }

        $('#bookingModal').modal('hide');
    });

    renderTimeSlots();
});
