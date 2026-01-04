<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Reportes Personalizados';
$reportes = db()->select("SELECT * FROM reportes_personalizados WHERE empresa_id = :id ORDER BY nombre ASC", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>📄 Reportes Personalizados</h1>
        <p>Crea y programa reportes automáticos</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="nuevoReporte()">➕ Nuevo Reporte</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($reportes)): ?>
            <div class="empty-state">
                <span class="icon">📄</span>
                <h3>No hay reportes personalizados</h3>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>Nombre</th><th>Tipo</th><th>Frecuencia</th><th>Última Ejecución</th><th>Formato</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($reportes as $r): ?>
                            <tr>
                                <td><strong><?= e($r['nombre']) ?></strong></td>
                                <td><?= e($r['tipo']) ?></td>
                                <td><?= e($r['frecuencia'] ?? 'Manual') ?></td>
                                <td><?= $r['last_run_at'] ? e(date('d/m/Y H:i', strtotime($r['last_run_at']))) : 'Nunca' ?></td>
                                <td><?= e(strtoupper($r['formato'] ?? 'PDF')) ?></td>
                                <td class="actions">
                                    <button class="btn-icon" onclick="ejecutarReporte(<?= $r['id'] ?>)" title="Ejecutar">▶️</button>
                                    <button class="btn-icon" onclick="editarReporte(<?= $r['id'] ?>)" title="Editar">✏️</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
function nuevoReporte() { alert('Nuevo reporte'); }
function ejecutarReporte(id) { window.location.href = '/app/api/bi/reportes.php?action=run&id=' + id; }
function editarReporte(id) { alert('Editar reporte ' + id); }
</script>
