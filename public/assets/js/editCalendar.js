document.addEventListener("DOMContentLoaded", function () {
  setTimeout(function () {
    const submitButton = document.getElementById("submit-button");
    const startInput = document.getElementById("calendar_start");
    const numberOfGuestsInput = document.getElementById(
      "calendar_numberOfGuests"
    );
    const availablePlacesInput = document.getElementById(
      "calendar_availablePlaces"
    );
    const dayInput = document.querySelector(
      "#calendar_start [name='calendar[start][date][day]']"
    );
    // Objet pour annuler les requêtes fetch
    let abortController = new AbortController(); 
    dayInput.addEventListener("change", function () {
      let year = document.querySelector(
        "#calendar_start [name='calendar[start][date][year]']"
      ).value;
      let month = document.querySelector(
        "#calendar_start [name='calendar[start][date][month]']"
      ).value;
      let day = document.querySelector(
        "#calendar_start [name='calendar[start][date][day]']"
      ).value;

      // Assure-toi que le mois et le jour ont deux chiffres
      month = String(month).padStart(2, "0");
      day = String(day).padStart(2, "0");

      // Crée une chaîne de date au format ISO et ensuite un objet Date
      const selectedDateStr = `${year}-${month}-${day}T00:00:00.000Z`;
      
      const selectedDate = new Date(year, month - 1, day);

      // Vérifie maintenant le jour
      const selectedDay = selectedDate.getUTCDay();

      function updateAvailablePlaces(event) {
        // Annule la dernière requête fetch
        abortController.abort(); 
         // Crée un nouvel objet AbortController
        abortController = new AbortController();

        let year = document.querySelector(
          "#calendar_start [name='calendar[start][date][year]']"
        ).value;
        let month = document.querySelector(
          "#calendar_start [name='calendar[start][date][month]']"
        ).value;
        let day = document.querySelector(
          "#calendar_start [name='calendar[start][date][day]']"
        ).value;
        let hour = document.querySelector(
          "#calendar_start [name='calendar[start][time][hour]']"
        ).value;
        let minute = document.querySelector(
          "#calendar_start [name='calendar[start][time][minute]']"
        ).value;
        let start = `${year}-${month}-${day} ${hour}:${minute}`;
        let numberOfGuests = numberOfGuestsInput.value;

        if (start && numberOfGuests) {
          fetch(
            `/calendar/remaining-places-api?start=${encodeURIComponent(
              start
            )}&numberOfGuests=${encodeURIComponent(numberOfGuests)}`,
            // Annulation de cette requête si besoin!
            {
              signal: abortController.signal, 
            }
          )
            .then((response) => {
              if (!response.ok) {
                throw new Error("Erreur réseau");
              }
              return response.json();
            })
            .then((data) => {
              const remainingPlaces = data.remainingPlaces;
              availablePlacesInput.value = remainingPlaces;
            })
            .catch((error) => {
              if (error.name === "AbortError") {
                console.log("Requête annulée");
                return; // Requête annulée, rien à faire
              }
              console.error(
                "Il y a eu un problème avec l'opération fetch: ",
                error
              );
            });
        } else {
          availablePlacesInput.value = "";
        }
      }

      startInput.addEventListener("input", updateAvailablePlaces);
      numberOfGuestsInput.addEventListener("input", updateAvailablePlaces);
    });
  });
});
