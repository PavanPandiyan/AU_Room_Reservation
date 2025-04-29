/* Add this to js/header.js */
document.addEventListener('DOMContentLoaded', (event) => {
    const bookNowBtn = document.getElementById('bookNowBtn');
    const modal = document.getElementById('loginModal');
    const closeBtn = document.querySelector('.close-btn');

    if (bookNowBtn) {
        bookNowBtn.onclick = function(event) {
            event.preventDefault(); // Prevent default anchor behavior
            modal.style.display = "block";
        }
    }

    if (closeBtn) {
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});
