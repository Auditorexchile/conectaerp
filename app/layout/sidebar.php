<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <span class="logo-icon">C</span>
            <span class="logo-text">Conecta ERP</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="<?php echo url('app/router.php?module=dashboard'); ?>" class="nav-item">
            <span class="nav-icon">📊</span>
            <span class="nav-text">Dashboard</span>
        </a>

        <div class="nav-section">
            <div class="nav-section-title">ADMINISTRACIÓN</div>
            <a href="<?php echo url('app/router.php?module=empresas'); ?>" class="nav-item">
                <span class="nav-icon">🏢</span>
                <span class="nav-text">Empresas</span>
            </a>
            <a href="<?php echo url('app/router.php?module=usuarios'); ?>" class="nav-item">
                <span class="nav-icon">👥</span>
                <span class="nav-text">Usuarios</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">ENTIDADES</div>
            <a href="<?php echo url('app/router.php?module=clientes'); ?>" class="nav-item">
                <span class="nav-icon">👥</span>
                <span class="nav-text">Clientes</span>
            </a>
            <a href="<?php echo url('app/router.php?module=proveedores'); ?>" class="nav-item">
                <span class="nav-icon">🚚</span>
                <span class="nav-text">Proveedores</span>
            </a>
            <a href="<?php echo url('app/router.php?module=productos'); ?>" class="nav-item">
                <span class="nav-icon">📦</span>
                <span class="nav-text">Productos</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">FINANZAS</div>
            <a href="<?php echo url('app/router.php?module=contabilidad'); ?>" class="nav-item">
                <span class="nav-icon">📘</span>
                <span class="nav-text">Contabilidad</span>
            </a>
            <a href="<?php echo url('app/router.php?module=tesoreria'); ?>" class="nav-item">
                <span class="nav-icon">💰</span>
                <span class="nav-text">Tesorería</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">OPERACIONES</div>
            <a href="<?php echo url('app/router.php?module=ventas'); ?>" class="nav-item">
                <span class="nav-icon">🛒</span>
                <span class="nav-text">Ventas</span>
            </a>
            <a href="<?php echo url('app/router.php?module=compras'); ?>" class="nav-item">
                <span class="nav-icon">📦</span>
                <span class="nav-text">Compras</span>
            </a>
            <a href="<?php echo url('app/router.php?module=inventario'); ?>" class="nav-item">
                <span class="nav-icon">📦</span>
                <span class="nav-text">Inventario</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">SISTEMA</div>
            <a href="<?php echo url('app/router.php?module=configuracion'); ?>" class="nav-item">
                <span class="nav-icon">⚙️</span>
                <span class="nav-text">Configuración</span>
            </a>
        </div>
    </nav>
</aside>
