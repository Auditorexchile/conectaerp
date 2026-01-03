<?php
// Módulo: Usuarios

// Solo superadmin y admin pueden ver esto
if (!Session::isSuperAdmin() && Session::get('tipo_usuario') !== 'admin') {
    setFlash('error', 'No tienes permisos para acceder a este módulo');
    redirect('/app/router.php?module=dashboard');
}

$empresaId = Session::getEmpresaId();

// Obtener usuarios
$usuarios = db()->select(
    "SELECT u.*, ut.nombre as tipo_nombre, ue.nombre as estado_nombre
     FROM usuarios_acceso u
     JOIN usuario_tipo ut ON u.tipo_usuario_id = ut.id
     JOIN usuario_estado ue ON u.estado_id = ue.id
     WHERE u.empresa_id = :empresa_id
     ORDER BY u.nombre",
    ['empresa_id' => $empresaId]
);
?>

<div class="page-header">
    <h1>Gestión de Usuarios</h1>
    <div class="page-actions">
        <button class="btn-primary" onclick="alert('Funcionalidad de creación de usuario próximamente')">
            + Nuevo Usuario
        </button>
    </div>
</div>

<?php if (getFlash('success')): ?>
    <div class="alert alert-success"><?php echo getFlash('success'); ?></div>
<?php endif; ?>

<?php if (getFlash('error')): ?>
    <div class="alert alert-error"><?php echo getFlash('error'); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Usuarios del Sistema</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Último Acceso</th>
                    <th>2FA</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No hay usuarios registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo e($usuario['nombre']); ?></td>
                            <td><?php echo e($usuario['email']); ?></td>
                            <td>
                                <span class="badge badge-success">
                                    <?php echo e($usuario['tipo_nombre']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($usuario['estado_nombre'] === 'Activo'): ?>
                                    <span class="badge badge-success">Activo</span>
                                <?php elseif ($usuario['estado_nombre'] === 'Bloqueado'): ?>
                                    <span class="badge badge-error">Bloqueado</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><?php echo e($usuario['estado_nombre']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $usuario['ultimo_acceso'] ? formatDateTime($usuario['ultimo_acceso']) : 'Nunca'; ?>
                            </td>
                            <td>
                                <?php if ($usuario['mfa_enabled']): ?>
                                    <span class="badge badge-success">✓ Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">✗ Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($usuario['id'] !== Session::getUserId()): ?>
                                    <button class="btn-sm" onclick="alert('Funcionalidad próximamente')">Editar</button>
                                <?php else: ?>
                                    <span class="text-muted">Usuario actual</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.card { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.card-header { padding: 20px; border-bottom: 1px solid var(--gray-200); }
.card-body { padding: 20px; }
.table { width: 100%; border-collapse: collapse; }
.table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid var(--gray-200); }
.table th { font-weight: 600; }
.btn-sm { padding: 6px 12px; font-size: 12px; }
</style>
