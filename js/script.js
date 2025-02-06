document.addEventListener("DOMContentLoaded", function () {
    const carousel = document.querySelector(".carousel");
    const prevBtn = document.querySelector(".prev");
    const nextBtn = document.querySelector(".next");
    let scrollAmount = 0;

    nextBtn.addEventListener("click", function () {
        scrollAmount += 220; // Ajuste o valor conforme necessário
        carousel.style.transform = `translateX(-${scrollAmount}px)`;
    });

    prevBtn.addEventListener("click", function () {
        scrollAmount -= 220;
        if (scrollAmount < 0) scrollAmount = 0; // Impede rolagem excessiva
        carousel.style.transform = `translateX(-${scrollAmount}px)`;
    });
});