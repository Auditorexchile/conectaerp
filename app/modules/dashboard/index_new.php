<?php
/**
 * CONECTA ERP - DASHBOARD PRINCIPAL
 * Dashboard con banner de trial, widgets y resumen ejecutivo
 */

if (!defined('CONECTA_ERP')) {
    die('Acceso no autorizado');
}

require_once __DIR__ . '/../../helpers/trial.php';

$empresaId = Session::get('empresa_id');
$userName = Session::get('user_name', 'Usuario');

// Obtener datos de la empresa
$empresa = db()->selectOne(
    "SELECT * FROM empresas WHERE id = :id",
    ['id' => $empresaId]
);

// Obtener información del trial
$trialInfo = TrialHelper::getTrialInfo($empresaId);

// Obtener plan actual
$plan = db()->selectOne(
    "SELECT ps.* FROM planes_suscripcion ps
     JOIN empresas e ON e.plan_id = ps.id
     WHERE e.id = :id",
    ['id' => $empresaId]
);

$pageTitle = 'Dashboard';
?>

<!-- Banner de Trial (si aplica) -->
<?php if ($trialInfo && $trialInfo['en_trial']): ?>
    <?= TrialHelper::renderBanner($empresaId) ?>
<?php endif; ?>

<!-- Header del Dashboard con botón maximizar -->
<div class="dashboard-header">
    <div class="dashboard-title">
        <h1>Bienvenido, <?= e($userName) ?> 👋</h1>
        <p>Aquí tienes un resumen de tu empresa <strong><?= e($empresa['razon_social'] ?? 'tu empresa') ?></strong></p>
    </div>
    <div class="dashboard-actions">
        <button class="btn btn-secondary" onclick="toggleMaximize()" id="btnMaximize">
            <span class="icon" id="iconMaximize">⛶</span>
            <span id="textMaximize">Maximizar</span>
        </button>
        <a href="/app/router.php?module=config&sub=empresa" class="btn btn-primary">
            <span class="icon">⚙️</span>
            Configuración
        </a>
    </div>
</div>

<!-- Métricas principales -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-icon">💰</div>
        <div class="metric-content">
            <div class="metric-label">Ventas del Mes</div>
            <div class="metric-value">$0</div>
            <div class="metric-change positive">+0% vs mes anterior</div>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon">🛒</div>
        <div class="metric-content">
            <div class="metric-label">Compras del Mes</div>
            <div class="metric-value">$0</div>
            <div class="metric-change negative">+0% vs mes anterior</div>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon">📦</div>
        <div class="metric-content">
            <div class="metric-label">Productos</div>
            <div class="metric-value">0</div>
            <div class="metric-change">0 activos</div>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon">👥</div>
        <div class="metric-content">
            <div class="metric-label">Clientes</div>
            <div class="metric-value">0</div>
            <div class="metric-change">0 nuevos este mes</div>
        </div>
    </div>
</div>

