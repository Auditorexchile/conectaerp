<?php
/**
 * CONECTA ERP - WEBHOOKS
 * Configuración de webhooks para notificaciones en tiempo real
 */

if (!defined('CONECTA_ERP')) {
    die('Acceso no autorizado');
}

$empresaId = Session::get('empresa_id');
$pageTitle = 'Webhooks';

$webhooks = db()->select(
    "SELECT * FROM webhooks WHERE empresa_id = :empresa_id ORDER BY created_at DESC",
    ['empresa_id' => $empresaId]
);
?>

<div class="api-container">
    <div class="page-header">
        <div class="page-title">
            <h1>🪝 Webhooks</h1>
            <p>Recibe notificaciones automáticas cuando ocurran eventos en tu sistema</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-primary" onclick="showModal('modalNuevoWebhook')">
                <span class="icon">➕</span>
                Nuevo Webhook
            </button>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>ℹ️ ¿Qué son los Webhooks?</strong><br>
        Los webhooks te permiten recibir notificaciones HTTP POST en tiempo real cuando ocurren eventos específicos
        (nueva factura, pago recibido, stock bajo, etc.)
    </div>

    <div class="card">
        <div class="card-header">
            <h3>📋 Webhooks Configurados</h3>
        </div>
        <div class="card-body">
            <?php if (empty($webhooks)): ?>
                <div class="empty-state">
                    <span class="icon">🪝</span>
                    <h3>No tienes webhooks configurados</h3>
                    <p>Configura tu primer webhook para recibir notificaciones automáticas</p>
                    <button class="btn btn-primary" onclick="showModal('modalNuevoWebhook')">
                        Crear Webhook
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>URL</th>
                                <th>Eventos</th>
                                <th>Último Envío</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($webhooks as $webhook): ?>
                                <tr>
                                    <td><strong><?= e($webhook['nombre']) ?></strong></td>
                                    <td><code><?= e($webhook['url']) ?></code></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?= e($webhook['eventos'] ?? 'Todos') ?>
                                        </span>
                                    </td>
                                    <td><?= $webhook['last_triggered_at'] ? e(date('d/m/Y H:i', strtotime($webhook['last_triggered_at']))) : 'Nunca' ?></td>
                                    <td>
                                        <span class="badge <?= $webhook['activo'] ? 'badge-success' : 'badge-danger' ?>">
                                            <?= $webhook['activo'] ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <button class="btn-icon" onclick="testWebhook(<?= $webhook['id'] ?>)" title="Probar">
                                            🧪
                                        </button>
                                        <button class="btn-icon" onclick="eliminarWebhook(<?= $webhook['id'] ?>)" title="Eliminar">
                                            🗑️
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

    <!-- Eventos Disponibles -->
    <div class="card">
        <div class="card-header">
            <h3>📬 Eventos Disponibles</h3>
        </div>
        <div class="card-body">
            <div class="events-grid">
                <div class="event-item">
                    <strong>factura.creada</strong>
                    <p>Se dispara cuando se crea una nueva factura</p>
                </div>
                <div class="event-item">
                    <strong>pago.recibido</strong>
                    <p>Se dispara cuando se registra un pago</p>
                </div>
                <div class="event-item">
                    <strong>producto.stock_bajo</strong>
                    <p>Se dispara cuando un producto tiene stock bajo</p>
                </div>
                <div class="event-item">
                    <strong>pedido.creado</strong>
                    <p>Se dispara cuando se crea un nuevo pedido</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Webhook -->
<div id="modalNuevoWebhook" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🪝 Nuevo Webhook</h3>
            <button class="modal-close" onclick="closeModal('modalNuevoWebhook')">&times;</button>
        </div>
        <form id="formNuevoWebhook" onsubmit="guardarWebhook(event)">
            <div class="modal-body">
                <div class="form-group">
                    <label for="nombre">Nombre <span class="required">*</span></label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ej: Notificación Slack Ventas">
                </div>

                <div class="form-group">
                    <label for="url">URL del Webhook <span class="required">*</span></label>
                    <input type="url" id="url" name="url" required placeholder="https://tusistema.com/webhook">
                    <small class="form-hint">Esta URL recibirá las notificaciones POST</small>
                </div>

                <div class="form-group">
                    <label for="eventos">Eventos</label>
                    <select id="eventos" name="eventos">
                        <option value="*">Todos los eventos</option>
                        <option value="factura">Facturas</option>
                        <option value="pago">Pagos</option>
                        <option value="producto">Productos</option>
                        <option value="pedido">Pedidos</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="secret">Secret (opcional)</label>
                    <input type="text" id="secret" name="secret" placeholder="Token secreto para validar firma">
                    <small class="form-hint">Se enviará en el header X-Webhook-Signature</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalNuevoWebhook')">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    Crear Webhook
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function guardarWebhook(e) {
    e.preventDefault();
    const formData = new FormData(e.target);

    fetch('/app/api/webhooks.php?action=create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Webhook creado exitosamente');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function testWebhook(id) {
    fetch('/app/api/webhooks.php?action=test&id=' + id, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success ? 'Webhook enviado exitosamente ✓' : 'Error al enviar: ' + data.message);
    });
}

function eliminarWebhook(id) {
    if (confirm('¿Eliminar este webhook?')) {
        fetch('/app/api/webhooks.php?action=delete&id=' + id, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) location.reload();
        });
    }
}

window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
    }
});
</script>

<style>
.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.event-item {
    padding: 1rem;
    background: var(--bg-tertiary, #f3f4f6);
    border-radius: 6px;
    border-left: 3px solid var(--primary, #3b82f6);
}

.event-item strong {
    color: var(--primary, #3b82f6);
    font-family: monospace;
}

.event-item p {
    margin: 0.5rem 0 0 0;
    font-size: 0.9rem;
    color: var(--text-secondary, #6b7280);
}
</style>
