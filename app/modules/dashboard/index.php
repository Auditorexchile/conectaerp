<?php
// Dashboard principal

$empresaId = Session::getEmpresaId();

// Obtener datos de la empresa
$empresa = db()->selectOne(
    "SELECT * FROM empresas WHERE id = :id",
    ['id' => $empresaId]
);

// Verificar trial
$daysRemaining = getDaysRemainingTrial();
$inTrial = isInTrial();

// Obtener plan actual
$suscripcion = db()->selectOne(
    "SELECT p.nombre, p.codigo
     FROM suscripciones s
     JOIN planes p ON s.plan_id = p.id
     WHERE s.empresa_id = :id AND s.activo = true
     ORDER BY s.created_at DESC LIMIT 1",
    ['id' => $empresaId]
);

$planNombre = $suscripcion ? $suscripcion['nombre'] : 'Starter';
?>

<!-- Banner Dashboard -->
<div class="dashboard-banner">
    <div class="banner-left">
        <h2><?php echo e($empresa['razon_social']); ?></h2>
        <div class="banner-info">
            <span class="badge badge-<?php echo $inTrial ? 'warning' : 'success'; ?>">
                <?php echo $inTrial ? "Trial: {$daysRemaining} días restantes" : "Plan: {$planNombre}"; ?>
            </span>
        </div>
    </div>
    <div class="banner-right">
        <select id="idioma-selector" onchange="cambiarIdioma(this.value)">
            <?php
            $idiomas = config('languages');
            $currentLang = Session::get('locale', 'es');
            foreach ($idiomas as $code => $name):
            ?>
                <option value="<?php echo $code; ?>" <?php echo $code === $currentLang ? 'selected' : ''; ?>>
                    <?php echo $name; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="btn-icon" onclick="toggleDashboardMaximize()" title="Maximizar dashboard">
            ⛶
        </button>
        <a href="<?php echo url('public/logout.php'); ?>" class="btn-secondary">Salir</a>
    </div>
</div>

<?php if ($inTrial && $daysRemaining <= 7): ?>
    <div class="alert alert-warning">
        <strong>¡Tu período de prueba está por finalizar!</strong>
        Te quedan <?php echo $daysRemaining; ?> días.
        <a href="<?php echo url('app/router.php?module=planes'); ?>">Actualiza tu plan</a>
    </div>
<?php elseif (!$inTrial && (!$suscripcion || $suscripcion['codigo'] === 'starter')): ?>
    <div class="alert alert-error">
        <strong>Período de prueba finalizado</strong>
        Actualiza tu plan para acceder a todas las funcionalidades.
        <a href="<?php echo url('app/router.php?module=planes'); ?>" class="btn-primary">Ver planes</a>
    </div>
<?php endif; ?>

<!-- Dashboard Content -->
<div class="dashboard-content">
    <div class="dashboard-grid">
        <!-- Resumen financiero -->
        <div class="dashboard-card">
            <h3>Resumen Financiero</h3>
            <div class="card-content">
                <div class="metric">
                    <span class="metric-label">Ventas del mes</span>
                    <span class="metric-value"><?php echo formatCurrency(0); ?></span>
                </div>
                <div class="metric">
                    <span class="metric-label">Compras del mes</span>
                    <span class="metric-value"><?php echo formatCurrency(0); ?></span>
                </div>
            </div>
        </div>

        <!-- Inventario -->
        <div class="dashboard-card">
            <h3>Inventario</h3>
            <div class="card-content">
                <div class="metric">
                    <span class="metric-label">Productos activos</span>
                    <span class="metric-value">0</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Stock bajo</span>
                    <span class="metric-value">0</span>
                </div>
            </div>
        </div>

        <!-- Actividad reciente -->
        <div class="dashboard-card full-width">
            <h3>Actividad Reciente</h3>
            <div class="card-content">
                <p class="text-muted">No hay actividad reciente</p>
            </div>
        </div>
    </div>
</div>

<script>
function cambiarIdioma(idioma) {
    fetch('<?php echo url("app/ajax/cambiar_idioma.php"); ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({idioma: idioma})
    }).then(() => location.reload());
}

function toggleDashboardMaximize() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('main-content');
    sidebar.classList.toggle('hidden');
    content.classList.toggle('maximized');
}
</script>
