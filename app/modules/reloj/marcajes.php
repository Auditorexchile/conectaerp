<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Marcajes';
$marcajes = db()->select("SELECT m.*, e.nombre as empleado_nombre FROM marcajes m LEFT JOIN empleados e ON m.empleado_id = e.id WHERE m.empresa_id = :id ORDER BY m.fecha_hora DESC LIMIT 100", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>👆 Marcajes</h1>
        <p>Registro de asistencia de empleados</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary" onclick="exportarMarcajes()">📊 Exportar</button>
        <button class="btn btn-primary" onclick="nuevoMarcaje()">➕ Registrar Marcaje</button>
    </div>
</div>

<!-- Filtros -->
<div class="card">
    <div class="card-body">
        <div class="filters">
            <input type="date" id="filtroFecha" value="<?= date('Y-m-d') ?>" onchange="filtrar()">
            <select id="filtroTipo" onchange="filtrar()">
                <option value="">Todos los tipos</option>
                <option value="entrada">Entrada</option>
                <option value="salida">Salida</option>
                <option value="pausa">Pausa</option>
            </select>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>📋 Marcajes del Día</h3>
    </div>
    <div class="card-body">
        <?php if (empty($marcajes)): ?>
            <div class="empty-state">
                <span class="icon">👆</span>
                <h3>No hay marcajes registrados</h3>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>Empleado</th><th>Fecha/Hora</th><th>Tipo</th><th>Dispositivo</th><th>Ubicación</th><th>Estado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($marcajes as $m): ?>
                            <tr>
                                <td><strong><?= e($m['empleado_nombre']) ?></strong></td>
                                <td><?= e(date('d/m/Y H:i:s', strtotime($m['fecha_hora']))) ?></td>
                                <td><span class="badge badge-<?= getBadgeType($m['tipo']) ?>"><?= e(ucfirst($m['tipo'])) ?></span></td>
                                <td><?= e($m['dispositivo_nombre'] ?? 'Manual') ?></td>
                                <td><?= e($m['ubicacion'] ?? '-') ?></td>
                                <td><span class="badge badge-<?= $m['validado'] ? 'success' : 'warning' ?>"><?= $m['validado'] ? 'Validado' : 'Pendiente' ?></span></td>
                                <td class="actions">
                                    <button class="btn-icon" onclick="validarMarcaje(<?= $m['id'] ?>)" title="Validar">✓</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
function getBadgeType($tipo) {
    return $tipo == 'entrada' ? 'success' : ($tipo == 'salida' ? 'danger' : 'warning');
}
?>
<script>
function nuevoMarcaje() { alert('Nuevo marcaje'); }
function exportarMarcajes() { window.location.href = '/app/api/reloj/marcajes.php?action=export'; }
function filtrar() {
    const fecha = document.getElementById('filtroFecha').value;
    const tipo = document.getElementById('filtroTipo').value;
    window.location.href = '?module=reloj&sub=marcajes&fecha=' + fecha + '&tipo=' + tipo;
}
function validarMarcaje(id) {
    fetch('/app/api/reloj/marcajes.php?action=validar&id=' + id, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) location.reload();
    });
}
</script>
