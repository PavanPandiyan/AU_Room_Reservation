document.addEventListener("DOMContentLoaded", function () {
    // Get all sidebar links
    let links = document.querySelectorAll(".sidenav a");

    // Check if the current page URL matches any link href
    links.forEach(link => {
        if (link.href === window.location.href) {
            link.classList.add("active"); // Add active class to the current link
        }

        // Add click event to set active class when clicked
        link.addEventListener("click", function () {
            links.forEach(l => l.classList.remove("active")); // Remove active from all
            this.classList.add("active"); // Add active to clicked link
        });
    });
});
