document.addEventListener("DOMContentLoaded", function () {
    //const calendarId = document.querySelector("[data-calendar-id]").getAttribute("data-calendar-id"); // Remplacer avec l'ID de ton formulaire
    //const startInput = document.getElementById("calendar_start");
    const form = document.getElementById("calendar_start");
    const numberOfGuestsInput = document.getElementById("calendar_numberOfGuests");

    function updateAvailablePlaces() {
        let year = document.querySelector("#calendar_start [name='calendar[start][date][year]']").value;
        let month = document.querySelector("#calendar_start [name='calendar[start][date][month]']").value;
        let day = document.querySelector("#calendar_start [name='calendar[start][date][day]']").value;
        let hour = document.querySelector("#calendar_start [name='calendar[start][time][hour]']").value;
        let minute = document.querySelector("#calendar_start [name='calendar[start][time][minute]']").value;
        let start = `${year}-${month}-${day} ${hour}:${minute}`;
        let numberOfGuests = numberOfGuestsInput.value;

        if (start && numberOfGuests) {
            fetch(`/calendar/remaining-places-api?start=${encodeURIComponent(start)}&numberOfGuests=${encodeURIComponent(numberOfGuests)}`)
            .then(response => {
                return response.json();
            })
            .then(data => {
                const remainingPlaces = data.remainingPlaces;
                document.getElementById("calendar_availablePlaces").value = remainingPlaces;

                // Afficher un message si le nombre de places disponibles est insuffisant
                if (remainingPlaces < numberOfGuests) {
                    alert("Le nombre de places demandé n'est pas disponible.");
                }
            });
        } else {
            document.getElementById("calendar_availablePlaces").value = '';
        }
    }

    form.addEventListener("submit", function(event) {
        let year = document.querySelector("#calendar_start [name='calendar[start][date][year]']").value;
        let month = document.querySelector("#calendar_start [name='calendar[start][date][month]']").value;
        let day = document.querySelector("#calendar_start [name='calendar[start][date][day]']").value;
        let hour = document.querySelector("#calendar_start [name='calendar[start][time][hour]']").value;
        let minute = document.querySelector("#calendar_start [name='calendar[start][time][minute]']").value;
        let start = `${year}-${month}-${day} ${hour}:${minute}`;
        let numberOfGuests = numberOfGuestsInput.value;

        if (start && numberOfGuests) {
            fetch(`/calendar/remaining-places-api?start=${encodeURIComponent(start)}&numberOfGuests=${encodeURIComponent(numberOfGuests)}`)
            .then(response => {
                return response.json();
            })
            .then(data => {
                const remainingPlaces = data.remainingPlaces;

                if (remainingPlaces < numberOfGuests) {
                    alert("Le nombre de places demandé n'est pas disponible.");
                    event.preventDefault(); // Annuler l'envoi du formulaire
                }
            });
        } else {
            // Condition si les champs sont vides ou invalides
            event.preventDefault(); // Annuler l'envoi du formulaire
        }
        startInput.addEventListener("input", updateAvailablePlaces);
        numberOfGuestsInput.addEventListener("input", updateAvailablePlaces);
    }, 500);
    });


