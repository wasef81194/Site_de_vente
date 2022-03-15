//fonction d'afficher les données d'une cartes 

// Gestion des boutons "Supprimer"
let cards = document.querySelectorAll("[data-show-card]")
    
// On boucle sur links
for(card of cards){
    // On écoute le clic
    card.addEventListener("click", function(){
        console.log(card,this)
        window.location.href = this.getAttribute("href")
    })
}
