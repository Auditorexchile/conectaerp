<?php
/**
 * CONECTA ERP - MÉTODOS DE PAGO ONLINE
 * Configuración de pasarelas de pago (Transbank, Flow, Mercado Pago, etc.)
 */

if (!defined('CONECTA_ERP')) {
    die('Acceso no autorizado');
}

$empresaId = Session::get('empresa_id');
$paisCodigo = Session::get('pais_codigo', 'CL');
$pageTitle = 'Métodos de Pago Online';

$metodosPago = db()->select(
    "SELECT * FROM metodos_pago_online WHERE empresa_id = :empresa_id ORDER BY orden ASC",
    ['empresa_id' => $empresaId]
);
?>

<div class="ecommerce-container">
    <div class="page-header">
        <div class="page-title">
            <h1>💳 Métodos de Pago Online</h1>
            <p>Configura las pasarelas de pago para tu tienda</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-primary" onclick="showModal('modalNuevoMetodo')">
                <span class="icon">➕</span>
                Agregar Método de Pago
            </button>
        </div>
    </div>

    <!-- Pasarelas Disponibles por País -->
    <div class="card">
        <div class="card-header">
            <h3>🌍 Pasarelas Disponibles (<?= strtoupper($paisCodigo) ?>)</h3>
        </div>
        <div class="card-body">
            <div class="payment-gateways-grid">
                <?php if ($paisCodigo === 'CL'): ?>
                    <div class="gateway-card">
                        <h4>🏦 Transbank</h4>
                        <p>Webpay Plus - La solución más usada en Chile</p>
                        <button class="btn btn-sm btn-primary" onclick="configurarPasarela('transbank')">Configurar</button>
                    </div>
                    <div class="gateway-card">
                        <h4>💳 Flow</h4>
                        <p>Acepta tarjetas, transferencias y más</p>
                        <button class="btn btn-sm btn-primary" onclick="configurarPasarela('flow')">Configurar</button>
                    </div>
                    <div class="gateway-card">
                        <h4>📱 Mercado Pago</h4>
                        <p>Checkout completo con múltiples medios</p>
                        <button class="btn btn-sm btn-primary" onclick="configurarPasarela('mercadopago')">Configurar</button>
                    </div>
                <?php elseif ($paisCodigo === 'AR'): ?>
                    <div class="gateway-card">
                        <h4>📱 Mercado Pago</h4>
                        <p>Líder en Argentina</p>
                        <button class="btn btn-sm btn-primary" onclick="configurarPasarela('mercadopago')">Configurar</button>
                    </div>
                <?php elseif ($paisCodigo === 'MX'): ?>
                    <div class="gateway-card">
                        <h4>📱 Mercado Pago</h4>
                        <p>Líder en México</p>
                        <button class="btn btn-sm btn-primary" onclick="configurarPasarela('mercadopago')">Configurar</button>
                    </div>
                    <div class="gateway-card">
                        <h4>💳 Conekta</h4>
                        <p>Pasarela mexicana</p>
                        <button class="btn btn-sm btn-primary" onclick="configurarPasarela('conekta')">Configurar</button>
                    </div>
                <?php endif; ?>
                <div class="gateway-card">
                    <h4>💳 Stripe</h4>
                    <p>Pasarela global</p>
                    <button class="btn btn-sm btn-primary" onclick="configurarPasarela('stripe')">Configurar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Métodos Configurados -->
    <div class="card">
        <div class="card-header">
            <h3>✓ Métodos Configurados</h3>
        </div>
        <div class="card-body">
            <?php if (empty($metodosPago)): ?>
                <div class="empty-state">
                    <span class="icon">💳</span>
                    <h3>No tienes métodos de pago configurados</h3>
                    <p>Configura al menos un método para recibir pagos online</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Pasarela</th>
                                <th>Nombre</th>
                                <th>Comisión</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($metodosPago as $metodo): ?>
                                <tr>
                                    <td><strong><?= e(ucfirst($metodo['pasarela'])) ?></strong></td>
                                    <td><?= e($metodo['nombre']) ?></td>
                                    <td><?= e($metodo['comision_porcentaje']) ?>%</td>
                                    <td>
                                        <span class="badge <?= $metodo['activo'] ? 'badge-success' : 'badge-danger' ?>">
                                            <?= $metodo['activo'] ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <button class="btn-icon" onclick="editarMetodo(<?= $metodo['id'] ?>)" title="Editar">
                                            ⚙️
                                        </button>
                                        <button class="btn-icon" onclick="testearPasarela(<?= $metodo['id'] ?>)" title="Probar">
                                            🧪
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
    document.getElementById(modalId)?.style.display = 'flex';
}

function configurarPasarela(pasarela) {
    alert('Configurando ' + pasarela + '...\n\nRedirigiendo a configuración.');
    // Redirigir a configuración específica
}

function editarMetodo(id) {
    alert('Editar método de pago ID: ' + id);
}

function testearPasarela(id) {
    fetch('/app/api/ecommerce/metodos_pago.php?action=test&id=' + id, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success ? 'Prueba exitosa ✓' : 'Error en la prueba: ' + data.message);
    });
}
</script>

<style>
.payment-gateways-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.gateway-card {
    padding: 1.5rem;
    background: var(--bg-tertiary, #f3f4f6);
    border-radius: 8px;
    border: 1px solid var(--border-color, #e5e7eb);
    text-align: center;
}

.gateway-card h4 {
    margin: 0 0 0.5rem 0;
    color: var(--text-primary);
}

.gateway-card p {
    margin: 0 0 1rem 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
}
</style>
