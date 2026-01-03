<?php
// Módulo: Ventas

$empresaId = Session::getEmpresaId();

// Obtener ventas
$ventas = db()->select(
    "SELECT v.*, a.razon_social as cliente_nombre
     FROM ventas v
     LEFT JOIN auxiliares a ON v.cliente_id = a.id
     WHERE v.empresa_id = :empresa_id
     ORDER BY v.fecha DESC
     LIMIT 50",
    ['empresa_id' => $empresaId]
);
?>

<div class="page-header">
    <h1>Ventas</h1>
    <div class="page-actions">
        <button class="btn-primary" onclick="alert('Funcionalidad próximamente')">
            + Nueva Venta
        </button>
    </div>
</div>

<?php if (getFlash('success')): ?>
    <div class="alert alert-success"><?php echo getFlash('success'); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Registro de Ventas</h3>
        <div class="card-actions">
            <select class="form-control" style="width: 200px;">
                <option value="">Todos los estados</option>
                <option value="borrador">Borrador</option>
                <option value="confirmada">Confirmada</option>
                <option value="facturada">Facturada</option>
                <option value="anulada">Anulada</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Subtotal</th>
                    <th>Impuesto</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ventas)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">No hay ventas registradas</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ventas as $venta): ?>
                        <tr>
                            <td><?php echo e($venta['numero']); ?></td>
                            <td><?php echo formatDate($venta['fecha']); ?></td>
                            <td><?php echo e($venta['cliente_nombre']); ?></td>
                            <td><?php echo formatCurrency($venta['subtotal']); ?></td>
                            <td><?php echo formatCurrency($venta['impuesto']); ?></td>
                            <td><strong><?php echo formatCurrency($venta['total']); ?></strong></td>
                            <td>
                                <?php
                                $badgeClass = 'badge-warning';
                                if ($venta['estado'] === 'confirmada') $badgeClass = 'badge-success';
                                if ($venta['estado'] === 'anulada') $badgeClass = 'badge-error';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo ucfirst($venta['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn-sm" onclick="alert('Ver detalle')">Ver</button>
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
.card-header { padding: 20px; border-bottom: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center; }
.card-body { padding: 20px; }
.table { width: 100%; border-collapse: collapse; }
.table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid var(--gray-200); }
.table th { font-weight: 600; }
.btn-sm { padding: 6px 12px; font-size: 12px; }
</style>
