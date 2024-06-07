document.addEventListener("DOMContentLoaded", function() {
    // Get the current URL path
    var currentPath = window.location.pathname;

    // Get all nav links
    var links = document.querySelectorAll('.nav-link');

    // Loop through each nav link
    links.forEach(function(link) {
        // Check if the link's href contains "game", "profile", or "leaderboard"
        if (currentPath.includes("game") && link.textContent.toLowerCase() === "game") {
            link.classList.add('active'); // Add active class to "Game" link
        } else if (currentPath.includes("profile") && link.textContent.toLowerCase() === "profile") {
            link.classList.add('active'); // Add active class to "Profile" link
        } else if (currentPath.includes("leaderboard") && link.textContent.toLowerCase() === "leaderboard") {
            link.classList.add('active'); // Add active class to "Leaderboard" link
        } else if (currentPath.includes("login") && link.textContent.toLowerCase() === "login") {
            link.classList.add('active'); // Add active class to "Leaderboard" link
        }

        // Add click event listener to each nav link
        link.addEventListener('click', function(event) {
            // Remove active class from all nav links
            links.forEach(function(navLink) {
                navLink.classList.remove('active');
            });

            // Add active class to the clicked nav link
            this.classList.add('active');
        });
    });
});