document.addEventListener("DOMContentLoaded", function () {
  const submitButton = document.getElementById("submit-button");
  const startInput = document.getElementById("calendar_start");
  const numberOfGuestsInput = document.getElementById("calendar_numberOfGuests");
  const availablePlacesInput = document.getElementById("calendar_availablePlaces");

  // Objet pour annuler les requêtes fetch
  let abortController = new AbortController();

  function updateAvailablePlaces(_event) {

    availablePlacesInput.value = "";
    console.log("Mise à jour des places disponibles..." + availablePlacesInput.value);

    // Annule la dernière requête fetch
    abortController.abort();

    // Crée un nouvel objet AbortController
    abortController = new AbortController();

    console.log("Nouvel AbortController créé.");
    console.log(numberOfGuestsInput.value, availablePlacesInput.value, startInput.value);

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
      console.log(
        `Fetching remaining places with start = ${start} and numberOfGuests = ${numberOfGuests}`
      );

      // Récupération des places disponibles
      const reservationId = calendar.id; // Remplace 'calendar.id' par le moyen approprié pour obtenir l'ID de la réservation actuelle

      fetch(`/calendar/remaining-places-api?start=${encodeURIComponent(start)}&numberOfGuests=${encodeURIComponent(numberOfGuests)}&reservationId=${encodeURIComponent(reservationId)}`, {
          signal: abortController.signal,
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Erreur réseau");
          }
          return response.json();
        })
        .then((data) => {
          const remainingPlaces = data.remainingPlaces;
          console.log("Remaining places after API call: ", remainingPlaces);
      
          if (remainingPlaces < 0) {
              alert("Il n'y a pas assez de places disponibles pour accueillir votre nouvelle demande. Veuillez réduire le nombre de places ou choisir un autre horaire.");
              // Tu peux choisir de conserver ou de réinitialiser le champ des places disponibles
              availablePlacesInput.value = 0; // Ou une autre logique appropriée
          } else {
              availablePlacesInput.value = remainingPlaces;
          }
      })
      
      
        .catch((error) => {
          if (error.name === "AbortError") {
            console.log("Requête annulée");
            return;
          }
          console.error(
            "Il y a eu un problème avec l'opération fetch: ",
            error
          );
        });
    } else {
      console.log(
        "Les champs start ou numberOfGuests sont vides. Pas de fetch effectué."
      );
      availablePlacesInput.value = "";
    }
  }

  // Attachement des écouteurs d'événements
  startInput.addEventListener("input", updateAvailablePlaces);
  numberOfGuestsInput.addEventListener("input", updateAvailablePlaces);
});
