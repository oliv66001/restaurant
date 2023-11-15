document.addEventListener("DOMContentLoaded", function () {
  const submitButton = document.getElementById("submit-button");
  const startInput = document.getElementById("calendar_start");
  const numberOfGuestsInput = document.getElementById("calendar_numberOfGuests");
  const availablePlacesInput = document.getElementById("calendar_availablePlaces");

  // Objet pour annuler les requêtes fetch
  let abortController = new AbortController();

      function updateAvailablePlaces(_event) {
        console.log("Mise à jour des places disponibles...");

        // Ici, vérifie que le nombre de convives est supérieur à 0
        submitButton.addEventListener("click", function(event) {
          if (numberOfGuestsInput.value <= 0) {
              alert("Le nombre de convives doit être supérieur à 0.");
              event.preventDefault(); // Annule l'envoi du formulaire
          }
      });
        // Annule la dernière requête fetch
        abortController.abort();

        // Crée un nouvel objet AbortController
        abortController = new AbortController();

        console.log("Nouvel AbortController créé.");

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

          fetch(
            `/calendar/remaining-places-api?start=${encodeURIComponent(
              start
            )}&numberOfGuests=${encodeURIComponent(numberOfGuests)}`,
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
              console.log("Données récupérées :", data);
              const remainingPlaces = data.remainingPlaces;
              if (remainingPlaces < 0) {
                alert("Les places disponibles ne peuvent pas être négatives.");
                availablePlacesInput.value = 0; // Définir à zéro si négatif
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

  // Contrôle supplémentaire pour le nombre de convives lors de la soumission du formulaire
  submitButton.addEventListener("click", function(event) {
    if (numberOfGuestsInput.value <= 0) {
      alert("Le nombre de convives doit être supérieur à 0.");
      event.preventDefault();
    }
  });
});
