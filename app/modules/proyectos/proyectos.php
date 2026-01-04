<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Proyectos';
$proyectos = db()->select("SELECT * FROM proyectos WHERE empresa_id = :id ORDER BY created_at DESC", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>📊 Proyectos</h1>
        <p>Gestión de proyectos y planificación</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="nuevoProyecto()">➕ Nuevo Proyecto</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($proyectos)): ?>
            <div class="empty-state">
                <span class="icon">📁</span>
                <h3>No hay proyectos</h3>
                <p>Crea tu primer proyecto</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr><th>Nombre</th><th>Cliente</th><th>Inicio</th><th>Fin</th><th>Progreso</th><th>Estado</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proyectos as $p): ?>
                            <tr>
                                <td><strong><?= e($p['nombre']) ?></strong></td>
                                <td><?= e($p['cliente_nombre'] ?? '-') ?></td>
                                <td><?= e($p['fecha_inicio'] ?? '-') ?></td>
                                <td><?= e($p['fecha_fin'] ?? '-') ?></td>
                                <td><?= e($p['progreso'] ?? 0) ?>%</td>
                                <td><span class="badge badge-<?= $p['estado'] == 'activo' ? 'success' : 'secondary' ?>"><?= e(ucfirst($p['estado'])) ?></span></td>
                                <td class="actions">
                                    <button class="btn-icon" onclick="verProyecto(<?= $p['id'] ?>)">👁️</button>
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
function nuevoProyecto() { alert('Crear proyecto'); }
function verProyecto(id) { alert('Ver proyecto ' + id); }
</script>
