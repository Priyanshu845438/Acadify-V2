/**
 * forms.js
 * Bootstrap form validation & simple handling
 */
document.addEventListener("DOMContentLoaded", () => {
  const forms = document.querySelectorAll("form");

  forms.forEach(form => {
    form.addEventListener("submit", event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }

      form.classList.add("was-validated");

      // Example: Handle contact form (custom behavior)
      if (form.id === "contactForm" && form.checkValidity()) {
        event.preventDefault();
        alert("✅ Message sent successfully!");
        form.reset();
        form.classList.remove("was-validated");
      }

      // Example: Handle login form
      if (form.id === "loginForm" && form.checkValidity()) {
        event.preventDefault();
        alert("🔐 Login successful (dummy placeholder)!");
      }
    });
  });
});
