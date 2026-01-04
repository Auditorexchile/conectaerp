<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Mantenimiento Preventivo';
$preventivos = db()->select("SELECT * FROM mantenimientos WHERE empresa_id = :id AND tipo = 'preventivo' ORDER BY proxima_fecha ASC LIMIT 50", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>🛠️ Mantenimiento Preventivo</h1>
        <p>Planificación y seguimiento de mantenimientos preventivos</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="nuevoPreventivo()">➕ Nuevo Preventivo</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($preventivos)): ?>
            <div class="empty-state">
                <span class="icon">🛠️</span>
                <h3>No hay mantenimientos preventivos programados</h3>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>Equipo</th><th>Actividad</th><th>Frecuencia</th><th>Próxima Fecha</th><th>Última Ejecución</th><th>Estado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($preventivos as $p): ?>
                            <tr>
                                <td><strong><?= e($p['equipo_nombre']) ?></strong></td>
                                <td><?= e($p['actividad']) ?></td>
                                <td><?= e($p['frecuencia_dias']) ?> días</td>
                                <td><?= e(date('d/m/Y', strtotime($p['proxima_fecha']))) ?></td>
                                <td><?= $p['ultima_ejecucion'] ? e(date('d/m/Y', strtotime($p['ultima_ejecucion']))) : '-' ?></td>
                                <td><span class="badge badge-<?= strtotime($p['proxima_fecha']) < time() ? 'danger' : 'success' ?>"><?= strtotime($p['proxima_fecha']) < time() ? 'Vencido' : 'Vigente' ?></span></td>
                                <td class="actions"><button class="btn-icon" onclick="ejecutarPreventivo(<?= $p['id'] ?>)">▶️</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
function nuevoPreventivo() { alert('Nuevo preventivo'); }
function ejecutarPreventivo(id) { alert('Ejecutar preventivo ' + id); }
</script>
