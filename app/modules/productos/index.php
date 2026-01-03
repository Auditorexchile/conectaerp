<?php
// Módulo: Productos

$empresaId = Session::getEmpresaId();

// Obtener productos
$productos = db()->select(
    "SELECT *
     FROM productos
     WHERE empresa_id = :empresa_id
     ORDER BY nombre",
    ['empresa_id' => $empresaId]
);
?>

<div class="page-header">
    <h1>Productos</h1>
    <div class="page-actions">
        <button class="btn-primary" onclick="window.location.href='?module=productos&view=crear'">
            + Nuevo Producto
        </button>
    </div>
</div>

<?php if (getFlash('success')): ?>
    <div class="alert alert-success"><?php echo getFlash('success'); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Catálogo de Productos</h3>
        <input type="text" placeholder="Buscar producto..." class="form-control" id="searchInput">
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Precio Venta</th>
                    <th>Stock Actual</th>
                    <th>Stock Mínimo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($productos)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">No hay productos registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo e($producto['codigo']); ?></td>
                            <td><?php echo e($producto['nombre']); ?></td>
                            <td><?php echo ucfirst($producto['tipo']); ?></td>
                            <td><?php echo formatCurrency($producto['precio_venta']); ?></td>
                            <td>
                                <?php
                                $class = $producto['stock_actual'] <= $producto['stock_minimo'] ? 'text-error' : '';
                                ?>
                                <span class="<?php echo $class; ?>">
                                    <?php echo formatNumber($producto['stock_actual'], 2); ?>
                                </span>
                            </td>
                            <td><?php echo formatNumber($producto['stock_minimo'], 2); ?></td>
                            <td>
                                <?php if ($producto['activo']): ?>
                                    <span class="badge badge-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-error">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?module=productos&view=editar&id=<?php echo $producto['id']; ?>" class="btn-sm">Editar</a>
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
.text-error { color: var(--error); font-weight: 600; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.card { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.card-header { padding: 20px; border-bottom: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center; }
.card-body { padding: 20px; }
.table { width: 100%; border-collapse: collapse; }
.table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid var(--gray-200); }
.table th { font-weight: 600; }
.btn-sm { padding: 6px 12px; font-size: 12px; }
</style>
