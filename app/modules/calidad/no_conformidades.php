<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'No Conformidades';
$noConformidades = db()->select("SELECT * FROM no_conformidades WHERE empresa_id = :id ORDER BY fecha DESC LIMIT 50", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>⚠️ No Conformidades</h1>
        <p>Registro y seguimiento de no conformidades</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="nuevaNC()">➕ Nueva NC</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($noConformidades)): ?>
            <div class="empty-state">
                <span class="icon">✓</span>
                <h3>No hay no conformidades</h3>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>N°</th><th>Fecha</th><th>Descripción</th><th>Severidad</th><th>Estado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($noConformidades as $nc): ?>
                            <tr>
                                <td><strong>NC-<?= str_pad($nc['id'], 4, '0', STR_PAD_LEFT) ?></strong></td>
                                <td><?= e(date('d/m/Y', strtotime($nc['fecha']))) ?></td>
                                <td><?= e(substr($nc['descripcion'], 0, 50)) ?>...</td>
                                <td><span class="badge badge-<?= getSeverityClass($nc['severidad']) ?>"><?= e(ucfirst($nc['severidad'])) ?></span></td>
                                <td><span class="badge badge-<?= $nc['cerrada'] ? 'success' : 'warning' ?>"><?= $nc['cerrada'] ? 'Cerrada' : 'Abierta' ?></span></td>
                                <td class="actions"><button class="btn-icon" onclick="verNC(<?= $nc['id'] ?>)">👁️</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
function getSeverityClass($sev) {
    return $sev == 'critica' ? 'danger' : ($sev == 'mayor' ? 'warning' : 'info');
}
?>
<script>
function nuevaNC() { alert('Nueva NC'); }
function verNC(id) { alert('Ver NC ' + id); }
</script>
