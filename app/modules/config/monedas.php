<?php
/**
 * CONECTA ERP - GESTIÓN DE MONEDAS
 * Gestión de monedas disponibles en el sistema
 */

if (!defined('CONECTA_ERP')) {
    die('Acceso no autorizado');
}

$empresaId = Session::get('empresa_id');
$pageTitle = 'Gestión de Monedas';

// Obtener lista de monedas
$monedas = db()->select(
    "SELECT * FROM monedas
     WHERE empresa_id = :empresa_id OR empresa_id IS NULL
     ORDER BY codigo ASC",
    ['empresa_id' => $empresaId]
);
?>

<div class="config-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title">
            <h1>💱 Gestión de Monedas</h1>
            <p>Administra las monedas disponibles en el sistema</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-primary" onclick="showModal('modalNuevaMoneda')">
                <span class="icon">➕</span>
                Nueva Moneda
            </button>
        </div>
    </div>

    <!-- Tabla de Monedas -->
    <div class="card">
        <div class="card-header">
            <h3>📋 Monedas Registradas</h3>
            <div class="search-box">
                <input type="text" id="searchMonedas" placeholder="Buscar moneda..." class="search-input">
                <span class="search-icon">🔍</span>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($monedas)): ?>
                <div class="empty-state">
                    <span class="icon">💱</span>
                    <h3>No hay monedas registradas</h3>
                    <p>Comienza agregando tu primera moneda</p>
                    <button class="btn btn-primary" onclick="showModal('modalNuevaMoneda')">
                        Agregar Moneda
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Símbolo</th>
                                <th>Decimales</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($monedas as $moneda): ?>
                                <tr>
                                    <td><strong><?= e($moneda['codigo']) ?></strong></td>
                                    <td><?= e($moneda['nombre']) ?></td>
                                    <td><?= e($moneda['simbolo']) ?></td>
                                    <td><?= e($moneda['decimales'] ?? 2) ?></td>
                                    <td>
                                        <span class="badge <?= $moneda['activo'] ? 'badge-success' : 'badge-danger' ?>">
                                            <?= $moneda['activo'] ? 'Activa' : 'Inactiva' ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <button class="btn-icon" onclick="editarMoneda(<?= $moneda['id'] ?>)" title="Editar">
                                            ✏️
                                        </button>
                                        <button class="btn-icon" onclick="eliminarMoneda(<?= $moneda['id'] ?>)" title="Eliminar">
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

<!-- Modal Nueva Moneda -->
<div id="modalNuevaMoneda" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>➕ Nueva Moneda</h3>
            <button class="modal-close" onclick="closeModal('modalNuevaMoneda')">&times;</button>
        </div>
        <form id="formNuevaMoneda" onsubmit="guardarMoneda(event)">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="codigo">Código <span class="required">*</span></label>
                        <input type="text" id="codigo" name="codigo" required maxlength="3" placeholder="Ej: USD, CLP, EUR">
                        <small class="form-hint">3 caracteres (ISO 4217)</small>
                    </div>
                    <div class="form-group">
                        <label for="simbolo">Símbolo <span class="required">*</span></label>
                        <input type="text" id="simbolo" name="simbolo" required maxlength="5" placeholder="Ej: $, €, £">
                    </div>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre <span class="required">*</span></label>
                    <input type="text" id="nombre" name="nombre" required maxlength="100" placeholder="Ej: Dólar Estadounidense">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="decimales">Decimales</label>
                        <input type="number" id="decimales" name="decimales" value="2" min="0" max="4">
                    </div>
                    <div class="form-group">
                        <label for="activo">Estado</label>
                        <select id="activo" name="activo">
                            <option value="1" selected>Activa</option>
                            <option value="0">Inactiva</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalNuevaMoneda')">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    Guardar Moneda
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

function guardarMoneda(e) {
    e.preventDefault();
    const formData = new FormData(e.target);

    fetch('/app/api/config/monedas.php?action=create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Moneda guardada exitosamente');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error al guardar la moneda');
        console.error(error);
    });
}

function editarMoneda(id) {
    alert('Función de edición en desarrollo. ID: ' + id);
}

function eliminarMoneda(id) {
    if (confirm('¿Estás seguro de eliminar esta moneda?')) {
        fetch('/app/api/config/monedas.php?action=delete&id=' + id, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Moneda eliminada');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

// Búsqueda en tabla
document.getElementById('searchMonedas')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.data-table tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Cerrar modal al hacer clic fuera
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
    }
});
</script>
