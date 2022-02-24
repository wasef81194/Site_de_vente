//fonction d'afficher les données d'une cartes 
window.onload = () => {
    // Gestion des boutons "Supprimer"
    let cards = document.querySelectorAll("[data-show-card]")
    let href = document.getElementsByClassName("href")
    
    // On boucle sur links
    for(card of cards){
        // On écoute le clic
        card.addEventListener("click", function(){
            console.log('click',this.dataset.id);
            // On demande confirmation
                // On envoie une requête Ajax vers le href du lien avec la méthode DELETE
                fetch(this.getAttribute("href"), {
                    method: "GET",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                }).then(
                    window.location.href = this.getAttribute("href")
                ).catch(e => alert(e))
            //console.log(data);
        })
    }
}