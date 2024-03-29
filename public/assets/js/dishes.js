let productLinks = document.querySelectorAll("[data-delete][data-type='product']");

for (let link of productLinks) {
    link.addEventListener("click", function (e) {
        e.preventDefault();

        if (confirm("Voulez-vous supprimer ce produit ?")) {
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
                    // Redirection vers la route 'admin_dishes_index'
                    window.location.href = '/admin/dishes';
                } else if (data.error === "Token invalide") {
                    alert("Token invalide, impossible de supprimer le produit.");
                } else {
                    alert("Vous n'avez pas les droits nécessaires pour supprimer ce produit.");
                }
            })
            .catch(error => {
                console.error('Erreur lors de la suppression:', error);
            });
        }
    });
}