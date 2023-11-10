$(document).ready(function () {
    // Lorsque le formulaire est soumis
    $("form").on("submit", function (event) {
        // Retire les anciens messages d'erreur
        $('.error-message').remove();

        // Initialise une variable pour les erreurs
        let errors = false;

        // Vérifie le champ email
        const email = $("input[name='email']").val();
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        if (!emailPattern.test(email)) {
            $("<div class='error-message'>L'adresse e-mail n'est pas valide.</div>").insertAfter("input[name='email']");
            errors = true;
        }

        // Vérifie le champ mot de passe
        const password = $("input[name='password']").val();
        if (password.length < 8) {
            $("<div class='error-message'>Le mot de passe doit avoir au moins 8 caractères.</div>").insertAfter("input[name='password']");
            errors = true;
        }

        // Si des erreurs ont été trouvées, empêche la soumission du formulaire
        if (errors) {
            event.preventDefault();
        }
    });
});
