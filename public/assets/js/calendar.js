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
    let abortController = new AbortController(); // Objet pour annuler les requêtes fetch
    dayInput.addEventListener("change", function () {
      const year = document.querySelector(
        "#calendar_start [name='calendar[start][date][year]']"
      ).value;
      const month = document.querySelector(
        "#calendar_start [name='calendar[start][date][month]']"
      ).value;
      const day = this.value;

      // Créer un objet Date
      const selectedDate = new Date(year, month - 1, day); // Les mois dans l'objet Date vont de 0 à 11

      if (selectedDate.getDay() === 1) {
        // 1 représente Lundi
        alert(
          "Le restaurant est fermé ce jour-là. Veuillez choisir un autre jour."
        );
      }
    });

    // Vérifie que le nombre de places demandé est disponible
    submitButton.addEventListener("click", function (event) {
      //event.preventDefault();
      let remainingPlaces = parseInt(availablePlacesInput.value, 10);
      let numberOfGuests = parseInt(numberOfGuestsInput.value, 10);
    });

    function updateAvailablePlaces(event) {
      abortController.abort(); // Annule la dernière requête fetch
      abortController = new AbortController(); // Crée un nouvel objet AbortController

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
          {
            signal: abortController.signal, // Permet d'annuler cette requête si besoin
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
            console.log(availablePlacesInput.value);
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
