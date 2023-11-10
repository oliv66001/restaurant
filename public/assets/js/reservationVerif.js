document.addEventListener('DOMContentLoaded', function() {
    const allergieInput = document.querySelector('#calendar_allergieOfGuests'); // Remplace avec le bon sélecteur
    const submitButton = document.querySelector('#submit-button'); // Sélecteur du bouton de soumission
    const errorMessage = document.querySelector('#error-message'); // Sélecteur du message d'erreur
     if (allergieInput) { // Ajout d'une vérification pour s'assurer que allergieInput n'est pas null
      allergieInput.addEventListener('input', function() {
        const regex = /[<>.+\$%\/;:!?@€*-]/;
        console.log('input event fired');
        if (regex.test(this.value)) {
          errorMessage.style.display = 'inline'; // Montre le message d'erreur
          submitButton.disabled = true; // Désactive le bouton
        } else {
          errorMessage.style.display = 'none'; // Cache le message d'erreur
          submitButton.disabled = false; // Réactive le bouton
        }
      });
    }
  });
  