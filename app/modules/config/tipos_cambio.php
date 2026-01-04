<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Tipos de Cambio';
$tiposCambio = db()->select("SELECT * FROM tipos_cambio WHERE empresa_id = :id ORDER BY fecha DESC LIMIT 30", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>💹 Tipos de Cambio</h1>
        <p>Gestión de tasas de cambio entre monedas</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary" onclick="actualizarAutomatico()">🔄 Actualizar desde API</button>
        <button class="btn btn-primary" onclick="nuevoTipoCambio()">➕ Nuevo Tipo de Cambio</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($tiposCambio)): ?>
            <div class="empty-state">
                <span class="icon">💹</span>
                <h3>No hay tipos de cambio registrados</h3>
                <p>Agrega tasas de cambio manualmente o actualiza desde una API</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead><tr><th>Fecha</th><th>Moneda Origen</th><th>Moneda Destino</th><th>Tasa</th><th>Fuente</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($tiposCambio as $tc): ?>
                            <tr>
                                <td><?= e(date('d/m/Y', strtotime($tc['fecha']))) ?></td>
                                <td><strong><?= e($tc['moneda_origen']) ?></strong></td>
                                <td><strong><?= e($tc['moneda_destino']) ?></strong></td>
                                <td><strong><?= e(number_format($tc['tasa'], 4)) ?></strong></td>
                                <td><?= e($tc['fuente'] ?? 'Manual') ?></td>
                                <td class="actions">
                                    <button class="btn-icon" onclick="editarTipoCambio(<?= $tc['id'] ?>)">✏️</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
function nuevoTipoCambio() { alert('Nuevo tipo de cambio'); }
function editarTipoCambio(id) { alert('Editar tipo cambio ' + id); }
function actualizarAutomatico() {
    fetch('/app/api/config/tipos_cambio.php?action=update_from_api', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success ? 'Tipos de cambio actualizados' : 'Error: ' + data.message);
        if (data.success) location.reload();
    });
}
</script>
