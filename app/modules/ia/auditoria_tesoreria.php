<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$pageTitle = 'Auditoría Tesorería';
?>
<div class="ia-module">
    <div class="module-header">
        <h1>💰 Auditoría de Tesorería IA</h1>
        <p>Conciliación bancaria automática y detección de diferencias</p>
        <button class="btn btn-primary">Conciliar Cuentas</button>
    </div>
    <div class="module-content">
        <div class="stats-grid">
            <div class="stat-card"><h3>0</h3><p>Cuentas Bancarias</p></div>
            <div class="stat-card"><h3>$0</h3><p>Diferencias</p></div>
            <div class="stat-card excellent"><h3>OK</h3><p>Conciliación</p></div>
            <div class="stat-card"><h3>0</h3><p>Pendientes</p></div>
        </div>
        <div class="results-table">
            <h3>Estado de Conciliación</h3>
            <div class="empty-state"><p>Sin diferencias</p></div>
        </div>
    </div>
</div>