<!-- Grid de widgets -->
<div class="dashboard-grid">

    <!-- Resumen Financiero -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3>📊 Resumen Financiero</h3>
            <a href="/app/router.php?module=fi" class="card-link">Ver más →</a>
        </div>
        <div class="card-body">
            <div class="summary-row">
                <span class="summary-label">Ingresos</span>
                <span class="summary-value positive">$0</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Gastos</span>
                <span class="summary-value negative">$0</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Utilidad</span>
                <span class="summary-value">$0</span>
            </div>
            <div class="summary-row total">
                <span class="summary-label"><strong>Balance</strong></span>
                <span class="summary-value"><strong>$0</strong></span>
            </div>
        </div>
    </div>

    <!-- Inventario -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3>📦 Inventario</h3>
            <a href="/app/router.php?module=mm" class="card-link">Ver más →</a>
        </div>
        <div class="card-body">
            <div class="summary-row">
                <span class="summary-label">Total Productos</span>
                <span class="summary-value">0</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Stock Bajo</span>
                <span class="summary-value warning">0</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Sin Stock</span>
                <span class="summary-value danger">0</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Valor Total</span>
                <span class="summary-value">$0</span>
            </div>
        </div>
    </div>

    <!-- Auditoría IA -->
    <div class="dashboard-card highlight">
        <div class="card-header">
            <h3>🤖 Auditoría IA</h3>
            <a href="/app/router.php?module=ia&sub=dashboard" class="card-link">Ver más →</a>
        </div>
        <div class="card-body">
            <div class="ai-score">
                <div class="score-circle">
                    <div class="score-value">--</div>
                    <div class="score-label">Score</div>
                </div>
                <div class="score-info">
                    <p>Auditoría IA no configurada</p>
                    <a href="/app/router.php?module=ia&sub=dashboard" class="btn btn-sm btn-primary">
                        Activar ahora
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Plan Actual -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3>💎 Tu Plan</h3>
            <a href="/app/router.php?module=config&sub=plan" class="card-link">Gestionar →</a>
        </div>
        <div class="card-body">
            <div class="plan-badge <?= e($plan['codigo'] ?? 'starter') ?>">
                <?= e($plan['nombre'] ?? 'Starter') ?>
            </div>
            <?php if ($trialInfo && $trialInfo['en_trial']): ?>
                <p class="plan-info">
                    <strong>Trial:</strong> <?= $trialInfo['dias_restantes'] ?> días restantes
                </p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $trialInfo['porcentaje_usado'] ?>%"></div>
                </div>
            <?php else: ?>
                <p class="plan-info">
                    Plan activo y funcionando
                </p>
            <?php endif; ?>

            <?php if ($plan && isset($plan['modulos_incluidos'])): ?>
                <div class="plan-features">
                    <p><strong>Módulos incluidos:</strong></p>
                    <ul>
                        <?php
                        $modulos = json_decode($plan['modulos_incluidos'], true);
                        if ($modulos && is_array($modulos)):
                            foreach (array_slice($modulos, 0, 4) as $modulo):
                        ?>
                            <li>✓ <?= e($modulo) ?></li>
                        <?php
                            endforeach;
                            if (count($modulos) > 4):
                        ?>
                            <li>Y <?= count($modulos) - 4 ?> más...</li>
                        <?php endif; endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Actividad Reciente (Ancho completo) -->
    <div class="dashboard-card full-width">
        <div class="card-header">
            <h3>📝 Actividad Reciente</h3>
            <a href="/app/router.php?module=auditoria" class="card-link">Ver todo →</a>
        </div>
        <div class="card-body">
            <div class="activity-list">
                <div class="activity-empty">
                    <span class="icon">📋</span>
                    <p>No hay actividad reciente</p>
                    <small>Las acciones realizadas en el sistema aparecerán aquí</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos Rápidos (Ancho completo) -->
    <div class="dashboard-card full-width">
        <div class="card-header">
            <h3>🚀 Accesos Rápidos</h3>
        </div>
        <div class="card-body">
            <div class="quick-actions">
                <a href="/app/router.php?module=sd&sub=facturas" class="quick-action">
                    <span class="icon">📄</span>
                    <span class="text">Nueva Factura</span>
                </a>
                <a href="/app/router.php?module=sd&sub=clientes" class="quick-action">
                    <span class="icon">👥</span>
                    <span class="text">Nuevo Cliente</span>
                </a>
                <a href="/app/router.php?module=mm&sub=productos" class="quick-action">
                    <span class="icon">📦</span>
                    <span class="text">Nuevo Producto</span>
                </a>
                <a href="/app/router.php?module=fi&sub=comprobantes" class="quick-action">
                    <span class="icon">📚</span>
                    <span class="text">Comprobante</span>
                </a>
                <a href="/app/router.php?module=hr&sub=empleados" class="quick-action">
                    <span class="icon">👤</span>
                    <span class="text">Empleado</span>
                </a>
                <a href="/app/router.php?module=ia&sub=dashboard" class="quick-action">
                    <span class="icon">🤖</span>
                    <span class="text">Auditoría IA</span>
                </a>
            </div>
        </div>
    </div>

</div>

<script>
// Función para maximizar/minimizar (ocultar sidebar)
function toggleMaximize() {
    const sidebar = document.getElementById('erpSidebar');
    const body = document.body;
    const btnText = document.getElementById('textMaximize');
    const btnIcon = document.getElementById('iconMaximize');

    if (body.classList.contains('dashboard-maximized')) {
        // Restaurar
        body.classList.remove('dashboard-maximized');
        sidebar.style.display = '';
        btnText.textContent = 'Maximizar';
        btnIcon.textContent = '⛶';
        localStorage.setItem('dashboardMaximized', 'false');
    } else {
        // Maximizar (ocultar sidebar)
        body.classList.add('dashboard-maximized');
        sidebar.style.display = 'none';
        btnText.textContent = 'Restaurar';
        btnIcon.textContent = '◫';
        localStorage.setItem('dashboardMaximized', 'true');
    }
}

// Restaurar estado al cargar
if (localStorage.getItem('dashboardMaximized') === 'true') {
    const sidebar = document.getElementById('erpSidebar');
    const body = document.body;
    const btnText = document.getElementById('textMaximize');
    const btnIcon = document.getElementById('iconMaximize');

    body.classList.add('dashboard-maximized');
    sidebar.style.display = 'none';
    btnText.textContent = 'Restaurar';
    btnIcon.textContent = '◫';
}
</script>
