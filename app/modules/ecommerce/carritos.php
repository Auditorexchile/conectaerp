<?php
/**
 * CONECTA ERP - CARRITOS ABANDONADOS
 * Seguimiento de carritos de compra abandonados
 */

if (!defined('CONECTA_ERP')) {
    die('Acceso no autorizado');
}

$empresaId = Session::get('empresa_id');
$pageTitle = 'Carritos Abandonados';

$carritos = db()->select(
    "SELECT cc.*, c.razon_social as cliente_nombre
     FROM carritos_compra cc
     LEFT JOIN clientes c ON cc.cliente_id = c.id
     WHERE cc.empresa_id = :empresa_id
     AND cc.estado = 'abandonado'
     ORDER BY cc.updated_at DESC
     LIMIT 50",
    ['empresa_id' => $empresaId]
);
?>

<div class="ecommerce-container">
    <div class="page-header">
        <div class="page-title">
            <h1>🛒 Carritos Abandonados</h1>
            <p>Recupera ventas enviando recordatorios automáticos</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-primary" onclick="enviarRecordatorios()">
                <span class="icon">📧</span>
                Enviar Recordatorios
            </button>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon">🛒</div>
            <div class="metric-content">
                <div class="metric-label">Carritos Abandonados</div>
                <div class="metric-value"><?= count($carritos) ?></div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon">💰</div>
            <div class="metric-content">
                <div class="metric-label">Valor Potencial</div>
                <div class="metric-value">$<?= e(number_format(array_sum(array_column($carritos, 'total')), 0, ',', '.')) ?></div>
            </div>
        </div>
    </div>

    <!-- Tabla de Carritos -->
    <div class="card">
        <div class="card-header">
            <h3>📋 Carritos Abandonados</h3>
        </div>
        <div class="card-body">
            <?php if (empty($carritos)): ?>
                <div class="empty-state">
                    <span class="icon">✓</span>
                    <h3>No hay carritos abandonados</h3>
                    <p>Excelente, todos tus clientes están completando sus compras</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Email</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Abandonado hace</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($carritos as $carrito): ?>
                                <tr>
                                    <td><?= e($carrito['cliente_nombre'] ?? 'Invitado') ?></td>
                                    <td><?= e($carrito['email'] ?? 'N/A') ?></td>
                                    <td><?= e($carrito['items_count'] ?? 0) ?> productos</td>
                                    <td><strong>$<?= e(number_format($carrito['total'], 0, ',', '.')) ?></strong></td>
                                    <td><?= e(timeAgo($carrito['updated_at'])) ?></td>
                                    <td class="actions">
                                        <button class="btn-icon" onclick="enviarRecordatorio(<?= $carrito['id'] ?>)" title="Enviar Email">
                                            📧
                                        </button>
                                        <button class="btn-icon" onclick="verCarrito(<?= $carrito['id'] ?>)" title="Ver Detalle">
                                            👁️
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
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 3600) return floor($diff / 60) . ' min';
    if ($diff < 86400) return floor($diff / 3600) . ' horas';
    return floor($diff / 86400) . ' días';
}
?>

<script>
function enviarRecordatorios() {
    if (confirm('¿Enviar emails de recordatorio a todos los carritos abandonados?')) {
        fetch('/app/api/ecommerce/carritos.php?action=send_reminders', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            alert(data.success ? 'Recordatorios enviados: ' + data.count : 'Error: ' + data.message);
        });
    }
}

function enviarRecordatorio(id) {
    fetch('/app/api/ecommerce/carritos.php?action=send_reminder&id=' + id, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success ? 'Recordatorio enviado' : 'Error: ' + data.message);
    });
}

function verCarrito(id) {
    alert('Ver detalle del carrito ID: ' + id);
}
</script>
