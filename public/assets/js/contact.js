$(document).ready(function () {
    $("form").on("submit", function (event) {
        $('.error-message').remove();
        let errors = false;

        // Fonction pour afficher les messages d'erreur
        function showError(selector, message) {
            $(selector).after(`<span class="error-message">${message}</span>`);
            errors = true;
        }

        // Vérifications
        $("input[name^='contact_form']").each(function() {
            const name = $(this).attr('name');
            const value = $(this).val();

            switch(name) {
                case 'contact_form[full_name]':
                    if (value.length < 3 || /[<>,.+\$%\/;:!?@€*-]/.test(value)) {
                        showError(this, 'Le nom complet doit avoir au moins 3 caractères et ne peut pas contenir de caractères spéciaux.');
                    }
                    break;
                case 'contact_form[email]':
                    if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(value)) {
                        showError(this, 'L\'adresse e-mail n\'est pas valide.');
                    }
                    break;
                case 'contact_form[phone]':
                    if (!/^[0-9]+$/.test(value) || value.length != 10) {
                        showError(this, 'Le numéro de téléphone n\'est pas valide.');
                    }
                    break;
                case 'contact_form[subject]':
                    if (value && value.length < 5) {
                        showError(this, 'Le sujet doit avoir au moins 5 caractères.');
                    }
                    break;
            }
        });

        // Vérifie le champ message
        const message = $("textarea[name='contact_form[message]']").val();
        if (message && message.length < 10) {
            showError("textarea[name='contact_form[message]']", 'Le message doit avoir au moins 10 caractères.');
        }

        if (errors) {
            event.preventDefault();
        }
    });
});
