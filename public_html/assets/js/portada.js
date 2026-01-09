/**
 * PORTADA.JS - Funcionalidad landing page
 */

document.addEventListener('DOMContentLoaded', function() {
    initHeaderScroll();
    initSmoothScroll();
    initMobileMenu();
});

// Header scroll effect
function initHeaderScroll() {
    const header = document.getElementById('header');

    if (!header) return;

    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
}

// Smooth scroll para navegación
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();

            const target = document.querySelector(this.getAttribute('href'));

            if (target) {
                const headerHeight = document.getElementById('header')?.offsetHeight || 0;
                const targetPosition = target.offsetTop - headerHeight;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// Mobile menu toggle
function initMobileMenu() {
    const toggle = document.getElementById('mobileMenuToggle');
    const menu = document.querySelector('.nav-menu');

    if (!toggle || !menu) return;

    toggle.addEventListener('click', function() {
        menu.classList.toggle('active');
    });

    // Cerrar al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!toggle.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.remove('active');
        }
    });
}
