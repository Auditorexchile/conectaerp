<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Métricas';
$metricas = db()->select("SELECT * FROM metricas WHERE empresa_id = :id ORDER BY categoria, nombre ASC", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>📉 Métricas</h1>
        <p>Métricas y estadísticas del negocio</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary" onclick="exportarMetricas()">📊 Exportar</button>
        <button class="btn btn-primary" onclick="nuevaMetrica()">➕ Nueva Métrica</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($metricas)): ?>
            <div class="empty-state">
                <span class="icon">📉</span>
                <h3>No hay métricas configuradas</h3>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>Métrica</th><th>Categoría</th><th>Valor Actual</th><th>Tendencia</th><th>Última Actualización</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($metricas as $m): ?>
                            <tr>
                                <td><strong><?= e($m['nombre']) ?></strong></td>
                                <td><span class="badge badge-info"><?= e($m['categoria']) ?></span></td>
                                <td><strong><?= e(number_format($m['valor'], 2)) ?></strong></td>
                                <td><?= getTrendIcon($m['tendencia'] ?? 0) ?></td>
                                <td><?= e(date('d/m/Y H:i', strtotime($m['updated_at']))) ?></td>
                                <td class="actions"><button class="btn-icon" onclick="verHistorial(<?= $m['id'] ?>)">📈</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
function getTrendIcon($trend) {
    if ($trend > 0) return '📈 +' . number_format($trend, 1) . '%';
    if ($trend < 0) return '📉 ' . number_format($trend, 1) . '%';
    return '➡️ 0%';
}
?>
<script>
function nuevaMetrica() { alert('Nueva métrica'); }
function exportarMetricas() { window.location.href = '/app/api/bi/metricas.php?action=export'; }
function verHistorial(id) { alert('Ver historial de métrica ' + id); }
</script>
