<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <span class="logo-icon">⚡</span>
            <span class="logo-text">Conecta ERP</span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <span class="toggle-icon">‹</span>
        </button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <a href="/app/dashboard/dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
                <span class="nav-icon">📊</span>
                <span class="nav-text">Dashboard</span>
            </a>
        </div>

        <?php if (Permissions::can('contabilidad')): ?>
        <div class="nav-section">
            <div class="nav-section-title">Finanzas</div>
            <a href="/app/contabilidad/index.php" class="nav-item">
                <span class="nav-icon">💰</span>
                <span class="nav-text">Contabilidad</span>
            </a>
            <a href="/app/tesoreria/index.php" class="nav-item">
                <span class="nav-icon">🏦</span>
                <span class="nav-text">Tesorería</span>
            </a>
        </div>
        <?php endif; ?>

        <?php if (Permissions::can('ventas')): ?>
        <div class="nav-section">
            <div class="nav-section-title">Comercial</div>
            <a href="/app/ventas/index.php" class="nav-item">
                <span class="nav-icon">📈</span>
                <span class="nav-text">Ventas</span>
            </a>
            <a href="/app/compras/index.php" class="nav-item">
                <span class="nav-icon">🛒</span>
                <span class="nav-text">Compras</span>
            </a>
            <a href="/app/crm/index.php" class="nav-item">
                <span class="nav-icon">👥</span>
                <span class="nav-text">CRM</span>
            </a>
        </div>
        <?php endif; ?>

        <?php if (Permissions::can('inventario')): ?>
        <div class="nav-section">
            <div class="nav-section-title">Operaciones</div>
            <a href="/app/inventario/index.php" class="nav-item">
                <span class="nav-icon">📦</span>
                <span class="nav-text">Inventario</span>
            </a>
            <?php if (Permissions::can('produccion')): ?>
            <a href="/app/produccion/index.php" class="nav-item">
                <span class="nav-icon">🏭</span>
                <span class="nav-text">Producción</span>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (Permissions::can('proyectos')): ?>
        <div class="nav-section">
            <a href="/app/proyectos/index.php" class="nav-item">
                <span class="nav-icon">📋</span>
                <span class="nav-text">Proyectos</span>
            </a>
            <a href="/app/rrhh/index.php" class="nav-item">
                <span class="nav-icon">👔</span>
                <span class="nav-text">RRHH</span>
            </a>
        </div>
        <?php endif; ?>

        <?php if (Permissions::can('bi_avanzado')): ?>
        <div class="nav-section">
            <a href="/app/bi/index.php" class="nav-item">
                <span class="nav-icon">📊</span>
                <span class="nav-text">Business Intelligence</span>
            </a>
        </div>
        <?php endif; ?>

        <div class="nav-section">
            <div class="nav-section-title">Sistema</div>
            <a href="/app/configuracion/index.php" class="nav-item">
                <span class="nav-icon">⚙️</span>
                <span class="nav-text">Configuración</span>
            </a>
            <?php if (SessionManager::isSuperUser()): ?>
            <a href="/app/admin/index.php" class="nav-item">
                <span class="nav-icon">🔧</span>
                <span class="nav-text">Administración</span>
            </a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <?= strtoupper(substr(SessionManager::get('usuario_nombre'), 0, 1)) ?>
            </div>
            <div class="user-details">
                <div class="user-name"><?= Security::preventXSS(SessionManager::get('usuario_nombre')) ?></div>
                <div class="user-email"><?= Security::preventXSS(SessionManager::get('usuario_email')) ?></div>
            </div>
        </div>
        <a href="/app/auth/logout.php" class="logout-btn">
            <span class="nav-icon">🚪</span>
            <span class="nav-text">Salir</span>
        </a>
    </div>
</aside>
