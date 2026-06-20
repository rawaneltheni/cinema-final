// Clean regular text before comparing it.
// Example: " Avatar " becomes "avatar".
const normalize = (value) => (value || '').trim().toLowerCase();

// Clean seat values before comparing or saving them in the input.
// Example: " a1 " becomes "A1".
const normalizeSeat = (value) => (value || '').trim().toUpperCase();

// Keep only the hour and minute part of a time value.
// Example: "18:30:00" becomes "18:30".
const normalizeTime = (value) => (value || '').slice(0, 5);

// The Blade file stores all reservations as JSON inside a data attribute.
// This function reads that JSON safely and returns an array.
function readReservations(map) {
    try {
        // map.dataset.reservations reads data-reservations="..."
        // If it is missing, use an empty array instead.
        return JSON.parse(map.dataset.reservations || '[]');
    } catch {
        // If the JSON is broken for any reason, fail safely with no reservations.
        return [];
    }
}

// The movie dropdown stores a JSON object that maps movie titles to theaters.
// This function reads it safely and returns an object.
function readMovieTheaters(select) {
    try {
        // select?. means "only try this if select exists".
        // If the data attribute is missing, use an empty object instead.
        return JSON.parse(select?.dataset.movieTheaters || '{}');
    } catch {
        // If the JSON is broken, fail safely with no movie-to-theater mapping.
        return {};
    }
}

