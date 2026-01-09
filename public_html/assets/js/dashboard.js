/**
 * DASHBOARD.JS - Funcionalidad dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
    initDashboard();
    initTrialCountdown();
    updateStats();
    initCharts();
});

// Inicializar dashboard
function initDashboard() {
    console.log('Dashboard inicializado');

    // Verificar sesión y permisos
    checkSession();

    // Cargar widgets
    loadWidgets();
}

// Verificar sesión
function checkSession() {
    // Esta función debería hacer una petición AJAX para verificar sesión
    // Por ahora solo log
    console.log('Sesión verificada');
}

// Trial countdown
function initTrialCountdown() {
    const trialBanner = document.querySelector('.trial-banner');
    if (!trialBanner) return;

    const trialDaysEl = document.querySelector('.trial-days');
    if (!trialDaysEl) return;

    // Obtener días restantes del data attribute o calcular
    const daysRemaining = parseInt(trialDaysEl.dataset.days || '14');

    updateTrialDisplay(daysRemaining);

    // Actualizar cada hora
    setInterval(() => {
        updateTrialDisplay(daysRemaining);
    }, 3600000);
}

function updateTrialDisplay(days) {
    const trialDaysEl = document.querySelector('.trial-days');
    const trialBanner = document.querySelector('.trial-banner');

    if (!trialDaysEl || !trialBanner) return;

    if (days <= 0) {
        trialBanner.style.background = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
        trialDaysEl.textContent = '¡Periodo finalizado!';
    } else if (days <= 3) {
        trialBanner.style.background = 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';
        trialDaysEl.textContent = days + ' día' + (days > 1 ? 's' : '');
    } else {
        trialDaysEl.textContent = days + ' días';
    }
}

// Actualizar estadísticas
function updateStats() {
    // Simulación de actualización de stats
    // En producción, esto haría peticiones AJAX a la API

    const stats = {
        ventas: {
            value: '$2,450,000',
            change: '+12.5%',
            positive: true
        },
        clientes: {
            value: '156',
            change: '+8',
            positive: true
        },
        productos: {
            value: '489',
            change: '+23',
            positive: true
        },
        pendientes: {
            value: '12',
            change: '-3',
            positive: true
        }
    };

    // Actualizar valores en el DOM
    updateStatCards(stats);
}

function updateStatCards(stats) {
    Object.keys(stats).forEach(key => {
        const card = document.querySelector(`[data-stat="${key}"]`);
        if (!card) return;

        const valueEl = card.querySelector('.stat-value');
        const changeEl = card.querySelector('.stat-change');

        if (valueEl) valueEl.textContent = stats[key].value;
        if (changeEl) {
            changeEl.textContent = stats[key].change;
            changeEl.className = 'stat-change ' + (stats[key].positive ? 'positive' : 'negative');
        }
    });
}

// Cargar widgets
function loadWidgets() {
    loadRecentActivity();
    loadQuickStats();
}

function loadRecentActivity() {
    // Simulación de actividad reciente
    const activities = [
        {
            icon: '📄',
            text: 'Nueva factura creada #1245',
            time: 'Hace 5 minutos'
        },
        {
            icon: '👤',
            text: 'Nuevo cliente registrado: Juan Pérez',
            time: 'Hace 1 hora'
        },
        {
            icon: '💰',
            text: 'Pago recibido $150.000',
            time: 'Hace 2 horas'
        },
        {
            icon: '📦',
            text: 'Producto actualizado: Laptop HP',
            time: 'Hace 3 horas'
        }
    ];

    const activityList = document.querySelector('.activity-list');
    if (!activityList) return;

    activityList.innerHTML = activities.map(activity => `
        <li class="activity-item">
            <div class="activity-icon">${activity.icon}</div>
            <div class="activity-content">
                <div class="activity-text">${activity.text}</div>
                <div class="activity-time">${activity.time}</div>
            </div>
        </li>
    `).join('');
}

function loadQuickStats() {
    console.log('Quick stats cargadas');
}

// Inicializar gráficos (placeholders para Chart.js u otra librería)
function initCharts() {
    console.log('Gráficos inicializados');

    // Aquí iría la inicialización de Chart.js o similar
    // Por ejemplo:
    // initSalesChart();
    // initRevenueChart();
}

// Language selector
const languageSelector = document.getElementById('languageSelector');
if (languageSelector) {
    languageSelector.addEventListener('change', function() {
        const language = this.value;
        changeLanguage(language);
    });
}

function changeLanguage(lang) {
    // Guardar preferencia
    localStorage.setItem('language', lang);

    // Hacer petición AJAX para cambiar idioma
    fetch('/api/change-language.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ language: lang })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recargar página para aplicar nuevo idioma
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error cambiando idioma:', error);
    });
}

// User menu dropdown
const userMenu = document.querySelector('.user-menu');
if (userMenu) {
    userMenu.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleUserMenu();
    });
}

function toggleUserMenu() {
    const dropdown = document.querySelector('.user-menu-dropdown');
    if (dropdown) {
        dropdown.classList.toggle('active');
    }
}

// Cerrar dropdowns al hacer click fuera
document.addEventListener('click', function() {
    const dropdown = document.querySelector('.user-menu-dropdown');
    if (dropdown) {
        dropdown.classList.remove('active');
    }
});

// Auto-refresh stats cada 5 minutos
setInterval(updateStats, 300000);

// Notificaciones en tiempo real (WebSocket placeholder)
function initRealtimeNotifications() {
    // Aquí iría la conexión WebSocket para notificaciones en tiempo real
    console.log('Sistema de notificaciones en tiempo real inicializado');
}

// Atajos de teclado
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K para búsqueda rápida
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        openQuickSearch();
    }

    // Ctrl/Cmd + N para nueva factura
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        createNewInvoice();
    }
});

function openQuickSearch() {
    console.log('Abrir búsqueda rápida');
    // Implementar modal de búsqueda
}

function createNewInvoice() {
    console.log('Crear nueva factura');
    window.location.href = '/ventas/facturas/nueva';
}

// Export functions para uso externo
window.Dashboard = {
    updateStats,
    loadRecentActivity,
    changeLanguage
};
