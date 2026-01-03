<?php
// Módulo: Configuración General

$empresaId = Session::getEmpresaId();

// Obtener datos de la empresa
$empresa = db()->selectOne(
    "SELECT e.*, es.nombre as estado_nombre
     FROM empresas e
     JOIN empresa_estado es ON e.estado_id = es.id
     WHERE e.id = :id",
    ['id' => $empresaId]
);

// Obtener suscripción actual
$suscripcion = db()->selectOne(
    "SELECT s.*, p.nombre as plan_nombre, p.codigo as plan_codigo
     FROM suscripciones s
     JOIN planes p ON s.plan_id = p.id
     WHERE s.empresa_id = :id AND s.activo = true
     ORDER BY s.created_at DESC LIMIT 1",
    ['id' => $empresaId]
);

// Obtener trial
$trial = db()->selectOne(
    "SELECT * FROM trial_configuracion WHERE empresa_id = :id",
    ['id' => $empresaId]
);
?>

<div class="page-header">
    <h1>Configuración General</h1>
</div>

<?php if (getFlash('success')): ?>
    <div class="alert alert-success"><?php echo getFlash('success'); ?></div>
<?php endif; ?>

<div class="dashboard-grid">
    <!-- Información de Empresa -->
    <div class="dashboard-card">
        <h3>Información de la Empresa</h3>
        <div class="card-content">
            <div class="config-item">
                <strong>RUT:</strong>
                <span><?php echo e($empresa['rut']); ?></span>
            </div>
            <div class="config-item">
                <strong>Razón Social:</strong>
                <span><?php echo e($empresa['razon_social']); ?></span>
            </div>
            <div class="config-item">
                <strong>Nombre Fantasía:</strong>
                <span><?php echo e($empresa['nombre_fantasia']); ?></span>
            </div>
            <div class="config-item">
                <strong>Giro:</strong>
                <span><?php echo e($empresa['giro']); ?></span>
            </div>
            <div class="config-item">
                <strong>Estado:</strong>
                <span class="badge badge-success"><?php echo e($empresa['estado_nombre']); ?></span>
            </div>
            <div class="config-item">
                <strong>Fecha Registro:</strong>
                <span><?php echo formatDate($empresa['created_at']); ?></span>
            </div>
        </div>
    </div>

    <!-- Plan y Suscripción -->
    <div class="dashboard-card">
        <h3>Plan y Suscripción</h3>
        <div class="card-content">
            <?php if ($suscripcion): ?>
                <div class="config-item">
                    <strong>Plan Actual:</strong>
                    <span class="badge badge-success"><?php echo e($suscripcion['plan_nombre']); ?></span>
                </div>
                <div class="config-item">
                    <strong>Precio:</strong>
                    <span><?php echo formatCurrency($suscripcion['precio']); ?> / mes</span>
                </div>
                <div class="config-item">
                    <strong>Fecha Inicio:</strong>
                    <span><?php echo formatDate($suscripcion['fecha_inicio']); ?></span>
                </div>
                <div class="config-item">
                    <strong>Auto Renovación:</strong>
                    <span><?php echo $suscripcion['auto_renovacion'] ? 'Sí' : 'No'; ?></span>
                </div>
            <?php else: ?>
                <p class="text-muted">Sin suscripción activa</p>
            <?php endif; ?>

            <?php if ($trial && $trial['activo']): ?>
                <div class="alert alert-warning" style="margin-top: 20px;">
                    <strong>Trial activo</strong><br>
                    Desde: <?php echo formatDate($trial['fecha_inicio']); ?><br>
                    Hasta: <?php echo formatDate($trial['fecha_fin']); ?><br>
                    Días restantes: <?php echo getDaysRemainingTrial(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Configuración Regional -->
    <div class="dashboard-card">
        <h3>Configuración Regional</h3>
        <div class="card-content">
            <div class="config-item">
                <strong>País:</strong>
                <span><?php echo e($empresa['pais']); ?></span>
            </div>
            <div class="config-item">
                <strong>Moneda Base:</strong>
                <span><?php echo e($empresa['moneda_base']); ?></span>
            </div>
            <div class="config-item">
                <strong>Idioma:</strong>
                <span><?php echo e($empresa['idioma']); ?></span>
            </div>
        </div>
    </div>

    <!-- Acciones -->
    <div class="dashboard-card">
        <h3>Acciones</h3>
        <div class="card-content">
            <button class="btn-primary btn-block" onclick="alert('Funcionalidad próximamente')">
                Editar Información
            </button>
            <button class="btn-outline btn-block" onclick="alert('Funcionalidad próximamente')" style="margin-top: 10px;">
                Cambiar Plan
            </button>
            <button class="btn-outline btn-block" onclick="alert('Funcionalidad próximamente')" style="margin-top: 10px;">
                Ver Facturación
            </button>
        </div>
    </div>
</div>

<style>
.page-header { margin-bottom: 20px; }
.dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
.dashboard-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.dashboard-card h3 { margin-bottom: 16px; font-size: 18px; }
.card-content { display: flex; flex-direction: column; gap: 12px; }
.config-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--gray-100); }
.config-item:last-child { border-bottom: none; }
.btn-block { width: 100%; }
</style>
