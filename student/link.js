
const occupiedCards = document.querySelectorAll(".parking--card.occupied");
const bookedCards = document.querySelectorAll(".parking--card.booked")

//  Go through each occupied card one by one
occupiedCards.forEach(function (card) {
    //  Find the "Book now" link inside this card
    const bookLink = card.querySelector(".book");

    //  Stop the link from working when clicked
    bookLink.addEventListener("click", function (event) {
        event.preventDefault();
    });

    // 5. Change how the link looks and what it says
    bookLink.textContent = "Not available";
    bookLink.style.opacity = "0.5";
    bookLink.style.cursor = "not-allowed";
});
bookedCards.forEach(function (card){
    const link = card.querySelector(".book");

    link.addEventListener("click", function (event){
        event.preventDefault();
    });

    link.textContent = "Not available";
    link.style.opacity ="0.5";
    link.style.cursor = "not-allowed";
});