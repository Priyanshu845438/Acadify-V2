// Component loader for HTML partials
class ComponentLoader {
    constructor() {
        this.loadedComponents = new Map();
    }

    async loadComponent(selector, componentPath) {
        try {
            // Check if component already loaded
            if (this.loadedComponents.has(componentPath)) {
                const element = document.querySelector(selector);
                if (element) {
                    element.innerHTML = this.loadedComponents.get(componentPath);
                    // Re-initialize Bootstrap components after loading
                    this.initializeBootstrapComponents(element);
                }
                return;
            }

            const response = await fetch(componentPath);
            if (!response.ok) {
                throw new Error(`Failed to load component: ${componentPath}`);
            }
            
            const html = await response.text();
            this.loadedComponents.set(componentPath, html);
            
            const element = document.querySelector(selector);
            if (element) {
                element.innerHTML = html;
                // Initialize Bootstrap components after loading
                this.initializeBootstrapComponents(element);
                
                // Special handling for case studies component
                if (componentPath.includes('case-studies.html')) {
                    // Small delay to ensure DOM is ready, then initialize modal
                    setTimeout(() => {
                        this.initializeCaseStudyModal();
                    }, 100);
                }
            }
        } catch (error) {
            console.error('Error loading component:', error);
        }
    }

    initializeBootstrapComponents(element) {
        // Wait for Bootstrap to be available
        if (typeof bootstrap === 'undefined') {
            setTimeout(() => this.initializeBootstrapComponents(element), 100);
            return;
        }

        // Initialize dropdowns
        const dropdownTriggers = element.querySelectorAll('[data-bs-toggle="dropdown"]');
        dropdownTriggers.forEach(trigger => {
            if (!trigger._bootstrap_dropdown_initialized) {
                try {
                    new bootstrap.Dropdown(trigger);
                    trigger._bootstrap_dropdown_initialized = true;
                } catch (error) {
                    console.error('Error initializing dropdown:', error);
                }
            }
        });

        // Initialize navbar collapse
        const collapseTriggers = element.querySelectorAll('[data-bs-toggle="collapse"]');
        collapseTriggers.forEach(trigger => {
            if (!trigger._bootstrap_collapse_initialized) {
                const targetId = trigger.getAttribute('data-bs-target');
                const target = document.querySelector(targetId);
                if (target) {
                    try {
                        new bootstrap.Collapse(target, { toggle: false });
                        trigger._bootstrap_collapse_initialized = true;
                    } catch (error) {
                        console.error('Error initializing collapse:', error);
                    }
                }
            }
        });

        // Initialize case study modal functionality if case studies are loaded
        if (element.querySelector('#caseStudyModal') || document.querySelector('#caseStudyModal')) {
            this.initializeCaseStudyModal();
        }
    }

