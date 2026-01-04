<?php
/**
 * CONECTA ERP - GESTIÓN DE IMPUESTOS
 * Configuración de impuestos (IVA, retenciones, etc.)
 */

if (!defined('CONECTA_ERP')) {
    die('Acceso no autorizado');
}

$empresaId = Session::get('empresa_id');
$paisCodigo = Session::get('pais_codigo', 'CL');
$pageTitle = 'Gestión de Impuestos';

// Obtener impuestos configurados
$impuestos = db()->select(
    "SELECT * FROM impuestos
     WHERE empresa_id = :empresa_id
     ORDER BY tipo ASC, nombre ASC",
    ['empresa_id' => $empresaId]
);
?>

<div class="config-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title">
            <h1>💼 Gestión de Impuestos</h1>
            <p>Configura los impuestos aplicables a tu empresa (IVA, retenciones, etc.)</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-primary" onclick="showModal('modalNuevoImpuesto')">
                <span class="icon">➕</span>
                Nuevo Impuesto
            </button>
        </div>
    </div>

    <!-- Información por País -->
    <div class="alert alert-info">
        <strong>País: <?= strtoupper($paisCodigo) ?></strong><br>
        <?php if ($paisCodigo === 'CL'): ?>
            IVA vigente: 19% | Retenciones comunes: 10%, 11.5%
        <?php elseif ($paisCodigo === 'AR'): ?>
            IVA vigente: 21% | Retenciones comunes: 3%, 6%
        <?php elseif ($paisCodigo === 'MX'): ?>
            IVA vigente: 16% | Retenciones comunes: 10%
        <?php else: ?>
            Configure los impuestos según la legislación de su país
        <?php endif; ?>
    </div>

    <!-- Tabla de Impuestos -->
    <div class="card">
        <div class="card-header">
            <h3>📋 Impuestos Configurados</h3>
        </div>
        <div class="card-body">
            <?php if (empty($impuestos)): ?>
                <div class="empty-state">
                    <span class="icon">💼</span>
                    <h3>No hay impuestos configurados</h3>
                    <p>Comienza agregando los impuestos aplicables</p>
                    <button class="btn btn-primary" onclick="showModal('modalNuevoImpuesto')">
                        Agregar Impuesto
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Nombre</th>
                                <th>Código</th>
                                <th>Tasa (%)</th>
                                <th>Aplicación</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($impuestos as $impuesto): ?>
                                <tr>
                                    <td>
                                        <span class="badge badge-primary">
                                            <?= e(strtoupper($impuesto['tipo'])) ?>
                                        </span>
                                    </td>
                                    <td><strong><?= e($impuesto['nombre']) ?></strong></td>
                                    <td><?= e($impuesto['codigo']) ?></td>
                                    <td><strong><?= e(number_format($impuesto['tasa'], 2)) ?>%</strong></td>
                                    <td><?= e($impuesto['aplicacion'] ?? 'General') ?></td>
                                    <td>
                                        <span class="badge <?= $impuesto['activo'] ? 'badge-success' : 'badge-danger' ?>">
                                            <?= $impuesto['activo'] ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <button class="btn-icon" onclick="editarImpuesto(<?= $impuesto['id'] ?>)" title="Editar">
                                            ✏️
                                        </button>
                                        <button class="btn-icon" onclick="eliminarImpuesto(<?= $impuesto['id'] ?>)" title="Eliminar">
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

    <!-- Templates Rápidos -->
    <div class="card">
        <div class="card-header">
            <h3>⚡ Templates Rápidos (<?= strtoupper($paisCodigo) ?>)</h3>
        </div>
        <div class="card-body">
            <div class="quick-templates">
                <?php if ($paisCodigo === 'CL'): ?>
                    <button class="template-btn" onclick="crearImpuestoTemplate('IVA', 19)">
                        IVA 19%
                    </button>
                    <button class="template-btn" onclick="crearImpuestoTemplate('RET', 10)">
                        Retención 10%
                    </button>
                    <button class="template-btn" onclick="crearImpuestoTemplate('RET', 11.5)">
                        Retención 11.5%
                    </button>
                <?php elseif ($paisCodigo === 'AR'): ?>
                    <button class="template-btn" onclick="crearImpuestoTemplate('IVA', 21)">
                        IVA 21%
                    </button>
                    <button class="template-btn" onclick="crearImpuestoTemplate('IVA', 10.5)">
                        IVA Reducido 10.5%
                    </button>
                <?php elseif ($paisCodigo === 'MX'): ?>
                    <button class="template-btn" onclick="crearImpuestoTemplate('IVA', 16)">
                        IVA 16%
                    </button>
                    <button class="template-btn" onclick="crearImpuestoTemplate('RET', 10)">
                        Retención 10%
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Impuesto -->
<div id="modalNuevoImpuesto" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>➕ Nuevo Impuesto</h3>
            <button class="modal-close" onclick="closeModal('modalNuevoImpuesto')">&times;</button>
        </div>
        <form id="formNuevoImpuesto" onsubmit="guardarImpuesto(event)">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="tipo">Tipo de Impuesto <span class="required">*</span></label>
                        <select id="tipo" name="tipo" required>
                            <option value="">Seleccionar...</option>
                            <option value="IVA">IVA</option>
                            <option value="RET">Retención</option>
                            <option value="OTRO">Otro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="codigo">Código <span class="required">*</span></label>
                        <input type="text" id="codigo" name="codigo" required maxlength="20" placeholder="Ej: IVA19">
                    </div>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre <span class="required">*</span></label>
                    <input type="text" id="nombre" name="nombre" required maxlength="100" placeholder="Ej: IVA 19%">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tasa">Tasa (%) <span class="required">*</span></label>
                        <input type="number" id="tasa" name="tasa" required step="0.01" min="0" max="100" placeholder="Ej: 19">
                    </div>
                    <div class="form-group">
                        <label for="aplicacion">Aplicación</label>
                        <select id="aplicacion" name="aplicacion">
                            <option value="GENERAL">General</option>
                            <option value="COMPRAS">Solo Compras</option>
                            <option value="VENTAS">Solo Ventas</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3" placeholder="Descripción opcional"></textarea>
                </div>

                <div class="form-group">
                    <label for="activo">Estado</label>
                    <select id="activo" name="activo">
                        <option value="1" selected>Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalNuevoImpuesto')">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    Guardar Impuesto
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

function guardarImpuesto(e) {
    e.preventDefault();
    const formData = new FormData(e.target);

    fetch('/app/api/config/impuestos.php?action=create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Impuesto guardado exitosamente');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error al guardar el impuesto');
        console.error(error);
    });
}

function crearImpuestoTemplate(tipo, tasa) {
    document.getElementById('tipo').value = tipo;
    document.getElementById('tasa').value = tasa;
    document.getElementById('codigo').value = tipo + tasa.toString().replace('.', '');
    document.getElementById('nombre').value = tipo + ' ' + tasa + '%';
    showModal('modalNuevoImpuesto');
}

function editarImpuesto(id) {
    alert('Función de edición en desarrollo. ID: ' + id);
}

function eliminarImpuesto(id) {
    if (confirm('¿Estás seguro de eliminar este impuesto?')) {
        fetch('/app/api/config/impuestos.php?action=delete&id=' + id, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Impuesto eliminado');
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
.quick-templates {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.template-btn {
    padding: 0.75rem 1.5rem;
    background: var(--bg-tertiary, #f3f4f6);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
}

.template-btn:hover {
    background: var(--primary, #3b82f6);
    color: white;
    border-color: var(--primary, #3b82f6);
}
</style>
