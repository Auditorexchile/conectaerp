<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Turnos y Horarios';
$turnos = db()->select("SELECT * FROM turnos WHERE empresa_id = :id ORDER BY nombre ASC", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>📅 Turnos y Horarios</h1>
        <p>Configuración de turnos de trabajo</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="nuevoTurno()">➕ Nuevo Turno</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($turnos)): ?>
            <div class="empty-state">
                <span class="icon">📅</span>
                <h3>No hay turnos configurados</h3>
                <p>Define los turnos de trabajo de tu empresa</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>Turno</th><th>Horario</th><th>Días</th><th>Tolerancia</th><th>Empleados</th><th>Estado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($turnos as $t): ?>
                            <tr>
                                <td><strong><?= e($t['nombre']) ?></strong></td>
                                <td><?= e($t['hora_inicio']) ?> - <?= e($t['hora_fin']) ?></td>
                                <td><?= e($t['dias_semana'] ?? 'L-V') ?></td>
                                <td><?= e($t['tolerancia_minutos'] ?? 0) ?> min</td>
                                <td><?= e($t['empleados_count'] ?? 0) ?> empleados</td>
                                <td><span class="badge badge-<?= $t['activo'] ? 'success' : 'danger' ?>"><?= $t['activo'] ? 'Activo' : 'Inactivo' ?></span></td>
                                <td class="actions">
                                    <button class="btn-icon" onclick="editarTurno(<?= $t['id'] ?>)" title="Editar">✏️</button>
                                    <button class="btn-icon" onclick="asignarEmpleados(<?= $t['id'] ?>)" title="Asignar Empleados">👥</button>
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
function nuevoTurno() { alert('Nuevo turno'); }
function editarTurno(id) { alert('Editar turno ' + id); }
function asignarEmpleados(id) { alert('Asignar empleados al turno ' + id); }
</script>
