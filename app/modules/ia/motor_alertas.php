<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$pageTitle = 'Motor de Alertas';
?>
<div class="ia-module">
    <div class="module-header">
        <h1>🔔 Motor de Alertas IA</h1>
        <p>Alertas inteligentes en tiempo real</p>
        <button class="btn btn-primary">Configurar Alertas</button>
    </div>
    <div class="module-content">
        <div class="stats-grid">
            <div class="stat-card"><h3>0</h3><p>Alertas Activas</p></div>
            <div class="stat-card"><h3>0</h3><p>Críticas</p></div>
            <div class="stat-card"><h3>0</h3><p>Advertencias</p></div>
            <div class="stat-card excellent"><h3>OK</h3><p>Sistema</p></div>
        </div>
        <div class="results-table">
            <h3>Alertas Pendientes</h3>
            <div class="empty-state"><p>Sin alertas</p></div>
        </div>
    </div>
</div>
