// Core JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Header scroll effect
    const header = document.getElementById('header');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) {
                header.classList.remove('transparent');
            } else {
                header.classList.add('transparent');
            }
        });
    }
});
