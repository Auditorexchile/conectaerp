<?php
/**
 * CONECTA ERP - HEADER
 * Header principal del sistema con navegación, notificaciones y selector de idioma
 */

if (!defined('CONECTA_ERP')) {
    die('Acceso no autorizado');
}

// Obtener datos del usuario de la sesión
$userName = Session::get('user_name', 'Usuario');
$userEmail = Session::get('user_email', '');
$empresaNombre = Session::get('empresa_nombre', 'Empresa');
$paisCodigo = Session::get('pais_codigo', 'CL');
$idiomaCodigo = Session::get('idioma_codigo', 'es');
$empresaId = Session::get('empresa_id');

// Obtener notificaciones no leídas
$notificaciones = db()->select(
    "SELECT id, titulo, mensaje, tipo, created_at
     FROM notificaciones
     WHERE usuario_id = :user_id
     AND leida = 0
     ORDER BY created_at DESC
     LIMIT 5",
    ['user_id' => Session::get('user_id')]
);

$notificacionesCount = count($notificaciones);

// Helper para iconos de notificaciones
function getNotificationIcon($tipo) {
    $icons = [
        'info' => 'ℹ️',
        'success' => '✅',
        'warning' => '⚠️',
        'error' => '❌',
        'mensaje' => '💬',
    ];
    return $icons[$tipo] ?? 'ℹ️';
}

