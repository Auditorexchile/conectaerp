<?php
/**
 * CONECTA ERP - MI TIENDA ONLINE
 * Configuración de la tienda ecommerce
 */

if (!defined('CONECTA_ERP')) {
    die('Acceso no autorizado');
}

$empresaId = Session::get('empresa_id');
$pageTitle = 'Mi Tienda Online';

$tienda = db()->selectOne(
    "SELECT * FROM tienda_online WHERE empresa_id = :empresa_id",
    ['empresa_id' => $empresaId]
);
?>

<div class="ecommerce-container">
    <div class="page-header">
        <div class="page-title">
            <h1>🏪 Mi Tienda Online</h1>
            <p>Configura tu tienda ecommerce</p>
        </div>
        <div class="page-actions">
            <?php if ($tienda && $tienda['activo']): ?>
                <a href="<?= e($tienda['url'] ?? '') ?>" target="_blank" class="btn btn-secondary">
                    <span class="icon">🌐</span>
                    Ver Tienda
                </a>
            <?php endif; ?>
            <button class="btn btn-primary" onclick="guardarTienda()">
                <span class="icon">💾</span>
                Guardar Cambios
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>⚙️ Configuración General</h3>
        </div>
        <div class="card-body">
            <form id="formTienda">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre_tienda">Nombre de la Tienda</label>
                        <input type="text" id="nombre_tienda" name="nombre_tienda" 
                               value="<?= e($tienda['nombre'] ?? '') ?>" placeholder="Mi Tienda">
                    </div>
                    <div class="form-group">
                        <label for="url">URL de la Tienda</label>
                        <input type="text" id="url" name="url" 
                               value="<?= e($tienda['url'] ?? '') ?>" placeholder="https://mitienda.com">
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3"><?= e($tienda['descripcion'] ?? '') ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email_contacto">Email de Contacto</label>
                        <input type="email" id="email_contacto" name="email_contacto" 
                               value="<?= e($tienda['email_contacto'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="telefono_contacto">Teléfono de Contacto</label>
                        <input type="tel" id="telefono_contacto" name="telefono_contacto" 
                               value="<?= e($tienda['telefono_contacto'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="activo" <?= ($tienda['activo'] ?? 0) ? 'checked' : '' ?>>
                        Tienda Activa
                    </label>
                </div>
            </form>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon">🛒</div>
            <div class="metric-content">
                <div class="metric-label">Pedidos del Mes</div>
                <div class="metric-value">0</div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon">💰</div>
            <div class="metric-content">
                <div class="metric-label">Ventas del Mes</div>
                <div class="metric-value">$0</div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon">📦</div>
            <div class="metric-content">
                <div class="metric-label">Productos Publicados</div>
                <div class="metric-value">0</div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon">👥</div>
            <div class="metric-content">
                <div class="metric-label">Clientes Registrados</div>
                <div class="metric-value">0</div>
            </div>
        </div>
    </div>
</div>

<script>
function guardarTienda() {
    const formData = new FormData(document.getElementById('formTienda'));
    
    fetch('/app/api/ecommerce/tienda.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Configuración guardada exitosamente');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}
</script>
