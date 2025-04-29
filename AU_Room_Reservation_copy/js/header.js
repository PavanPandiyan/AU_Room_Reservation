document.addEventListener("DOMContentLoaded", function () {
  // ✅ Toggle Login Options
  const loginBtn = document.getElementById("loginBtn");
  const loginOptions = document.getElementById("loginOptions");

  if (loginBtn && loginOptions) {
    loginOptions.style.display = "none";

    loginBtn.addEventListener("click", function (event) {
      event.stopPropagation();
      loginOptions.style.display = loginOptions.style.display === "none" ? "block" : "none";
    });

    document.addEventListener("click", function (event) {
      if (!loginBtn.contains(event.target) && !loginOptions.contains(event.target)) {
        loginOptions.style.display = "none";
      }
    });
  }

  // ✅ Handle Mobile Menu Toggle
  window.toggleMenu = function () {
    const navLinks = document.getElementById("nav-links");
    if (navLinks) {
      navLinks.classList.toggle("open");
    }
  };
});
