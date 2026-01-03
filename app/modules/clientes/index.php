<?php
// Módulo: Clientes

$empresaId = Session::getEmpresaId();

// Obtener clientes
$clientes = db()->select(
    "SELECT a.*, COUNT(DISTINCT v.id) as total_ventas
     FROM auxiliares a
     LEFT JOIN ventas v ON a.id = v.cliente_id
     WHERE a.empresa_id = :empresa_id
     AND a.tipo_id = (SELECT id FROM auxiliares_tipos WHERE codigo = 'cliente')
     GROUP BY a.id
     ORDER BY a.razon_social",
    ['empresa_id' => $empresaId]
);
?>

<div class="page-header">
    <h1>Clientes</h1>
    <div class="page-actions">
        <button class="btn-primary" onclick="window.location.href='?module=clientes&view=crear'">
            + Nuevo Cliente
        </button>
    </div>
</div>

<?php if (getFlash('success')): ?>
    <div class="alert alert-success"><?php echo getFlash('success'); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Lista de Clientes</h3>
        <div class="card-actions">
            <input type="text" placeholder="Buscar..." class="form-control" id="searchInput">
        </div>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>RUT</th>
                    <th>Razón Social</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Total Ventas</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clientes)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No hay clientes registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?php echo e($cliente['rut']); ?></td>
                            <td><?php echo e($cliente['razon_social']); ?></td>
                            <td><?php echo e($cliente['email']); ?></td>
                            <td><?php echo e($cliente['telefono']); ?></td>
                            <td><?php echo $cliente['total_ventas']; ?></td>
                            <td>
                                <?php if ($cliente['activo']): ?>
                                    <span class="badge badge-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-error">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?module=clientes&view=editar&id=<?php echo $cliente['id']; ?>" class="btn-sm">Editar</a>
                                <a href="?module=clientes&view=ver&id=<?php echo $cliente['id']; ?>" class="btn-sm">Ver</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});
</script>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.card-header {
    padding: 20px;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.card-body {
    padding: 20px;
}
.table {
    width: 100%;
    border-collapse: collapse;
}
.table th,
.table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid var(--gray-200);
}
.table th {
    font-weight: 600;
    color: var(--gray-900);
}
.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
    margin-right: 4px;
}
</style>
