<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$pageTitle = 'Auditoría Contable IA';
?>
<div class="ia-module">
    <div class="module-header">
        <h1>📚 Auditoría Contable Inteligente</h1>
        <p>Detección automática de asientos duplicados, ajustes sospechosos y errores contables</p>
        <button class="btn btn-primary" onclick="ejecutarAuditoria()">Ejecutar Auditoría</button>
    </div>
    <div class="module-content">
        <div class="stats-grid">
            <div class="stat-card"><h3>0</h3><p>Asientos Analizados</p></div>
            <div class="stat-card"><h3>0</h3><p>Anomalías Detectadas</p></div>
            <div class="stat-card"><h3>0</h3><p>Duplicados</p></div>
            <div class="stat-card low"><h3>Bajo</h3><p>Nivel de Riesgo</p></div>
        </div>
        <div class="results-table">
            <h3>Resultados de Auditoría</h3>
            <div class="empty-state">
                <p>No se han ejecutado auditorías aún</p>
                <small>Haz clic en "Ejecutar Auditoría" para comenzar el análisis</small>
            </div>
        </div>
    </div>
</div>
<script>function ejecutarAuditoria(){alert('Auditoría programada');}</script>
