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
    
    // Load service cards with different content
    componentLoader.loadServiceCard('#service-card-1', 'bi-code-slash', 'Software Development', 'We provide custom software development for your business, Billing, Inventory and every custom solution.');
    componentLoader.loadServiceCard('#service-card-2', 'bi-globe', 'Website Development', 'We provide website design & development to make your business online to drive more customers and sales.');
    componentLoader.loadServiceCard('#service-card-3', 'bi-phone', 'Mobile App Development', 'We provide Android & iOS app development. Turn your idea into reality and start your startup with us.');
    componentLoader.loadServiceCard('#service-card-4', 'bi-megaphone', 'Digital Marketing', 'We provide the best digital marketing services to drive more traffic on your mobile app or website.');
    componentLoader.loadServiceCard('#service-card-5', 'bi-server', 'Domain & Hosting', 'We provide domain, hosting and reliable servers to never let your business down.');
    componentLoader.loadServiceCard('#service-card-6', 'bi-diagram-3', 'ERP & CRM Development', 'We provide ERP & CRM to automate your business with fast execution and precise strategies.');
    componentLoader.loadServiceCard('#service-card-7', 'bi-tools', 'Maintenance Services', 'We provide annual maintenance services for websites, software, ERP, CRM & mobile apps.');
    componentLoader.loadServiceCard('#service-card-8', 'bi-plus-circle', 'Explore More Services', 'We provide IT training, internships, MLM software and many more services to discover.');
    
    // Load testimonial cards
    componentLoader.loadTestimonialCard('#testimonial-card-1', 
        'Ideovent transformed our online presence completely. Their team delivered a website that not only looks stunning but also performs exceptionally well. The attention to detail and user experience focus has resulted in a 40% increase in our conversion rate.',
        'Kavita Prem',
        'CEO, TechStart Inc.',
        'https://images.unsplash.com/photo-1494790108755-2616b612b786?auto=format&fit=crop&w=100&q=80'
    );
    
    componentLoader.loadTestimonialCard('#testimonial-card-2',
        'Working with Ideovent has been a game-changer for our e-commerce business. Their expertise in UI/UX design and development helped us create an intuitive shopping experience that our customers love.',
        'Ravi Raj',
        'Founder, StyleMart',
        'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=100&q=80'
    );
    
    componentLoader.loadTestimonialCard('#testimonial-card-3',
        'The SEO strategy developed by Ideovent has dramatically improved our online visibility. We\'re now ranking on the first page for our key terms, and the increased traffic has led to substantial growth.',
        'Ragini Sinha',
        'Marketing Director, GrowthBiz',
        'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=100&q=80'
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