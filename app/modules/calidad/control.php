<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Control de Calidad';
$controles = db()->select("SELECT * FROM control_calidad WHERE empresa_id = :id ORDER BY fecha DESC LIMIT 50", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>✓ Control de Calidad</h1>
        <p>Inspecciones y controles de calidad</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="nuevoControl()">➕ Nuevo Control</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($controles)): ?>
            <div class="empty-state">
                <span class="icon">✓</span>
                <h3>No hay controles registrados</h3>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>Fecha</th><th>Producto/Lote</th><th>Inspector</th><th>Resultado</th><th>Conformidad</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($controles as $c): ?>
                            <tr>
                                <td><?= e(date('d/m/Y H:i', strtotime($c['fecha']))) ?></td>
                                <td><?= e($c['producto_nombre'] ?? $c['lote_numero']) ?></td>
                                <td><?= e($c['inspector_nombre']) ?></td>
                                <td><?= e($c['resultado']) ?></td>
                                <td><span class="badge badge-<?= $c['conforme'] ? 'success' : 'danger' ?>"><?= $c['conforme'] ? 'Conforme' : 'No Conforme' ?></span></td>
                                <td class="actions"><button class="btn-icon" onclick="verControl(<?= $c['id'] ?>)">👁️</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
function nuevoControl() { alert('Nuevo control'); }
function verControl(id) { alert('Ver control ' + id); }
</script>
