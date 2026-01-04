<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Hitos de Proyectos';
$hitos = db()->select("SELECT h.*, p.nombre as proyecto_nombre FROM hitos h LEFT JOIN proyectos p ON h.proyecto_id = p.id WHERE p.empresa_id = :id ORDER BY h.fecha_objetivo ASC", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>🎯 Hitos</h1>
        <p>Milestones y objetivos de proyectos</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="nuevoHito()">➕ Nuevo Hito</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($hitos)): ?>
            <div class="empty-state">
                <span class="icon">🎯</span>
                <h3>No hay hitos</h3>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>Hito</th><th>Proyecto</th><th>Fecha Objetivo</th><th>Estado</th><th>Completado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($hitos as $h): ?>
                            <tr>
                                <td><strong><?= e($h['titulo']) ?></strong></td>
                                <td><?= e($h['proyecto_nombre']) ?></td>
                                <td><?= e(date('d/m/Y', strtotime($h['fecha_objetivo']))) ?></td>
                                <td><span class="badge badge-<?= $h['completado'] ? 'success' : 'warning' ?>"><?= $h['completado'] ? 'Completado' : 'Pendiente' ?></span></td>
                                <td><?= $h['completado'] ? e(date('d/m/Y', strtotime($h['fecha_completado']))) : '-' ?></td>
                                <td class="actions"><button class="btn-icon" onclick="verHito(<?= $h['id'] ?>)">👁️</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
function nuevoHito() { alert('Nuevo hito'); }
function verHito(id) { alert('Ver hito ' + id); }
</script>
