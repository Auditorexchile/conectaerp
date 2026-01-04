<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Dispositivos Biométricos';
$dispositivos = db()->select("SELECT * FROM dispositivos_biometricos WHERE empresa_id = :id ORDER BY nombre ASC", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>📱 Dispositivos Biométricos</h1>
        <p>Gestión de relojes y dispositivos de control de asistencia</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="nuevoDispositivo()">➕ Nuevo Dispositivo</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($dispositivos)): ?>
            <div class="empty-state">
                <span class="icon">📱</span>
                <h3>No hay dispositivos configurados</h3>
                <p>Agrega dispositivos biométricos o relojes de control</p>
            </div>
        <?php else: ?>
            <div class="devices-grid">
                <?php foreach ($dispositivos as $d): ?>
                    <div class="device-card">
                        <div class="device-status <?= $d['activo'] ? 'online' : 'offline' ?>">
                            <?= $d['activo'] ? '🟢 Online' : '🔴 Offline' ?>
                        </div>
                        <h3><?= e($d['nombre']) ?></h3>
                        <p><strong>Modelo:</strong> <?= e($d['modelo']) ?></p>
                        <p><strong>IP:</strong> <?= e($d['ip_address']) ?></p>
                        <p><strong>Ubicación:</strong> <?= e($d['ubicacion']) ?></p>
                        <p><strong>Marcajes hoy:</strong> <?= e($d['marcajes_hoy'] ?? 0) ?></p>
                        <div class="device-actions">
                            <button class="btn btn-sm btn-secondary" onclick="sincronizar(<?= $d['id'] ?>)">🔄 Sincronizar</button>
                            <button class="btn btn-sm btn-primary" onclick="configurar(<?= $d['id'] ?>)">⚙️ Configurar</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
function nuevoDispositivo() { alert('Nuevo dispositivo'); }
function sincronizar(id) {
    fetch('/app/api/reloj/dispositivos.php?action=sync&id=' + id, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success ? 'Sincronización exitosa' : 'Error: ' + data.message);
    });
}
function configurar(id) { alert('Configurar dispositivo ' + id); }
</script>
<style>
.devices-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}
.device-card {
    padding: 1.5rem;
    background: var(--bg-tertiary, #f3f4f6);
    border-radius: 8px;
    border: 1px solid var(--border-color, #e5e7eb);
}
.device-status {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 1rem;
}
.device-status.online {
    background: #d1fae5;
    color: #065f46;
}
.device-status.offline {
    background: #fee2e2;
    color: #991b1b;
}
.device-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}
</style>
