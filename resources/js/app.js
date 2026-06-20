// Seat selection for the booking page.
// Booked seats are rendered by the server (data-state="booked" + disabled);
// this script only handles choosing one of the remaining open seats.
function initSeatMap(map) {
    const form = map.closest('form');

    if (!form) {
        return;
    }

    // Hidden input that carries the chosen seat with the form submission.
    const seatInput = form.querySelector('[data-seat-input]');

    // Label that shows the currently selected seat to the user.
    const selectedLabel = form.querySelector('[data-selected-seat-label]');

    // Every clickable seat button in the grid.
    const seatButtons = [...map.querySelectorAll('[data-seat]')];

    if (!seatInput || seatButtons.length === 0) {
        return;
    }

    const normalizeSeat = (value) => (value || '').trim().toUpperCase();

    // Repaint the grid so the chosen seat looks "selected" and update the label.
    const render = () => {
        const selectedSeat = normalizeSeat(seatInput.value);

        seatButtons.forEach((button) => {
            // Never touch seats that are already booked.
            if (button.dataset.state === 'booked') {
                return;
            }

            const isSelected = normalizeSeat(button.dataset.seat) === selectedSeat;
            button.dataset.state = isSelected ? 'selected' : 'available';
            button.setAttribute('aria-pressed', String(isSelected));
        });

        if (selectedLabel) {
            selectedLabel.textContent = selectedSeat || 'Choose below';
        }
    };

    seatButtons.forEach((button) => {
        button.addEventListener('click', () => {
            // Booked seats are disabled, but guard anyway.
            if (button.dataset.state === 'booked' || button.disabled) {
                return;
            }

            seatInput.value = normalizeSeat(button.dataset.seat);
            render();
        });
    });

    render();
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-seat-map]').forEach(initSeatMap);
});
