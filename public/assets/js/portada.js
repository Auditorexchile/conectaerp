/**
 * PORTADA - CONECTA ERP
 * JavaScript para header con efecto scroll y navegación suave
 */

(function() {
    'use strict';

    // ===== HEADER CON EFECTO SCROLL =====
    const header = document.getElementById('header');

    function handleScroll() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }

    // Detectar scroll
    window.addEventListener('scroll', handleScroll);

    // ===== NAVEGACIÓN SUAVE =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            // Solo aplicar a anchors internos
            if (href !== '#' && href.startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(href);

                if (target) {
                    const headerHeight = header.offsetHeight;
                    const targetPosition = target.offsetTop - headerHeight;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // ===== ANIMACIÓN DE APARICIÓN AL SCROLL =====
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);

    // Observar elementos que queremos animar
    const elementsToAnimate = document.querySelectorAll('.benefit-card, .plan-card');
    elementsToAnimate.forEach(el => observer.observe(el));

})();
