<?php
/**
 * CONECTA ERP - GESTIÓN DE ENVÍOS
 * Configuración de métodos de envío (Chilexpress, Starken, etc.)
 */

if (!defined('CONECTA_ERP')) {
    die('Acceso no autorizado');
}

$empresaId = Session::get('empresa_id');
$paisCodigo = Session::get('pais_codigo', 'CL');
$pageTitle = 'Gestión de Envíos';

$metodosEnvio = db()->select(
    "SELECT * FROM envios WHERE empresa_id = :empresa_id ORDER BY nombre ASC",
    ['empresa_id' => $empresaId]
);
?>

<div class="ecommerce-container">
    <div class="page-header">
        <div class="page-title">
            <h1>🚚 Gestión de Envíos</h1>
            <p>Configura los métodos de envío para tu tienda</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-primary" onclick="showModal('modalNuevoEnvio')">
                <span class="icon">➕</span>
                Agregar Método de Envío
            </button>
        </div>
    </div>

    <!-- Courier Disponibles -->
    <div class="card">
        <div class="card-header">
            <h3>📦 Couriers Disponibles (<?= strtoupper($paisCodigo) ?>)</h3>
        </div>
        <div class="card-body">
            <div class="courier-grid">
                <?php if ($paisCodigo === 'CL'): ?>
                    <div class="courier-card">
                        <h4>📮 Chilexpress</h4>
                        <p>Integración con API de Chilexpress</p>
                        <button class="btn btn-sm btn-primary" onclick="configurarCourier('chilexpress')">Configurar</button>
                    </div>
                    <div class="courier-card">
                        <h4>⭐ Starken</h4>
                        <p>Integración con API de Starken</p>
                        <button class="btn btn-sm btn-primary" onclick="configurarCourier('starken')">Configurar</button>
                    </div>
                    <div class="courier-card">
                        <h4>📫 Correos de Chile</h4>
                        <p>Servicio postal nacional</p>
                        <button class="btn btn-sm btn-primary" onclick="configurarCourier('correos')">Configurar</button>
                    </div>
                <?php endif; ?>
                <div class="courier-card">
                    <h4>🏪 Retiro en Tienda</h4>
                    <p>Permite al cliente retirar en tienda</p>
                    <button class="btn btn-sm btn-primary" onclick="configurarCourier('retiro')">Configurar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Métodos Configurados -->
    <div class="card">
        <div class="card-header">
            <h3>✓ Métodos de Envío Configurados</h3>
        </div>
        <div class="card-body">
            <?php if (empty($metodosEnvio)): ?>
                <div class="empty-state">
                    <span class="icon">🚚</span>
                    <h3>No tienes métodos de envío configurados</h3>
                    <p>Configura al menos un método para entregar tus productos</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Costo</th>
                                <th>Tiempo Estimado</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($metodosEnvio as $envio): ?>
                                <tr>
                                    <td><strong><?= e($envio['nombre']) ?></strong></td>
                                    <td><?= e(ucfirst($envio['tipo'] ?? 'courier')) ?></td>
                                    <td>$<?= e(number_format($envio['costo_base'], 0, ',', '.')) ?></td>
                                    <td><?= e($envio['tiempo_entrega'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge <?= $envio['activo'] ? 'badge-success' : 'badge-danger' ?>">
                                            <?= $envio['activo'] ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <button class="btn-icon" onclick="editarEnvio(<?= $envio['id'] ?>)" title="Editar">
                                            ✏️
                                        </button>
                                        <button class="btn-icon" onclick="eliminarEnvio(<?= $envio['id'] ?>)" title="Eliminar">
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
</div>

<script>
function showModal(modalId) {
    alert('Modal no implementado aún');
}

function configurarCourier(courier) {
    alert('Configurando ' + courier + '...');
}

function editarEnvio(id) {
    alert('Editar envío ID: ' + id);
}

function eliminarEnvio(id) {
    if (confirm('¿Eliminar este método de envío?')) {
        fetch('/app/api/ecommerce/envios.php?action=delete&id=' + id, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) location.reload();
        });
    }
}
</script>

<style>
.courier-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.courier-card {
    padding: 1.5rem;
    background: var(--bg-tertiary, #f3f4f6);
    border-radius: 8px;
    border: 1px solid var(--border-color, #e5e7eb);
    text-align: center;
}

.courier-card h4 {
    margin: 0 0 0.5rem 0;
}

.courier-card p {
    margin: 0 0 1rem 0;
    font-size: 0.9rem;
    color: var(--text-secondary);
}
</style>
