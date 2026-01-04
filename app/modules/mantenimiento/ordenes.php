<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Órdenes de Mantenimiento';
$ordenes = db()->select("SELECT * FROM mantenimientos WHERE empresa_id = :id AND tipo = 'correctivo' ORDER BY fecha_solicitud DESC LIMIT 50", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>📋 Órdenes de Mantenimiento</h1>
        <p>Gestión de órdenes de trabajo de mantenimiento</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="nuevaOrden()">➕ Nueva Orden</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($ordenes)): ?>
            <div class="empty-state">
                <span class="icon">📋</span>
                <h3>No hay órdenes de mantenimiento</h3>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>N° OT</th><th>Fecha</th><th>Equipo/Activo</th><th>Descripción</th><th>Prioridad</th><th>Estado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($ordenes as $o): ?>
                            <tr>
                                <td><strong>OT-<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                                <td><?= e(date('d/m/Y', strtotime($o['fecha_solicitud']))) ?></td>
                                <td><?= e($o['equipo_nombre'] ?? $o['activo_nombre']) ?></td>
                                <td><?= e(substr($o['descripcion'], 0, 40)) ?></td>
                                <td><span class="badge badge-<?= $o['prioridad'] ?>"><?= e(ucfirst($o['prioridad'])) ?></span></td>
                                <td><span class="badge badge-<?= getStatusClass($o['estado']) ?>"><?= e(ucfirst($o['estado'])) ?></span></td>
                                <td class="actions"><button class="btn-icon" onclick="verOrden(<?= $o['id'] ?>)">👁️</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
function getStatusClass($st) {
    return $st == 'completada' ? 'success' : ($st == 'en_proceso' ? 'warning' : 'secondary');
}
?>
<script>
function nuevaOrden() { alert('Nueva orden'); }
function verOrden(id) { alert('Ver orden ' + id); }
</script>
