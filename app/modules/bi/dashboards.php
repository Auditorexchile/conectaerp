<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Dashboards Personalizados';
$dashboards = db()->select("SELECT * FROM dashboards_personalizados WHERE empresa_id = :id ORDER BY nombre ASC", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>📊 Dashboards Personalizados</h1>
        <p>Crea dashboards personalizados con tus métricas</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="nuevoDashboard()">➕ Nuevo Dashboard</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($dashboards)): ?>
            <div class="empty-state">
                <span class="icon">📊</span>
                <h3>No hay dashboards personalizados</h3>
                <p>Crea tu primer dashboard con métricas importantes</p>
            </div>
        <?php else: ?>
            <div class="dashboards-grid">
                <?php foreach ($dashboards as $d): ?>
                    <div class="dashboard-card" onclick="verDashboard(<?= $d['id'] ?>)">
                        <h3><?= e($d['nombre']) ?></h3>
                        <p><?= e($d['descripcion']) ?></p>
                        <div class="dashboard-meta">
                            <span>📈 <?= e($d['widgets_count'] ?? 0) ?> widgets</span>
                            <span>👁️ <?= e($d['views_count'] ?? 0) ?> vistas</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
function nuevoDashboard() { alert('Nuevo dashboard'); }
function verDashboard(id) { window.location.href = '?module=bi&sub=dashboard_view&id=' + id; }
</script>
<style>
.dashboards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}
.dashboard-card {
    padding: 1.5rem;
    background: var(--bg-tertiary, #f3f4f6);
    border-radius: 8px;
    border: 1px solid var(--border-color, #e5e7eb);
    cursor: pointer;
    transition: all 0.2s;
}
.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.dashboard-meta {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    font-size: 0.9rem;
    color: var(--text-secondary);
}
</style>