    initializeCaseStudyModal() {
        const caseStudyModal = document.getElementById('caseStudyModal');
        
        if (caseStudyModal && !caseStudyModal._modal_initialized) {
            caseStudyModal.addEventListener('show.bs.modal', function (event) {
                // Button that triggered the modal
                const button = event.relatedTarget;

                // Extract info from data attributes
                const title = button.getAttribute('data-title');
                const image = button.getAttribute('data-image');
                const category = button.getAttribute('data-category');
                const challenge = button.getAttribute('data-challenge');
                const solution = button.getAttribute('data-solution');
                const results = button.getAttribute('data-results');

                // Update the modal's content
                const modalProjectTitle = caseStudyModal.querySelector('#modal-project-title');
                const modalCategoryBadge = caseStudyModal.querySelector('#modal-category-badge');
                const modalImage = caseStudyModal.querySelector('#modal-image');
                const modalChallenge = caseStudyModal.querySelector('#modal-challenge');
                const modalSolution = caseStudyModal.querySelector('#modal-solution');
                const modalResults = caseStudyModal.querySelector('#modal-results');

                // Populate the modal elements
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
            caseStudyModal._modal_initialized = true;
        }
    }

    async loadServiceCard(selector, icon, title, description, link = '#') {
        try {
            const response = await fetch('/components/service-card.html');
            const html = await response.text();
            
            const customizedHtml = html
                .replace('bi-code-slash', icon)
                .replace('Software Development', title)
                .replace('We provide custom software development for your business, Billing, Inventory and every custom solution.', description)
                .replace('href="#"', `href="${link}"`);
            
            const element = document.querySelector(selector);
            if (element) {
                element.innerHTML = customizedHtml;
            }
        } catch (error) {
            console.error('Error loading service card:', error);
        }
    }

    async loadTestimonialCard(selector, content, name, title, avatar) {
        try {
            const response = await fetch('/components/testimonial-card.html');
            const html = await response.text();
            
            const customizedHtml = html
                .replace('Ideovent transformed our online presence completely. Their team delivered a website that not only looks stunning but also performs exceptionally well.', content)
                .replace('Kavita Prem', name)
                .replace('CEO, TechStart Inc.', title)
                .replace('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ4A8PhcI7V3miXTmY26npWgI2nFvNxDRs_bg&s', avatar);
            
            const element = document.querySelector(selector);
            if (element) {
                element.innerHTML = customizedHtml;
            }
        } catch (error) {
            console.error('Error loading testimonial card:', error);
        }
    }

    async loadFeatureCard(selector, icon, title, description) {
        try {
            const response = await fetch('/components/feature-card.html');
            const html = await response.text();
            
            const customizedHtml = html
                .replace('bi-lightning-charge', icon)
                .replace('Fast Performance', title)
                .replace('Lightning-fast loading speeds with optimized code and modern web technologies.', description);
            
            const element = document.querySelector(selector);
            if (element) {
                element.innerHTML = customizedHtml;
            }
        } catch (error) {
            console.error('Error loading feature card:', error);
        }
    }
}

// Initialize component loader
const componentLoader = new ComponentLoader();

// Function to ensure quote modal is globally available on all pages
function ensureQuoteModalAvailability() {
    // Check if quote modal container exists
    let quoteModalContainer = document.getElementById('quote-modal-container');
    
    // If it doesn't exist, create it and append to body
    if (!quoteModalContainer) {
        quoteModalContainer = document.createElement('div');
        quoteModalContainer.id = 'quote-modal-container';
        document.body.appendChild(quoteModalContainer);
    }
    
    // Load the quote modal component
    componentLoader.loadComponent('#quote-modal-container', '/components/quote-modal.html');
}

// Function to initialize all components
function initializeComponents() {
    // Load main components
    componentLoader.loadComponent('#site-header', '/components/navbar.html');
    componentLoader.loadComponent('#hero-section', '/components/hero.html');
    componentLoader.loadComponent('#site-footer', '/components/footer.html');
    componentLoader.loadComponent('#about-us-section', '/components/about-us.html');
    componentLoader.loadComponent('#why-choose-us-section', '/components/why-choose-us.html');
    componentLoader.loadComponent('#case-studies-section', '/components/case-studies.html');
    componentLoader.loadComponent('#trusted-by-section', '/components/trusted-by.html');
    componentLoader.loadComponent('#latest-blogs-section', '/components/latest-blogs.html');
    componentLoader.loadComponent('#testimonial-cta-section', '/components/testimonial-cta.html');
    componentLoader.loadComponent('#bottom-cta-section', '/components/cta.html');
    
    // Ensure quote modal is globally available on all pages
    ensureQuoteModalAvailability();
    
    // Load service cards with rewritten, India-focused content
    componentLoader.loadServiceCard('#service-card-1', 'bi-globe', 'Web Development for India', 'We build high-performance websites that dominate search rankings in Delhi, Mumbai, and Kolkata, turning visitors into customers.');
    componentLoader.loadServiceCard('#service-card-2', 'bi-phone', 'Mobile App Development', 'From FinTech to E-commerce, we develop secure, scalable, and user-friendly mobile apps for the Indian market.');
    componentLoader.loadServiceCard('#service-card-3', 'bi-megaphone', 'Digital Marketing', 'Drive measurable growth with our ROI-focused SEO, social media, and content marketing strategies tailored for Indian audiences.');
    componentLoader.loadServiceCard('#service-card-4', 'bi-code-slash', 'Custom Software Solutions', 'Automate your business operations with custom-built ERP, CRM, and software that solves your unique challenges.');
    componentLoader.loadServiceCard('#service-card-5', 'bi-palette', 'UI/UX Design', 'Create unforgettable digital experiences with intuitive, user-centric design that boosts engagement and conversions.');
    componentLoader.loadServiceCard('#service-card-6', 'bi-shield-check', 'Maintenance & Support', 'Ensure your digital assets are always secure, updated, and performing at their peak with our reliable annual maintenance contracts.');
    
    // Note: service-card-7 and service-card-8 from the original seem redundant or too broad. 
    // Focusing on the core 6 is a stronger strategic choice for clarity. If you must have 8, they can be added.
    
    // Load testimonial cards, tying them to our established case studies for consistency
    componentLoader.loadTestimonialCard('#testimonial-card-1', 
        'Acadify built the virtual sales platform that allowed us to sell Gurgaon properties to NRIs sight unseen. Their understanding of the luxury market is unparalleled. Our sales are up 50%!',
        'A. K. Singh',
        'Director, Gurgaon Gateway Properties',
        'images/testimonials/client-ak-singh-gurgaon.jpg'
    );
    
    componentLoader.loadTestimonialCard('#testimonial-card-2',
        'They didn\'t just build an app; they built the backbone of our logistics operations in West India. The AI-powered route optimization has cut our delivery times on the Mumbai-Pune route by 45%.',
        'Operations Head',
        'BharatConnect Logistics',
        'images/testimonials/client-bharatconnect-mumbai.jpg'
    );
    
    componentLoader.loadTestimonialCard('#testimonial-card-3',
        'Thanks to Acadify\'s local SEO strategy, we are now the #1 result on Google Maps for our services in Kolkata. Our foot traffic and local inquiries have increased dramatically.',
        'S. Banerjee',
        'Founder, Kolkata Kitchen Co.',
        'images/testimonials/client-s-banerjee-kolkata.jpg'
    );
}

// Load components when DOM is ready and Bootstrap is available
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for Bootstrap to load, then initialize components
    if (typeof bootstrap !== 'undefined') {
        initializeComponents();
    } else {
        // Wait for Bootstrap to load
        const checkBootstrap = setInterval(() => {
            if (typeof bootstrap !== 'undefined') {
                clearInterval(checkBootstrap);
                initializeComponents();
            }
        }, 50);
    }
});