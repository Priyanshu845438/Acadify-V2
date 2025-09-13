/**
 * ui.js
 * Handles small UI interactions (modals, alerts, toggles, etc.)
 */

// Example: Auto-dismiss alerts after 5 seconds
document.addEventListener("DOMContentLoaded", () => {
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.classList.remove("show");
    }, 5000);
  });
});

// Example: Sidebar toggle
const sidebarToggle = document.getElementById("sidebarToggle");
if (sidebarToggle) {
  sidebarToggle.addEventListener("click", () => {
    document.body.classList.toggle("sidebar-collapsed");
  });
}
