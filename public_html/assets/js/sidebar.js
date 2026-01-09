/**
 * SIDEBAR.JS - Funcionalidad sidebar
 */

document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initSidebarToggle();
    initSubmenuToggles();
    initMobileSidebar();
    restoreSidebarState();
});

// Inicializar sidebar
function initSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (!sidebar) return;

    // Agregar tooltips a items del sidebar
    addTooltips();

    // Marcar item activo según URL
    setActiveMenuItem();
}

// Toggle sidebar (expandir/colapsar)
function initSidebarToggle() {
    const toggleBtn = document.querySelector('.sidebar-toggle');
    const toggleBtnDashboard = document.querySelector('.toggle-sidebar-btn');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleSidebar);
    }

    if (toggleBtnDashboard) {
        toggleBtnDashboard.addEventListener('click', toggleSidebar);
    }
}

function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    if (!sidebar) return;

    sidebar.classList.toggle('collapsed');

    if (mainContent) {
        mainContent.classList.toggle('sidebar-collapsed');
    }

    // Guardar estado
    const isCollapsed = sidebar.classList.contains('collapsed');
    localStorage.setItem('sidebarCollapsed', isCollapsed);

    // Trigger resize event para gráficos
    window.dispatchEvent(new Event('resize'));
}

// Restaurar estado del sidebar
function restoreSidebarState() {
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

    if (isCollapsed) {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');

        if (sidebar) sidebar.classList.add('collapsed');
        if (mainContent) mainContent.classList.add('sidebar-collapsed');
    }
}

// Submenús
function initSubmenuToggles() {
    const menuItemsWithSubmenu = document.querySelectorAll('.nav-item.has-submenu');

    menuItemsWithSubmenu.forEach(item => {
        const link = item.querySelector('.nav-link');

        if (link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                toggleSubmenu(item);
            });
        }
    });
}

function toggleSubmenu(item) {
    const sidebar = document.querySelector('.sidebar');

    // Si el sidebar está colapsado, no hacer nada
    if (sidebar && sidebar.classList.contains('collapsed')) {
        return;
    }

    // Cerrar otros submenús
    const openItems = document.querySelectorAll('.nav-item.open');
    openItems.forEach(openItem => {
        if (openItem !== item) {
            openItem.classList.remove('open');
        }
    });

    // Toggle este submenu
    item.classList.toggle('open');

    // Guardar estado
    const isOpen = item.classList.contains('open');
    const itemId = item.dataset.menuId;
    if (itemId) {
        localStorage.setItem(`submenu_${itemId}`, isOpen);
    }
}

// Marcar item activo
function setActiveMenuItem() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(link => {
        const href = link.getAttribute('href');

        if (href && currentPath.includes(href)) {
            link.classList.add('active');

            // Si está en un submenu, abrir el padre
            const parentItem = link.closest('.nav-item.has-submenu');
            if (parentItem) {
                parentItem.classList.add('open');
            }
        }
    });
}

// Tooltips para sidebar colapsado
function addTooltips() {
    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(link => {
        const text = link.querySelector('.nav-text');
        if (text) {
            link.setAttribute('data-tooltip', text.textContent.trim());
        }
    });
}

// Sidebar mobile
function initMobileSidebar() {
    const mobileToggle = document.querySelector('.mobile-sidebar-toggle');
    const overlay = document.querySelector('.sidebar-overlay');
    const sidebar = document.querySelector('.sidebar');

    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            toggleMobileSidebar();
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function() {
            closeMobileSidebar();
        });
    }

    // Cerrar sidebar mobile al hacer click en un link
    if (window.innerWidth <= 992) {
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Si no tiene submenu, cerrar sidebar
                if (!this.parentElement.classList.contains('has-submenu')) {
                    closeMobileSidebar();
                }
            });
        });
    }
}

function toggleMobileSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (sidebar) sidebar.classList.toggle('active');
    if (overlay) overlay.classList.toggle('active');

    // Prevenir scroll del body
    if (sidebar && sidebar.classList.contains('active')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
}

function closeMobileSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (sidebar) sidebar.classList.remove('active');
    if (overlay) overlay.classList.remove('active');

    document.body.style.overflow = '';
}

// Resize handler
let resizeTimeout;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(function() {
        if (window.innerWidth > 992) {
            closeMobileSidebar();
        }
    }, 250);
});

// Búsqueda en sidebar
function initSidebarSearch() {
    const searchInput = document.querySelector('.sidebar-search');

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            filterSidebarItems(query);
        });
    }
}

function filterSidebarItems(query) {
    const navItems = document.querySelectorAll('.nav-item');

    navItems.forEach(item => {
        const text = item.querySelector('.nav-text');

        if (text) {
            const itemText = text.textContent.toLowerCase();

            if (itemText.includes(query)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        }
    });
}

// Highlight activo
function highlightActiveModule() {
    const currentModule = getCurrentModule();

    if (currentModule) {
        const moduleSection = document.querySelector(`[data-module="${currentModule}"]`);
        if (moduleSection) {
            moduleSection.classList.add('active-module');
        }
    }
}

function getCurrentModule() {
    const path = window.location.pathname;
    const parts = path.split('/').filter(Boolean);
    return parts[0] || null;
}

// Export functions
window.Sidebar = {
    toggle: toggleSidebar,
    toggleMobile: toggleMobileSidebar,
    close: closeMobileSidebar
};
