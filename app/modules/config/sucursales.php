<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Sucursales';
$sucursales = db()->select("SELECT * FROM sucursales WHERE empresa_id = :id ORDER BY nombre ASC", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>🏪 Sucursales</h1>
        <p>Gestión de sucursales y puntos de venta</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="nuevaSucursal()">➕ Nueva Sucursal</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($sucursales)): ?>
            <div class="empty-state">
                <span class="icon">🏪</span>
                <h3>No hay sucursales</h3>
                <p>Agrega tu primera sucursal</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>Nombre</th><th>Código</th><th>Dirección</th><th>Ciudad</th><th>Teléfono</th><th>Estado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($sucursales as $s): ?>
                            <tr>
                                <td><strong><?= e($s['nombre']) ?></strong></td>
                                <td><?= e($s['codigo']) ?></td>
                                <td><?= e($s['direccion']) ?></td>
                                <td><?= e($s['ciudad']) ?></td>
                                <td><?= e($s['telefono']) ?></td>
                                <td><span class="badge badge-<?= $s['activo'] ? 'success' : 'danger' ?>"><?= $s['activo'] ? 'Activa' : 'Inactiva' ?></span></td>
                                <td class="actions">
                                    <button class="btn-icon" onclick="editarSucursal(<?= $s['id'] ?>)">✏️</button>
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
function nuevaSucursal() { alert('Nueva sucursal'); }
function editarSucursal(id) { alert('Editar sucursal ' + id); }
</script>
