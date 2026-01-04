<?php
/**
 * CONECTA ERP - PEDIDOS WEB
 * Gestión de pedidos del ecommerce
 */

if (!defined('CONECTA_ERP')) {
    die('Acceso no autorizado');
}

$empresaId = Session::get('empresa_id');
$pageTitle = 'Pedidos Web';

$pedidos = db()->select(
    "SELECT po.*, c.razon_social as cliente_nombre
     FROM pedidos_web po
     LEFT JOIN clientes c ON po.cliente_id = c.id
     WHERE po.empresa_id = :empresa_id
     ORDER BY po.created_at DESC
     LIMIT 100",
    ['empresa_id' => $empresaId]
);
?>

<div class="ecommerce-container">
    <div class="page-header">
        <div class="page-title">
            <h1>📦 Pedidos Web</h1>
            <p>Gestiona los pedidos recibidos desde tu tienda online</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-secondary" onclick="exportarPedidos()">
                <span class="icon">📊</span>
                Exportar
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card">
        <div class="card-body">
            <div class="filters">
                <select id="filtroEstado" onchange="filtrarPedidos()">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="procesando">Procesando</option>
                    <option value="enviado">Enviado</option>
                    <option value="entregado">Entregado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
                <input type="date" id="filtroFecha" onchange="filtrarPedidos()">
            </div>
        </div>
    </div>

    <!-- Tabla de Pedidos -->
    <div class="card">
        <div class="card-header">
            <h3>📋 Lista de Pedidos</h3>
        </div>
        <div class="card-body">
            <?php if (empty($pedidos)): ?>
                <div class="empty-state">
                    <span class="icon">📦</span>
                    <h3>No hay pedidos web</h3>
                    <p>Los pedidos de tu tienda online aparecerán aquí</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>N° Pedido</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Pago</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td><strong>#<?= e($pedido['numero_pedido'] ?? $pedido['id']) ?></strong></td>
                                    <td><?= e(date('d/m/Y H:i', strtotime($pedido['created_at']))) ?></td>
                                    <td><?= e($pedido['cliente_nombre'] ?? 'Invitado') ?></td>
                                    <td><strong>$<?= e(number_format($pedido['total'], 0, ',', '.')) ?></strong></td>
                                    <td>
                                        <span class="badge badge-<?= getBadgeClass($pedido['estado']) ?>">
                                            <?= e(ucfirst($pedido['estado'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $pedido['pagado'] ? 'badge-success' : 'badge-warning' ?>">
                                            <?= $pedido['pagado'] ? 'Pagado' : 'Pendiente' ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <button class="btn-icon" onclick="verPedido(<?= $pedido['id'] ?>)" title="Ver">
                                            👁️
                                        </button>
                                        <button class="btn-icon" onclick="procesarPedido(<?= $pedido['id'] ?>)" title="Procesar">
                                            ✓
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
function getBadgeClass($estado) {
    $classes = [
        'pendiente' => 'warning',
        'procesando' => 'info',
        'enviado' => 'primary',
        'entregado' => 'success',
        'cancelado' => 'danger'
    ];
    return $classes[$estado] ?? 'secondary';
}
?>

<script>
function filtrarPedidos() {
    const estado = document.getElementById('filtroEstado').value;
    const fecha = document.getElementById('filtroFecha').value;
    // Implementar filtrado
    location.href = '?module=ecommerce&sub=pedidos&estado=' + estado + '&fecha=' + fecha;
}

function verPedido(id) {
    alert('Ver detalle del pedido ID: ' + id);
}

function procesarPedido(id) {
    if (confirm('¿Marcar pedido como procesado?')) {
        fetch('/app/api/ecommerce/pedidos.php?action=procesar&id=' + id, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Pedido procesado');
                location.reload();
            }
        });
    }
}

function exportarPedidos() {
    window.location.href = '/app/api/ecommerce/pedidos.php?action=export';
}
</script>
