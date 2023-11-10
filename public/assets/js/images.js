let imageLinks = document.querySelectorAll("[data-delete][data-type='image']");


// On boucle sur les liens
for(let link of imageLinks){
    //On met un écouteur d'évènements
    link.addEventListener("click", function(e){
        e.preventDefault();

        //On demande confirmation
        if(confirm("Voulez-vous supprimer cette image ?")){
            //On envoie la requete ajax
            fetch(this.getAttribute("href"), {
                method: "DELETE",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({"_token": this.dataset.token})
            }).then(response => response.json())
            .then(data =>{
                if(data.success){
                    this.parentElement.remove();
                } else if (data.error === "Token invalide") {
                    alert("Token invalide, impossible de supprimer l'image'.");
                } else {
                    alert("Vous n'avez pas les droits nécessaires pour supprimer cette image.");
                }
            })
            .catch(error => {
                console.error('Erreur lors de la suppression:', error);
            });
        }
    });
}