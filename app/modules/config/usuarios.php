<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Usuarios';
$usuarios = db()->select("SELECT u.*, r.nombre as rol_nombre FROM usuarios u LEFT JOIN roles r ON u.rol_id = r.id WHERE u.empresa_id = :id ORDER BY u.nombre ASC", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>👥 Usuarios del Sistema</h1>
        <p>Gestión de usuarios y permisos</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="nuevoUsuario()">➕ Nuevo Usuario</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($usuarios)): ?>
            <div class="empty-state">
                <span class="icon">👥</span>
                <h3>No hay usuarios</h3>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Último Acceso</th><th>Estado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><strong><?= e($u['nombre']) ?></strong></td>
                                <td><?= e($u['email']) ?></td>
                                <td><span class="badge badge-info"><?= e($u['rol_nombre'] ?? 'Usuario') ?></span></td>
                                <td><?= $u['last_login_at'] ? e(date('d/m/Y H:i', strtotime($u['last_login_at']))) : 'Nunca' ?></td>
                                <td><span class="badge badge-<?= $u['activo'] ? 'success' : 'danger' ?>"><?= $u['activo'] ? 'Activo' : 'Inactivo' ?></span></td>
                                <td class="actions">
                                    <button class="btn-icon" onclick="editarUsuario(<?= $u['id'] ?>)">✏️</button>
                                    <button class="btn-icon" onclick="cambiarPassword(<?= $u['id'] ?>)">🔑</button>
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
function nuevoUsuario() { alert('Nuevo usuario'); }
function editarUsuario(id) { alert('Editar usuario ' + id); }
function cambiarPassword(id) { alert('Cambiar password usuario ' + id); }
</script>
