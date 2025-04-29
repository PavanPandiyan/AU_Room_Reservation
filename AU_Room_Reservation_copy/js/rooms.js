// Initialize Flatpickr for date selection
flatpickr("#checkin_checkout_date", {
    mode: "range",
    dateFormat: "Y-m-d",
    minDate: "today"
});

// Room selection logic
let selectedRooms = [];
document.querySelectorAll(".room-card").forEach(card => {
    card.addEventListener("click", function () {
        if (!this.classList.contains("booked")) {
            let roomId = this.getAttribute("data-room-id");
            if (selectedRooms.includes(roomId)) {
                selectedRooms = selectedRooms.filter(id => id !== roomId);
                this.classList.remove("selected");
            } else {
                selectedRooms.push(roomId);
                this.classList.add("selected");
            }
        }
    });
});

document.getElementById('book-now').addEventListener('click', function () {
    let selectedRooms = [];
    document.querySelectorAll('.room-card.selected').forEach(room => {
        selectedRooms.push(room.getAttribute('data-room-id'));
    });

    if (selectedRooms.length === 0) {
        alert('Please select at least one room.');
        return;
    }

    let checkinDate = document.getElementById('checkin_checkout_date').value.split(' to ')[0];
    let checkoutDate = document.getElementById('checkin_checkout_date').value.split(' to ')[1];

    if (!checkinDate || !checkoutDate) {
        alert('Please select check-in and check-out dates.');
        return;
    }

    let url = `reservation_form.php?rooms=${selectedRooms.join(',')}&checkin=${checkinDate}&checkout=${checkoutDate}`;
    window.location.href = url; // Redirect to reservation form
});
