$(document).ready(function () {
    // Lorsque le formulaire est soumis
    $("form").on("submit", function (event) {
        // Retire les anciens messages d'erreur
        $('.error-message').remove();

        // Initialise une variable pour les erreurs
        let errors = false;

        // Vérifie le champ du nom complet
        const fullName = $("input[name='contact_form[full_name]']").val();
        const illegalChars = /[<>,.+\$%\/;:!?@€*-]/;
        if (fullName.length < 3 || illegalChars.test(fullName)) {
            $("input[name='contact_form[full_name]']").after('<span class="error-message">Le nom complet doit avoir au moins 3 caractères et ne peut pas contenir de caractères spéciaux.</span>');
            errors = true;
        }

        // Vérifie le champ email
        const email = $("input[name='contact_form[email]']").val();
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        if (!emailPattern.test(email)) {
            $("input[name='contact_form[email]']").after('<span class="error-message">L\'adresse e-mail n\'est pas valide.</span>');
            errors = true;
        }

        // Vérifie le champ téléphone
        const phone = $("input[name='contact_form[phone]']").val();
        const phonePattern = /^[0-9]+$/;
        if (!phonePattern.test(phone) || phone.length != 10) {
            $("input[name='contact_form[phone]']").after('<span class="error-message">Le numéro de téléphone n\'est pas valide.</span>');
            errors = true;
        }

        // Vérifie le champ sujet
        const subject = $("input[name='contact_form[subject]']").val();
        if (subject && subject.length < 5) {
            $("input[name='contact_form[subject]']").after('<span class="error-message">Le sujet doit avoir au moins 5 caractères.</span>');
            errors = true;
        }

        // Vérifie le champ message
        const message = $("textarea[name='contact_form[message]']").val();
        if (message && message.length < 10) {
            $("textarea[name='contact_form[message]']").after('<span class="error-message">Le message doit avoir au moins 10 caractères.</span>');
            errors = true;
        }

        // Si des erreurs ont été trouvées, empêche la soumission du formulaire
        if (errors) {
            event.preventDefault();
        }
    });
});
