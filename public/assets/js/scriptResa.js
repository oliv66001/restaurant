function validateForm() {
    // Récupération des champs date, heure et nombre de personnes
    const date = document.getElementById("date").value;
    const time = document.getElementById("time").value;
    const nbPeople = document.getElementById("nb_people").value;
  
    // Vérification que tous les champs sont renseignés
    if (date === "" || time === "" || nbPeople === "") {
      alert("Veuillez renseigner tous les champs");
      return false;
    }
  
    // Vérification que le nombre de personnes est compris entre 1 et 10
    if (nbPeople < 1 || nbPeople > 10) {
      alert("Le nombre de personnes doit être compris entre 1 et 10");
      return false;
    }
  
    // Vérification que l'heure de réservation est entre 11h et 14h
    const timeParts = time.split(":");
    const hour = parseInt(timeParts[0]);
    if (hour < 11 || hour >= 14) {
      alert("Les réservations ne sont possibles que entre 11h et 14h");
      return false;
    }
  
    // Si toutes les vérifications ont été passées, le formulaire est valide
    return true;
  }
  