<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Tareas de Proyectos';
$tareas = db()->select("SELECT t.*, p.nombre as proyecto_nombre FROM tareas_proyecto t LEFT JOIN proyectos p ON t.proyecto_id = p.id WHERE p.empresa_id = :id ORDER BY t.created_at DESC", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>✓ Tareas</h1>
        <p>Gestión de tareas de proyectos</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="nuevaTarea()">➕ Nueva Tarea</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($tareas)): ?>
            <div class="empty-state">
                <span class="icon">✓</span>
                <h3>No hay tareas</h3>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>Tarea</th><th>Proyecto</th><th>Asignado a</th><th>Prioridad</th><th>Estado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($tareas as $t): ?>
                            <tr>
                                <td><strong><?= e($t['titulo']) ?></strong></td>
                                <td><?= e($t['proyecto_nombre']) ?></td>
                                <td><?= e($t['asignado_a'] ?? '-') ?></td>
                                <td><span class="badge badge-<?= $t['prioridad'] ?>"><?= e(ucfirst($t['prioridad'])) ?></span></td>
                                <td><span class="badge badge-<?= $t['estado'] == 'completada' ? 'success' : 'warning' ?>"><?= e(ucfirst($t['estado'])) ?></span></td>
                                <td class="actions"><button class="btn-icon" onclick="verTarea(<?= $t['id'] ?>)">👁️</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
function nuevaTarea() { alert('Nueva tarea'); }
function verTarea(id) { alert('Ver tarea ' + id); }
</script>