// Set up one seat map.
// This function is called once for each element with data-seat-map.
function initSeatMap(map) {
    // Find the form that contains this seat map.
    const form = map.closest('form');

    // If the seat map is not inside a form, there is nothing useful to do.
    if (!form) {
        return;
    }

    // Existing reservations for all shows, passed from Blade as JSON.
    const reservations = readReservations(map);

    // On the edit page, this is the reservation being edited.
    // We ignore it when checking booked seats, so the current reservation
    // does not block its own seat.
    const currentReservationId = Number(map.dataset.currentReservationId || 0);

    // The input that stores the selected seat value for form submission.
    const seatInput = form.querySelector('[data-seat-input]');

    // The small UI label that shows the currently selected seat.
    const selectedLabel = form.querySelector('[data-selected-seat-label]');

    // All clickable seat buttons inside the seat map.
    const seatButtons = [...map.querySelectorAll('[data-seat]')];

    // Counter elements in the UI.
    const bookedCount = map.querySelector('[data-booked-count]');
    const availableCount = map.querySelector('[data-available-count]');
    const totalCount = map.querySelector('[data-total-count]');

    // Important form fields used to decide which show is selected.
    const fields = {
        movieTitle: form.querySelector('[name="movie_title"]'),
        theater: form.querySelector('[name="theater"]'),
        showDate: form.querySelector('[name="show_date"]'),
        showTime: form.querySelector('[name="show_time"]'),
    };

    // A lookup table like:
    // {
    //   "Avatar": "Theater 1",
    //   "Oppenheimer": "Theater 2"
    // }
    const movieTheaters = readMovieTheaters(fields.movieTitle);

    // If we cannot find the seat input or there are no seat buttons,
    // stop before adding event listeners.
    if (!seatInput || seatButtons.length === 0) {
        return;
    }

    // When the user chooses a movie, automatically fill in its theater.
    const syncTheaterForMovie = () => {
        // Get the selected movie title from the movie field.
        const selectedMovie = fields.movieTitle?.value || '';

        // Find the matching theater from the movieTheaters object.
        const theater = movieTheaters[selectedMovie] || '';

        // Put the theater into the theater field if that field exists.
        if (fields.theater) {
            fields.theater.value = theater;
        }
    };

    // Return the currently selected show details in a clean format.
    // This makes comparisons with saved reservations more reliable.
    const currentShow = () => ({
        movieTitle: normalize(fields.movieTitle?.value),
        theater: normalize(fields.theater?.value),
        showDate: fields.showDate?.value || '',
        showTime: normalizeTime(fields.showTime?.value),
    });

    // A show is "complete" only when all details are selected.
    // Without all details, we cannot know which seats are booked.
    const hasCompleteShow = (show) => show.movieTitle && show.theater && show.showDate && show.showTime;

    // Work out which seats are already booked for the currently selected show.
    const bookedSeats = () => {
        // Read the current movie, theater, date, and time.
        const show = currentShow();

        // If the user has not picked the full show yet,
        // show no booked seats because we cannot match reservations properly.
        if (!hasCompleteShow(show)) {
            return new Set();
        }

        // Return a Set of seat numbers, like Set { "A1", "A2" }.
        // A Set is useful because booked.has("A1") is quick and simple.
        return new Set(
            reservations
                // Ignore the reservation currently being edited.
                .filter((reservation) => Number(reservation.id) !== currentReservationId)
                // Keep only reservations for the same movie.
                .filter((reservation) => normalize(reservation.movie_title) === show.movieTitle)
                // Keep only reservations for the same theater.
                .filter((reservation) => normalize(reservation.theater) === show.theater)
                // Keep only reservations for the same date.
                .filter((reservation) => reservation.show_date === show.showDate)
                // Keep only reservations for the same time.
                .filter((reservation) => normalizeTime(reservation.show_time) === show.showTime)
                // Convert the matching reservations into clean seat numbers.
                .map((reservation) => normalizeSeat(reservation.seat_number)),
        );
    };

    // Update the seat map UI based on the current form values.
    // This is called after clicks and after movie/date/time changes.
    const render = () => {
        // Get the seats that are booked for the currently selected show.
        const booked = bookedSeats();

        // Get the currently selected seat from the input.
        let selectedSeat = normalizeSeat(seatInput.value);

        // If the selected seat becomes booked after changing show details,
        // clear it so the form cannot submit a booked seat.
        if (booked.has(selectedSeat)) {
            selectedSeat = '';
            seatInput.value = '';
        }

        // Update every seat button.
        seatButtons.forEach((button) => {
            // Read the seat number from data-seat="..."
            const seat = normalizeSeat(button.dataset.seat);

            // Check if this button's seat is booked or selected.
            const isBooked = booked.has(seat);
            const isSelected = selectedSeat === seat;

            // Pick the state used by CSS to style the button.
            const state = isBooked ? 'booked' : isSelected ? 'selected' : 'available';

            // Store the state in HTML so CSS can style it.
            button.dataset.state = state;

            // Prevent clicking booked seats.
            button.disabled = isBooked;

            // Accessibility: tell screen readers if this seat is selected.
            button.setAttribute('aria-pressed', String(isSelected));

            // Accessibility: give the button a useful label.
            button.setAttribute('aria-label', `Seat ${seat} ${state}`);
        });

        // Count all seats in the map.
        const totalSeats = seatButtons.length;

        // Show the selected seat in the UI.
        // If no seat is selected, show "Choose below".
        if (selectedLabel) {
            selectedLabel.textContent = selectedSeat || 'Choose below';
        }

        // Show how many seats are booked.
        if (bookedCount) {
            bookedCount.textContent = String(booked.size);
        }

        // Show how many seats are still available.
        if (availableCount) {
            availableCount.textContent = String(totalSeats - booked.size);
        }

        // Show the total number of seats.
        if (totalCount) {
            totalCount.textContent = String(totalSeats);
        }
    };

    // When a seat button is clicked, select that seat.
    seatButtons.forEach((button) => {
        button.addEventListener('click', () => {
            // Do nothing if the seat is booked/disabled.
            if (button.disabled) {
                return;
            }

            // Save the clicked seat into the form input.
            seatInput.value = normalizeSeat(button.dataset.seat);

            // Refresh the UI so the clicked seat appears selected.
            render();
        });
    });

    // When the movie changes, update the theater and refresh the seat map.
    fields.movieTitle?.addEventListener('input', () => {
        syncTheaterForMovie();
        render();
    });

    // When theater, date, or time changes, refresh the seat map.
    [fields.theater, fields.showDate, fields.showTime].forEach((field) => field?.addEventListener('input', render));

    // If the seat input is changed manually, refresh the seat map too.
    seatInput.addEventListener('input', render);

    // Run once on page load so the theater field and seat map start correct.
    syncTheaterForMovie();
    render();
}

// Wait until the HTML page is fully loaded.
document.addEventListener('DOMContentLoaded', () => {
    // Find every seat map on the page and initialize it.
    document.querySelectorAll('[data-seat-map]').forEach(initSeatMap);
});