// Helper para tiempo relativo
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) {
        return 'Hace ' . $diff . ' segundos';
    } elseif ($diff < 3600) {
        return 'Hace ' . floor($diff / 60) . ' minutos';
    } elseif ($diff < 86400) {
        return 'Hace ' . floor($diff / 3600) . ' horas';
    } else {
        return 'Hace ' . floor($diff / 86400) . ' días';
    }
}
?>
<header class="erp-header">
    <div class="header-left">
        <!-- Botón toggle sidebar -->
        <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
            <span class="icon">☰</span>
        </button>

        <!-- Logo -->
        <a href="/app/router.php?module=dashboard" class="header-logo">
            <span class="logo-icon">📊</span>
            <span class="logo-text">Conecta ERP</span>
        </a>

        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <span class="breadcrumb-item"><?= e($empresaNombre) ?></span>
            <?php if (isset($pageTitle)): ?>
                <span class="breadcrumb-separator">›</span>
                <span class="breadcrumb-item active"><?= e($pageTitle) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="header-right">
        <!-- Búsqueda global -->
        <div class="header-search">
            <input type="text" placeholder="Buscar..." class="search-input" id="globalSearch">
            <span class="search-icon">🔍</span>
        </div>

        <!-- Selector de idioma -->
        <div class="header-language" id="languageSelector">
            <button class="language-btn" onclick="toggleLanguageMenu()">
                <span class="lang-code"><?= strtoupper($idiomaCodigo) ?></span>
            </button>
            <div class="language-menu" id="languageMenu" style="display: none;">
                <div class="language-menu-header">Idioma / Language</div>
                <a href="?change_lang=es" class="language-option <?= $idiomaCodigo === 'es' ? 'active' : '' ?>">
                    <span class="flag">🇪🇸</span>
                    <span>Español</span>
                </a>
                <a href="?change_lang=en" class="language-option <?= $idiomaCodigo === 'en' ? 'active' : '' ?>">
                    <span class="flag">🇺🇸</span>
                    <span>English</span>
                </a>
                <a href="?change_lang=pt" class="language-option <?= $idiomaCodigo === 'pt' ? 'active' : '' ?>">
                    <span class="flag">🇧🇷</span>
                    <span>Português</span>
                </a>
                <a href="?change_lang=fr" class="language-option <?= $idiomaCodigo === 'fr' ? 'active' : '' ?>">
                    <span class="flag">🇫🇷</span>
                    <span>Français</span>
                </a>
                <a href="?change_lang=de" class="language-option <?= $idiomaCodigo === 'de' ? 'active' : '' ?>">
                    <span class="flag">🇩🇪</span>
                    <span>Deutsch</span>
                </a>
                <a href="?change_lang=it" class="language-option <?= $idiomaCodigo === 'it' ? 'active' : '' ?>">
                    <span class="flag">🇮🇹</span>
                    <span>Italiano</span>
                </a>
            </div>
        </div>

        <!-- Notificaciones -->
        <div class="header-notifications" id="notificationsDropdown">
            <button class="notifications-btn" onclick="toggleNotifications()">
                <span class="icon">🔔</span>
                <?php if ($notificacionesCount > 0): ?>
                    <span class="badge"><?= $notificacionesCount ?></span>
                <?php endif; ?>
            </button>
            <div class="notifications-menu" id="notificationsMenu" style="display: none;">
                <div class="notifications-header">
                    Notificaciones
                    <?php if ($notificacionesCount > 0): ?>
                        <a href="#" class="mark-all-read" onclick="markAllAsRead(); return false;">Marcar todas como leídas</a>
                    <?php endif; ?>
                </div>
                <div class="notifications-list">
                    <?php if (empty($notificaciones)): ?>
                        <div class="notification-empty">
                            <span class="icon">✓</span>
                            <p>No tienes notificaciones</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notificaciones as $notif): ?>
                            <a href="#" class="notification-item" data-id="<?= $notif['id'] ?>">
                                <div class="notification-icon <?= $notif['tipo'] ?>">
                                    <?= getNotificationIcon($notif['tipo']) ?>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title"><?= e($notif['titulo']) ?></div>
                                    <div class="notification-message"><?= e($notif['mensaje']) ?></div>
                                    <div class="notification-time"><?= timeAgo($notif['created_at']) ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="notifications-footer">
                    <a href="/app/router.php?module=notificaciones">Ver todas</a>
                </div>
            </div>
        </div>

        <!-- Usuario -->
        <div class="header-user" id="userDropdown">
            <button class="user-btn" onclick="toggleUserMenu()">
                <div class="user-avatar"><?= strtoupper(substr($userName, 0, 1)) ?></div>
                <div class="user-info">
                    <div class="user-name"><?= e($userName) ?></div>
                    <div class="user-role"><?= e($empresaNombre) ?></div>
                </div>
                <span class="dropdown-arrow">▼</span>
            </button>
            <div class="user-menu" id="userMenu" style="display: none;">
                <div class="user-menu-header">
                    <div class="user-avatar-large"><?= strtoupper(substr($userName, 0, 1)) ?></div>
                    <div class="user-details">
                        <div class="user-name-large"><?= e($userName) ?></div>
                        <div class="user-email"><?= e($userEmail) ?></div>
                    </div>
                </div>
                <div class="user-menu-divider"></div>
                <a href="/app/router.php?module=perfil" class="user-menu-item">
                    <span class="icon">👤</span>
                    <span>Mi perfil</span>
                </a>
                <a href="/app/router.php?module=config" class="user-menu-item">
                    <span class="icon">⚙️</span>
                    <span>Configuración</span>
                </a>
                <a href="/app/router.php?module=ayuda" class="user-menu-item">
                    <span class="icon">❓</span>
                    <span>Ayuda</span>
                </a>
                <div class="user-menu-divider"></div>
                <a href="/app/logout.php" class="user-menu-item logout">
                    <span class="icon">🚪</span>
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </div>
    </div>
</header>

<script>
// Toggle sidebar
function toggleSidebar() {
    document.body.classList.toggle('sidebar-collapsed');
    localStorage.setItem('sidebarCollapsed', document.body.classList.contains('sidebar-collapsed'));
}

// Toggle language menu
function toggleLanguageMenu() {
    const menu = document.getElementById('languageMenu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

// Toggle notifications
function toggleNotifications() {
    const menu = document.getElementById('notificationsMenu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

// Toggle user menu
function toggleUserMenu() {
    const menu = document.getElementById('userMenu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

// Mark all as read
function markAllAsRead() {
    fetch('/app/router.php?module=notificaciones&action=mark_all_read', {
        method: 'POST'
    }).then(() => {
        location.reload();
    });
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('#languageSelector')) {
        document.getElementById('languageMenu').style.display = 'none';
    }
    if (!event.target.closest('#notificationsDropdown')) {
        document.getElementById('notificationsMenu').style.display = 'none';
    }
    if (!event.target.closest('#userDropdown')) {
        document.getElementById('userMenu').style.display = 'none';
    }
});

// Restore sidebar state
if (localStorage.getItem('sidebarCollapsed') === 'true') {
    document.body.classList.add('sidebar-collapsed');
}
</script>
