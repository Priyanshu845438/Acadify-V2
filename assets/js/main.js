// Main JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    
    // --- Mobile Viewport Height Fix ---
    function setVhProperty() {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }
    
    setVhProperty();
    window.addEventListener('resize', setVhProperty);
    
    // --- Mobile Navigation Close on Link Click ---
    const navbarNav = document.querySelector('.navbar-nav');
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (navbarNav && navbarToggler && navbarCollapse) {
        navbarNav.addEventListener('click', function(e) {
            const link = e.target.closest('.nav-link:not(.dropdown-toggle)');
            if (link && window.innerWidth < 992) {
                // Close mobile menu when clicking a nav link
                const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                    toggle: false
                });
                bsCollapse.hide();
            }
        });
    }
    
    // --- Hero Section: Counter Animation ---
    const counter = document.getElementById('satisfaction-counter');
    if (counter) {
        function animateCounter() {
            let currentValue = 0;
            const targetValue = 98;
            const interval = setInterval(() => {
                if (currentValue >= targetValue) {
                    clearInterval(interval);
                } else {
                    currentValue++;
                    counter.textContent = currentValue + '%';
                }
            }, 20); // Adjust speed here
        }

        // Use Intersection Observer to trigger counter only when visible
        // Check for prefers-reduced-motion
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (prefersReducedMotion) {
                        // Skip animation, set final value immediately
                        counter.textContent = '98%';
                    } else {
                        animateCounter();
                    }
                    observer.unobserve(entry.target); // Animate only once
                }
            });
        }, { threshold: 0.5 });
        observer.observe(counter);
    }

    // --- Hero Section: Mouse Parallax Effect ---
    const heroSection = document.querySelector('.hero-section');
    const videoWrapper = document.querySelector('.hero-video-wrapper');
    if (heroSection && videoWrapper) {
        // Check for prefers-reduced-motion
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        
        if (!prefersReducedMotion) {
            heroSection.addEventListener('mousemove', function(e) {
                if (window.innerWidth > 992) { // Only apply on desktop
                    const { clientX, clientY } = e;
                    const { offsetWidth, offsetHeight } = heroSection;
                    
                    // Calculate movement amount (adjust divisor for more/less effect)
                    const xMovement = (clientX / offsetWidth - 0.5) * 20; 
                    const yMovement = (clientY / offsetHeight - 0.5) * 20;

                    videoWrapper.style.transform = `translate(${xMovement}px, ${yMovement}px)`;
                }
            });
        }
    }
    
    // Smooth scrolling for anchor links
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a[href^="#"]');
        if (link) {
            e.preventDefault();
            const targetId = link.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                const offsetTop = targetElement.offsetTop - 80; // Account for fixed navbar
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
                
                // Update active nav link
                updateActiveNavLink(link);
            }
        }
    });
    
    // Update active navigation link
    function updateActiveNavLink(clickedLink) {
        setTimeout(() => {
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            navLinks.forEach(link => link.classList.remove('active'));
            clickedLink.classList.add('active');
        }, 100);
    }
    
    // Navbar collapse on mobile after link click
    document.addEventListener('click', function(e) {
        const navLink = e.target.closest('.navbar-nav .nav-link');
        if (navLink && window.innerWidth < 992) {
            const navbarCollapse = document.querySelector('.navbar-collapse');
            if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                bsCollapse.hide();
            }
        }
    });
    
    // Newsletter subscription
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-testid="button-newsletter"]')) {
            const email = document.querySelector('[data-testid="input-newsletter"]').value;
            if (email) {
                console.log('Newsletter subscription:', email);
                alert('Thank you for subscribing!');
                document.querySelector('[data-testid="input-newsletter"]').value = '';
            } else {
                alert('Please enter a valid email address.');
            }
        }
    });
    
    // Button click handlers
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-testid="button-get-started"]')) {
            console.log('Get Started button clicked');
            alert('Welcome! Let\'s get started building your website.');
        }
    });
});

// Global functions for hero section buttons
function handleOurServices() {
    console.log('Our Services button clicked');
    window.location.href = '/pages/services.html';
}

function handleViewWork() {
    console.log('View Our Work button clicked');
    window.location.href = '/pages/insights/portfolio.html';
}

// Professional Case Study Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    const caseStudyModal = document.getElementById('caseStudyModal');
    
    if (caseStudyModal) {
        caseStudyModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            const button = event.relatedTarget;

            // Extract info from data-bs-* attributes
            const title = button.getAttribute('data-title');
            const image = button.getAttribute('data-image');
            const category = button.getAttribute('data-category');
            const challenge = button.getAttribute('data-challenge');
            const solution = button.getAttribute('data-solution');
            const results = button.getAttribute('data-results');

            // Update the modal's content with professional structure
            const modalProjectTitle = caseStudyModal.querySelector('#modal-project-title');
            const modalCategoryBadge = caseStudyModal.querySelector('#modal-category-badge');
            const modalImage = caseStudyModal.querySelector('#modal-image');
            const modalChallenge = caseStudyModal.querySelector('#modal-challenge');
            const modalSolution = caseStudyModal.querySelector('#modal-solution');
            const modalResults = caseStudyModal.querySelector('#modal-results');

            // Populate the professional modal elements
            if (modalProjectTitle) modalProjectTitle.textContent = title;
            if (modalCategoryBadge) modalCategoryBadge.textContent = category;
            if (modalImage) {
                modalImage.src = image;
                modalImage.alt = title + " Case Study";
            }
            if (modalChallenge) modalChallenge.textContent = challenge;
            if (modalSolution) modalSolution.textContent = solution;
            
            // Format results as professional metric cards
            if (modalResults && results) {
                if (results.includes('•')) {
                    const resultsArray = results.split('•').filter(item => item.trim());
                    modalResults.innerHTML = resultsArray.map(item => {
                        const trimmedItem = item.trim();
                        // Extract percentage or number if present
                        const match = trimmedItem.match(/(\d+%|\d+\+|\d+,?\d*)/);
                        const metric = match ? match[1] : '';
                        const description = trimmedItem.replace(metric, '').trim();
                        
                        return `
                            <div class="col-md-6 col-lg-4">
                                <div class="bg-light rounded-3 p-4 h-100 text-center border border-primary border-opacity-10">
                                    <div class="display-6 fw-bold text-primary mb-2">${metric || '✓'}</div>
                                    <p class="mb-0 small text-muted">${description || trimmedItem}</p>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    modalResults.innerHTML = `
                        <div class="col-12">
                            <div class="bg-light rounded-3 p-4 border border-primary border-opacity-10">
                                <p class="mb-0 text-muted">${results}</p>
                            </div>
                        </div>
                    `;
                }
            }
        });
    }

    // Back to Top Button Functionality
    const backToTopBtn = document.getElementById('backToTopBtn');
    
    if (backToTopBtn) {
        // Show/hide button on scroll
        window.addEventListener('scroll', function() {
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                backToTopBtn.style.display = 'block';
            } else {
                backToTopBtn.style.display = 'none';
            }
        });

        // Smooth scroll to top when clicked
        backToTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});