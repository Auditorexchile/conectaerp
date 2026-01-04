<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Plan y Facturación';
$empresa = db()->selectOne("SELECT e.*, p.nombre as plan_nombre, p.precio FROM empresas e LEFT JOIN planes_suscripcion p ON e.plan_id = p.id WHERE e.id = :id", ['id' => $empresaId]);
$planes = db()->select("SELECT * FROM planes_suscripcion WHERE activo = 1 ORDER BY precio ASC");
?>
<div class="page-header">
    <div class="page-title">
        <h1>💎 Plan y Facturación</h1>
        <p>Gestiona tu suscripción y facturación</p>
    </div>
</div>

<!-- Plan Actual -->
<div class="card">
    <div class="card-header">
        <h3>📋 Plan Actual</h3>
    </div>
    <div class="card-body">
        <div class="current-plan">
            <div class="plan-badge <?= e($empresa['plan_id'] ?? 1) ?>"><?= e($empresa['plan_nombre'] ?? 'Starter') ?></div>
            <p><strong>Precio:</strong> $<?= e(number_format($empresa['precio'] ?? 0, 0, ',', '.')) ?> / mes</p>
            <p><strong>Estado:</strong> <span class="badge badge-success">Activo</span></p>
            <?php if ($empresa['trial_ends_at'] && strtotime($empresa['trial_ends_at']) > time()): ?>
                <p><strong>Trial:</strong> Vence el <?= e(date('d/m/Y', strtotime($empresa['trial_ends_at']))) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Planes Disponibles -->
<div class="card">
    <div class="card-header">
        <h3>🚀 Planes Disponibles</h3>
    </div>
    <div class="card-body">
        <div class="plans-grid">
            <?php foreach ($planes as $plan): ?>
                <div class="plan-card <?= $plan['destacado'] ? 'featured' : '' ?> <?= $plan['id'] == $empresa['plan_id'] ? 'current' : '' ?>">
                    <h4><?= e($plan['nombre']) ?></h4>
                    <div class="plan-price">
                        <span class="price">$<?= e(number_format($plan['precio'], 0, ',', '.')) ?></span>
                        <span class="period">/mes</span>
                    </div>
                    <ul class="plan-features">
                        <li>✓ <?= e($plan['max_usuarios']) ?> usuarios</li>
                        <li>✓ <?= e($plan['max_facturas_mes']) ?> facturas/mes</li>
                        <li>✓ <?= e($plan['almacenamiento_gb']) ?> GB almacenamiento</li>
                    </ul>
                    <?php if ($plan['id'] != $empresa['plan_id']): ?>
                        <button class="btn btn-primary" onclick="cambiarPlan(<?= $plan['id'] ?>)">
                            <?= $plan['precio'] > $empresa['precio'] ? 'Actualizar' : 'Cambiar' ?> Plan
                        </button>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>Plan Actual</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Historial de Facturación -->
<div class="card">
    <div class="card-header">
        <h3>📄 Historial de Facturación</h3>
    </div>
    <div class="card-body">
        <div class="empty-state">
            <span class="icon">📄</span>
            <h3>No hay facturas</h3>
            <p>El historial de facturación aparecerá aquí</p>
        </div>
    </div>
</div>

<script>
function cambiarPlan(planId) {
    if (confirm('¿Deseas cambiar tu plan de suscripción?')) {
        fetch('/app/api/config/plan.php?action=change&plan_id=' + planId, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            alert(data.success ? 'Plan actualizado exitosamente' : 'Error: ' + data.message);
            if (data.success) location.reload();
        });
    }
}
</script>

<style>
.plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 1rem;
}
.plan-card {
    padding: 2rem;
    border: 2px solid var(--border-color, #e5e7eb);
    border-radius: 12px;
    text-align: center;
    transition: all 0.3s;
}
.plan-card.featured {
    border-color: var(--primary, #3b82f6);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}
.plan-card.current {
    background: var(--bg-tertiary, #f3f4f6);
}
.plan-price {
    margin: 1rem 0;
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary);
}
.plan-features {
    list-style: none;
    padding: 0;
    margin: 1.5rem 0;
    text-align: left;
}
.plan-features li {
    padding: 0.5rem 0;
}
</style>
