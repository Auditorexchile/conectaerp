<?php
if (!defined('CONECTA_ERP')) die('Acceso no autorizado');
$empresaId = Session::get('empresa_id');
$pageTitle = 'Configuración de Empresa';
$empresa = db()->selectOne("SELECT * FROM empresas WHERE id = :id", ['id' => $empresaId]);
?>
<div class="page-header">
    <div class="page-title">
        <h1>🏢 Configuración de Empresa</h1>
        <p>Datos básicos de tu empresa</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="guardarEmpresa()">💾 Guardar Cambios</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <form id="formEmpresa">
            <div class="form-row">
                <div class="form-group">
                    <label>Razón Social</label>
                    <input type="text" name="razon_social" value="<?= e($empresa['razon_social'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>RUT/NIF</label>
                    <input type="text" name="rut" value="<?= e($empresa['rut'] ?? '') ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= e($empresa['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="tel" name="telefono" value="<?= e($empresa['telefono'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Dirección</label>
                <input type="text" name="direccion" value="<?= e($empresa['direccion'] ?? '') ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Ciudad</label>
                    <input type="text" name="ciudad" value="<?= e($empresa['ciudad'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>País</label>
                    <select name="pais_codigo">
                        <option value="CL" <?= ($empresa['pais_codigo'] ?? '') == 'CL' ? 'selected' : '' ?>>Chile</option>
                        <option value="AR" <?= ($empresa['pais_codigo'] ?? '') == 'AR' ? 'selected' : '' ?>>Argentina</option>
                        <option value="MX" <?= ($empresa['pais_codigo'] ?? '') == 'MX' ? 'selected' : '' ?>>México</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
function guardarEmpresa() {
    const formData = new FormData(document.getElementById('formEmpresa'));
    fetch('/app/api/config/empresa.php?action=update', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success ? 'Empresa actualizada' : 'Error: ' + data.message);
        if (data.success) location.reload();
    });
}
</script>
