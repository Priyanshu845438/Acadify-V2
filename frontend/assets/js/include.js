/**
 * include.js
 * Dynamically loads HTML components into pages using [include-html] attributes
 */
document.addEventListener("DOMContentLoaded", () => {
  const includes = document.querySelectorAll("[include-html]");
  includes.forEach(el => {
    const file = el.getAttribute("include-html");
    if (file) {
      fetch(file)
        .then(response => {
          if (!response.ok) throw new Error(`Error loading ${file}`);
          return response.text();
        })
        .then(data => {
          el.innerHTML = data;
        })
        .catch(err => {
          el.innerHTML = `<p style="color:red;">Component not found: ${file}</p>`;
          console.error(err);
        });
    }
  });
});
