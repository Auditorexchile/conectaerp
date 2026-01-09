<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
define('CONFIG_PATH', BASE_PATH . '/app/config');

require_once BASE_PATH . '/app/core/bootstrap.php';
require_once BASE_PATH . '/app/core/database.php';
require_once BASE_PATH . '/app/core/session.php';
require_once BASE_PATH . '/app/core/security.php';
require_once BASE_PATH . '/app/core/permissions.php';
require_once BASE_PATH . '/app/core/helpers.php';
require_once BASE_PATH . '/app/middleware/auth.php';
require_once BASE_PATH . '/app/middleware/trial.php';

// Verificar autenticación y trial
AuthMiddleware::handle();
TrialMiddleware::handle();

$pageTitle = 'Dashboard';
$pageSubtitle = 'Resumen general de tu negocio';

// Obtener estadísticas
$db = Database::getInstance();
$empresaId = SessionManager::getEmpresaId();

// Stats básicas (se completarán con módulos reales)
$stats = [
    'ventas_mes' => 0,
    'compras_mes' => 0,
    'productos' => 0,
    'clientes' => 0
];

include BASE_PATH . '/app/layout/header.php';
?>

<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">📈</div>
        <div class="stat-content">
            <div class="stat-label">Ventas del Mes</div>
            <div class="stat-value"><?= formatMoney($stats['ventas_mes']) ?></div>
            <div class="stat-change positive">+12% vs mes anterior</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">🛒</div>
        <div class="stat-content">
            <div class="stat-label">Compras del Mes</div>
            <div class="stat-value"><?= formatMoney($stats['compras_mes']) ?></div>
            <div class="stat-change negative">-5% vs mes anterior</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">📦</div>
        <div class="stat-content">
            <div class="stat-label">Productos</div>
            <div class="stat-value"><?= number_format($stats['productos']) ?></div>
            <div class="stat-change">En inventario</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">👥</div>
        <div class="stat-content">
            <div class="stat-label">Clientes</div>
            <div class="stat-value"><?= number_format($stats['clientes']) ?></div>
            <div class="stat-change positive">+8 este mes</div>
        </div>
    </div>
</div>

<div class="dashboard-content">
    <div class="content-section">
        <h2>Bienvenido a Conecta ERP</h2>
        <p>Sistema completo de gestión empresarial. Selecciona un módulo del menú lateral para comenzar.</p>

        <?php if (SessionManager::getEmpresaEstado() === 'trial'): ?>
            <div class="trial-banner">
                <div class="trial-icon">⏰</div>
                <div class="trial-content">
                    <strong>Período de Prueba</strong>
                    <p>Te quedan <?= SessionManager::getTrialDaysRemaining() ?> días de prueba gratuita.
                       <a href="mailto:soporte@conectaerp.cl">Contacta con ventas</a> para actualizar tu plan.</p>
                </div>
            </div>
        <?php endif; ?>

        <div class="quick-actions">
            <h3>Acciones Rápidas</h3>
            <div class="actions-grid">
                <?php if (Permissions::can('ventas')): ?>
                <a href="/app/ventas/nueva.php" class="action-btn">
                    <span>📝</span>
                    <span>Nueva Venta</span>
                </a>
                <?php endif; ?>

                <?php if (Permissions::can('compras')): ?>
                <a href="/app/compras/nueva.php" class="action-btn">
                    <span>🛒</span>
                    <span>Nueva Compra</span>
                </a>
                <?php endif; ?>

                <?php if (Permissions::can('inventario')): ?>
                <a href="/app/inventario/productos.php" class="action-btn">
                    <span>📦</span>
                    <span>Ver Inventario</span>
                </a>
                <?php endif; ?>

                <?php if (Permissions::can('reportes')): ?>
                <a href="/app/reportes/index.php" class="action-btn">
                    <span>📊</span>
                    <span>Reportes</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/app/layout/footer.php'; ?>
