<?php
/**
 * CONECTA ERP - GESTIÓN DE TOKENS API
 * Administración de tokens de acceso a la API REST
 */

if (!defined('CONECTA_ERP')) {
    die('Acceso no autorizado');
}

$empresaId = Session::get('empresa_id');
$userId = Session::get('user_id');
$pageTitle = 'Tokens API';

// Obtener tokens del usuario
$tokens = db()->select(
    "SELECT * FROM api_tokens
     WHERE empresa_id = :empresa_id
     ORDER BY created_at DESC",
    ['empresa_id' => $empresaId]
);
?>

<div class="api-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title">
            <h1>🔑 Tokens API</h1>
            <p>Administra los tokens de acceso a la API REST de Conecta ERP</p>
        </div>
        <div class="page-actions">
            <a href="/app/router.php?module=api&sub=documentacion" class="btn btn-secondary">
                <span class="icon">📚</span>
                Ver Documentación
            </a>
            <button class="btn btn-primary" onclick="showModal('modalNuevoToken')">
                <span class="icon">➕</span>
                Generar Token
            </button>
        </div>
    </div>

    <!-- Información de Seguridad -->
    <div class="alert alert-warning">
        <strong>⚠️ Seguridad:</strong> Los tokens permiten acceso completo a tu cuenta.
        No compartas tus tokens y revócalos si crees que están comprometidos.
    </div>

    <!-- Tabla de Tokens -->
    <div class="card">
        <div class="card-header">
            <h3>🔐 Tokens Activos</h3>
        </div>
        <div class="card-body">
            <?php if (empty($tokens)): ?>
                <div class="empty-state">
                    <span class="icon">🔑</span>
                    <h3>No tienes tokens API</h3>
                    <p>Genera tu primer token para comenzar a usar la API</p>
                    <button class="btn btn-primary" onclick="showModal('modalNuevoToken')">
                        Generar Token
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Token</th>
                                <th>Permisos</th>
                                <th>Último Uso</th>
                                <th>Expira</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tokens as $token): ?>
                                <tr>
                                    <td><strong><?= e($token['nombre']) ?></strong></td>
                                    <td>
                                        <code class="token-masked" data-token="<?= e($token['token']) ?>">
                                            <?= e(substr($token['token'], 0, 20)) ?>...
                                        </code>
                                        <button class="btn-icon" onclick="copyToken('<?= e($token['token']) ?>')" title="Copiar">
                                            📋
                                        </button>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?= e($token['permisos'] ?? 'Full Access') ?>
                                        </span>
                                    </td>
                                    <td><?= $token['last_used_at'] ? e(date('d/m/Y H:i', strtotime($token['last_used_at']))) : 'Nunca' ?></td>
                                    <td><?= $token['expires_at'] ? e(date('d/m/Y', strtotime($token['expires_at']))) : 'Sin expiración' ?></td>
                                    <td>
                                        <span class="badge <?= $token['activo'] ? 'badge-success' : 'badge-danger' ?>">
                                            <?= $token['activo'] ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <button class="btn-icon" onclick="revocarToken(<?= $token['id'] ?>)" title="Revocar">
                                            🚫
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

    <!-- Guía Rápida -->
    <div class="card">
        <div class="card-header">
            <h3>📖 Guía Rápida de Uso</h3>
        </div>
        <div class="card-body">
            <h4>Autenticación con Token:</h4>
            <pre><code>curl -H "Authorization: Bearer TU_TOKEN_AQUI" \
     https://tudominio.com/api/v1/clientes</code></pre>

            <h4>Endpoints Disponibles:</h4>
            <ul>
                <li><code>GET /api/v1/clientes</code> - Listar clientes</li>
                <li><code>GET /api/v1/productos</code> - Listar productos</li>
                <li><code>POST /api/v1/facturas</code> - Crear factura</li>
                <li><code>GET /api/v1/inventario</code> - Consultar stock</li>
            </ul>

            <p>
                <a href="/app/router.php?module=api&sub=documentacion" class="btn btn-primary">
                    Ver Documentación Completa →
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Modal Nuevo Token -->
<div id="modalNuevoToken" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🔑 Generar Nuevo Token</h3>
            <button class="modal-close" onclick="closeModal('modalNuevoToken')">&times;</button>
        </div>
        <form id="formNuevoToken" onsubmit="generarToken(event)">
            <div class="modal-body">
                <div class="form-group">
                    <label for="nombre">Nombre del Token <span class="required">*</span></label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ej: Integración Ecommerce">
                    <small class="form-hint">Identifica el propósito de este token</small>
                </div>

                <div class="form-group">
                    <label for="permisos">Permisos</label>
                    <select id="permisos" name="permisos">
                        <option value="full">Acceso Total</option>
                        <option value="readonly">Solo Lectura</option>
                        <option value="ventas">Solo Ventas</option>
                        <option value="inventario">Solo Inventario</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="expires_at">Fecha de Expiración</label>
                    <input type="date" id="expires_at" name="expires_at">
                    <small class="form-hint">Dejar vacío para token sin expiración</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalNuevoToken')">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    Generar Token
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

function generarToken(e) {
    e.preventDefault();
    const formData = new FormData(e.target);

    fetch('/app/api/tokens.php?action=create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Token generado exitosamente:\n\n' + data.token + '\n\nGuárdalo en un lugar seguro. No podrás verlo nuevamente.');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error al generar el token');
        console.error(error);
    });
}

function copyToken(token) {
    navigator.clipboard.writeText(token).then(() => {
        alert('Token copiado al portapapeles');
    });
}

function revocarToken(id) {
    if (confirm('¿Estás seguro de revocar este token?\n\nTodas las integraciones que lo usen dejarán de funcionar.')) {
        fetch('/app/api/tokens.php?action=revoke&id=' + id, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Token revocado exitosamente');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

// Cerrar modal al hacer clic fuera
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
    }
});
</script>

<style>
.token-masked {
    background: var(--bg-tertiary, #f3f4f6);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-family: monospace;
    font-size: 0.9rem;
}

pre code {
    display: block;
    background: var(--bg-tertiary, #1e293b);
    color: #e2e8f0;
    padding: 1rem;
    border-radius: 6px;
    overflow-x: auto;
    margin: 1rem 0;
}
</style>
