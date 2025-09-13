// Fade in nav links after load
window.addEventListener('DOMContentLoaded', () => {
  const navbar = document.querySelector('.wow-navbar');
  navbar.classList.add('loaded');
});

// Shrink navbar on scroll
window.addEventListener('scroll', () => {
  const navbar = document.querySelector('.wow-navbar');
  if (window.scrollY > 50) {
    navbar.classList.add('shrink');
  } else {
    navbar.classList.remove('shrink');
  }
});
