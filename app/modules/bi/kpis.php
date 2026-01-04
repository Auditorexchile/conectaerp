<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'KPIs';
$kpis = db()->select("SELECT * FROM kpis WHERE empresa_id = :id ORDER BY categoria, nombre ASC", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>📌 KPIs (Indicadores Clave)</h1>
        <p>Define y monitorea los KPIs de tu negocio</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="nuevoKPI()">➕ Nuevo KPI</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($kpis)): ?>
            <div class="empty-state">
                <span class="icon">📌</span>
                <h3>No hay KPIs configurados</h3>
                <p>Define tus indicadores clave de rendimiento</p>
            </div>
        <?php else: ?>
            <div class="kpis-grid">
                <?php foreach ($kpis as $kpi): ?>
                    <div class="kpi-card">
                        <div class="kpi-header">
                            <h4><?= e($kpi['nombre']) ?></h4>
                            <span class="kpi-category"><?= e($kpi['categoria']) ?></span>
                        </div>
                        <div class="kpi-value">
                            <span class="value"><?= e(number_format($kpi['valor_actual'], 2)) ?></span>
                            <span class="unit"><?= e($kpi['unidad'] ?? '%') ?></span>
                        </div>
                        <div class="kpi-target">
                            Meta: <?= e(number_format($kpi['valor_objetivo'], 2)) ?> <?= e($kpi['unidad'] ?? '%') ?>
                        </div>
                        <div class="kpi-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= min(100, ($kpi['valor_actual'] / $kpi['valor_objetivo']) * 100) ?>%"></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
function nuevoKPI() { alert('Nuevo KPI'); }
</script>
<style>
.kpis-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
}
.kpi-card {
    padding: 1.5rem;
    background: var(--bg-tertiary, #f3f4f6);
    border-radius: 8px;
    border-left: 4px solid var(--primary, #3b82f6);
}
.kpi-value {
    font-size: 2rem;
    font-weight: bold;
    margin: 1rem 0;
    color: var(--primary);
}
.kpi-target {
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
}
.progress-bar {
    background: var(--bg-secondary);
    height: 8px;
    border-radius: 4px;
    overflow: hidden;
}
.progress-fill {
    height: 100%;
    background: var(--primary);
    transition: width 0.3s;
}
</style>
