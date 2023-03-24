let links = document.querySelectorAll("[data-delete]");

for (let link of links) {
    link.addEventListener("click", function (e) {
        e.preventDefault();

        if (confirm("Voulez-vous supprimer votre compte cette action est irréversible ?")) {
            fetch(this.getAttribute("href"), {
                method: "DELETE",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ "_token": this.dataset.token })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.parentElement.remove();

                    // Afficher le message flash
                    alert(data.message);

                    // Déconnexion de l'utilisateur et redirection vers la page d'accueil
                    fetch('/logout', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            "Content-Type": "application/json"
                        }
                    })
                    .then(() => {
                        window.location.href = '/';
                    })
                    .catch(error => {
                        console.error('Erreur lors de la déconnexion:', error);
                    });

                } else {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Erreur lors de la suppression:', error);
            });
        }
    });
}
