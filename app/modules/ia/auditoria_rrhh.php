<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$pageTitle = 'Auditoría RRHH';
?>
<div class="ia-module">
    <div class="module-header">
        <h1>👥 Auditoría RRHH IA</h1>
        <p>Validación de liquidaciones, Previred y horas extra</p>
        <button class="btn btn-primary">Validar Liquidaciones</button>
    </div>
    <div class="module-content">
        <div class="stats-grid">
            <div class="stat-card"><h3>0</h3><p>Empleados</p></div>
            <div class="stat-card"><h3>0</h3><p>Errores</p></div>
            <div class="stat-card excellent"><h3>OK</h3><p>Previred</p></div>
            <div class="stat-card"><h3>0</h3><p>Anomalías</p></div>
        </div>
        <div class="results-table">
            <h3>Validación RRHH</h3>
            <div class="empty-state"><p>Todo correcto</p></div>
        </div>
    </div>
</div>
