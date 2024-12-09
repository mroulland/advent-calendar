document.addEventListener("DOMContentLoaded", () => {
  const snowflakes = document.querySelectorAll(".snowflake");

  snowflakes.forEach(snowflake => {
    // Position horizontale aléatoire
    const randomX = Math.floor(Math.random() * 100); // Entre 0% et 100%
    const randomDuration = Math.random() * (20 - 5) + 5; // Entre 3s et 5s pour la durée

    // Appliquer ces valeurs via JavaScript
    snowflake.style.left = `${randomX}%`; // Appliquer position X aléatoire
    snowflake.style.animationDuration = `${randomDuration}s`; // Appliquer durée d'animation aléatoire
  });
});
