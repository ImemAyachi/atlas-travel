// DOM Elements
document.addEventListener('DOMContentLoaded', function() {
    // Navbar elements
    const navbar = document.querySelector('.navbar');
    const backToTop = document.querySelector('.back-to-top');
    
    // Search tabs functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    // Navbar scroll effect - simplified to just add shadow on scroll
    window.addEventListener('scroll', function() {
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        // Back to top button visibility
        if (window.scrollY > 300) {
            backToTop.classList.add('show');
        } else {
            backToTop.classList.remove('show');
        }
    });
    
    // Back to top button click
    if (backToTop) {
        backToTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Mobile menu toggle with Bootstrap 5 compatibility
    const menuToggle = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const expanded = menuToggle.getAttribute('aria-expanded') === 'true' || false;
            menuToggle.setAttribute('aria-expanded', !expanded);
            navbarCollapse.classList.toggle('show');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!menuToggle.contains(e.target) && 
                !navbarCollapse.contains(e.target) && 
                navbarCollapse.classList.contains('show')) {
                navbarCollapse.classList.remove('show');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Close menu when clicking on nav-link
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (navbarCollapse.classList.contains('show')) {
                    navbarCollapse.classList.remove('show');
                    menuToggle.setAttribute('aria-expanded', 'false');
                }
            });
        });
    }
    
    // Active nav link handling
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Search tab functionality
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabId = button.getAttribute('data-tab');
            
            // Remove active class from all buttons and tabs
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(tab => tab.classList.remove('active'));
            
            // Add active class to current button and tab
            button.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Initialize date pickers if available
    const datePickers = document.querySelectorAll('.datepicker');
    if (datePickers.length > 0 && typeof flatpickr !== 'undefined') {
        datePickers.forEach(picker => {
            flatpickr(picker, {
                minDate: "today",
                dateFormat: "Y-m-d",
            });
        });
    }
    
    // Search form validation
    const searchForms = document.querySelectorAll('.search-form');
    searchForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(form);
            const formValues = Object.fromEntries(formData.entries());
            
            // Basic validation
            let isValid = true;
            for (const [key, value] of Object.entries(formValues)) {
                if (!value && form.elements[key].required) {
                    isValid = false;
                    form.elements[key].classList.add('is-invalid');
                } else {
                    form.elements[key].classList.remove('is-invalid');
                }
            }
            
            // If valid, you can proceed with the search (e.g., redirect to search results page)
            if (isValid) {
                // For demo purposes, show an alert
                alert('Search submitted! In a real application, this would navigate to search results.');
                // Uncomment to actually submit the form
                // form.submit();
            }
        });
    });
    
    // Newsletter form submission
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = newsletterForm.querySelector('input[type="email"]');
            const email = emailInput.value.trim();
            
            // Basic email validation
            if (email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                // Success - in a real app, you'd submit this to your server
                alert('Thank you for subscribing to our newsletter!');
                emailInput.value = '';
            } else {
                emailInput.classList.add('is-invalid');
                setTimeout(() => {
                    emailInput.classList.remove('is-invalid');
                }, 3000);
            }
        });
    }
    
    // Add simple animation for destination cards
    const animateOnScroll = function() {
        const cards = document.querySelectorAll('.destination-card, .package-card, .testimonial-card');
        
        cards.forEach(card => {
            const cardPosition = card.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.3;
            
            if (cardPosition < screenPosition) {
                card.classList.add('animated');
            }
        });
    };
    
    // Run once on page load
    animateOnScroll();
    
    // Run on scroll
    window.addEventListener('scroll', animateOnScroll);
}); 