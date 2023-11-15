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
    console.log("availablePlacesInput :", availablePlacesInput);
    // Objet pour annuler les requêtes fetch
    let abortController = new AbortController();

    dayInput.addEventListener("change", function () {
      console.log(
        "Changement détecté dans le champ jour, récupération des autres champs..."
      );

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

      console.log(`Date sélectionnée : ${day}-${month}-${year}`);

      // Crée une chaîne de date au format ISO et ensuite un objet Date
      const selectedDateStr = `${year}-${month}-${day}T00:00:00.000Z`;

      const selectedDate = new Date(year, month - 1, day);
      console.log("Date au format objet :", selectedDate);

      // Vérifie maintenant le jour
      const selectedDay = selectedDate.getUTCDay();
      console.log("Jour de la semaine :", selectedDay);

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
        let numberOfGuests = numberOfGuestsInput.value === 0;

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
              availablePlacesInput.value = remainingPlaces;
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

      console.log(
        "Ajout des écouteurs d'événements pour startInput et numberOfGuestsInput"
      );
      startInput.addEventListener("input", updateAvailablePlaces);
      numberOfGuestsInput.addEventListener("input", updateAvailablePlaces);
    });
  });
});
