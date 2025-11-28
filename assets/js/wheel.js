import * as d3 from 'd3';

document.addEventListener("DOMContentLoaded", () => {

    const rewards = window.WHEEL_REWARDS || [];
    
    const colors = ['#d9f5e4', '#2e7d4f', '#da1600', '#ff4d3f', '#f1b846', '#f27c2d', '#f5f3e7', '#f1b846'];

    const width = 400, height = 400;
    const radius = Math.min(width, height) / 2;

    const svg = d3
        .select("#wheel")
        .attr("width", width)
        .attr("height", height)
        .append("g")
        .attr("transform", `translate(${width / 2}, ${height / 2})`);

    const pieGenerator = d3.pie().value(() => 1);
    const arcGenerator = d3.arc().innerRadius(0).outerRadius(radius);

    const arcs = svg
        .selectAll("arc")
        .data(pieGenerator(rewards))
        .enter()
        .append("g");

    // ---- Couleurs des segments ----
    arcs.append("path")
        .attr("d", arcGenerator)
        .attr("fill", (d, i) => colors[i % colors.length])
        .attr("stroke", "#fff")
        .attr("stroke-width", "2px");

    // ---- Numéros vers l'extérieur ----
    arcs.append("text")
        .attr("transform", d => {
            const [x, y] = arcGenerator.centroid(d);
            return `translate(${x * 1.4}, ${y * 1.4})`;
        })
        .attr("text-anchor", "middle")
        .attr("dominant-baseline", "middle")
        .attr("font-size", "20px")
        .attr("fill", "#333")
        .text(d => d.data);

    // ---- Flèche rouge ----
    d3.select("#wheel")
        .append("polygon")
        .attr("points", "200,5 190,30 210,30")
        .attr("fill", "red");

    const spinButton = document.getElementById("spin");
    const form = document.querySelector("form");
    const rewardInput = document.querySelector("#wheel_rewards");

    // ---- Sécurité ----
    if (!form || !rewardInput) {
        console.error("Impossible de trouver le formulaire ou le champ reward.");
        return;
    }
    let currentRotation = 0;
    // ---- Animation de rotation ----
    spinButton.addEventListener("click", () => {

        spinButton.disabled = true;

        // 🎯 1) Choisir un segment rééllement au hasard
        const randomIndex = Math.floor(Math.random() * rewards.length);

        // 🎯 2) Calculer l’angle du segment choisi
        const segmentAngle = 360 / rewards.length;
        const targetAngle = (rewards.length - randomIndex) * segmentAngle - (segmentAngle / 2);
        
        // 🎯 3) Ajouter plusieurs tours complets pour l’animation
        const extraTurns = 360 * (4 + Math.floor(Math.random() * 3)); // entre 4 et 6 tours
        const finalRotation = currentRotation + extraTurns + targetAngle;

        svg.transition()
            .duration(4000)
            .ease(d3.easeCubicOut)
            .attrTween("transform", () =>
                d3.interpolateString(
                    `translate(${width / 2}, ${height / 2}) rotate(0)`,
                    `translate(${width / 2}, ${height / 2}) rotate(${finalRotation})`
                )
            )
            .on("end", () => {

                // ---- Calcul de la case gagnante ----
                const angle = finalRotation % 360;
                const segmentAngle = 360 / rewards.length;

                // On inverse le sens pour correspondre à D3 (qui part du haut)
                const selectedIndex = Math.floor((rewards.length - (angle / segmentAngle)) % rewards.length);

                const selectedReward = rewards[selectedIndex];
                console.log("Case gagnante :", selectedReward);

                // ---- Remplir le formulaire ----
                rewardInput.value = selectedReward;

                // ---- Attendre quelques secondes avant d'envoyer ----
                setTimeout(() => {
                    form.submit();
                }, 2000); // 2 sec (change si tu veux)

            });
    });
});