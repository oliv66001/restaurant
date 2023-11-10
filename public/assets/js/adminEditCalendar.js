/*document.addEventListener("DOMContentLoaded", function () {
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
        const year = document.querySelector("#calendar_start [name='calendar[start][date][year]']").value;
        const month = document.querySelector("#calendar_start [name='calendar[start][date][month]']").value;
        const day = this.value;
    
        // Créer un objet Date
        const selectedDate = new Date(year, month - 1, day); // Les mois dans l'objet Date vont de 0 à 11
    
        if (closedDays.includes(selectedDate.getDay())) {
          alert("Le restaurant est fermé ce jour-là. Veuillez choisir un autre jour.");
        }
      });

      startInput.addEventListener("input", updateAvailablePlaces);
      numberOfGuestsInput.addEventListener("input", updateAvailablePlaces);
    }, 500);
});*/