/**
 * RUT-VALIDATOR.JS - Validador específico RUT chileno (alias de country-config)
 * Este archivo mantiene compatibilidad con código legacy
 */

// Alias para validación RUT
window.validateRUT = function(rut) {
    if (window.validateIdentifier) {
        return window.validateIdentifier(rut, 'CL');
    }

    // Fallback si country-config no está cargado
    return validateRUTLocal(rut);
};

// Alias para formateo RUT
window.formatRUT = function(rut) {
    if (window.formatIdentifier) {
        return window.formatIdentifier(rut, 'CL');
    }

    return formatRUTLocal(rut);
};

// Validación local RUT
function validateRUTLocal(rut) {
    const clean = rut.replace(/[^0-9kK]/g, '');
    if (clean.length < 2) return { valid: false, error: 'RUT muy corto' };

    const dv = clean.slice(-1).toUpperCase();
    const numero = clean.slice(0, -1);

    if (!/^\d+$/.test(numero)) return { valid: false, error: 'RUT debe ser numérico' };

    const dvCalculado = calculateDV(numero);

    return dv === dvCalculado
        ? { valid: true }
        : { valid: false, error: 'Dígito verificador inválido' };
}

// Formateo local RUT
function formatRUTLocal(rut) {
    const clean = rut.replace(/[^0-9kK]/g, '');
    if (clean.length < 2) return clean;

    const dv = clean.slice(-1);
    const numero = clean.slice(0, -1);

    return numero.replace(/\B(?=(\d{3})+(?!\d))/g, '.') + '-' + dv.toUpperCase();
}

// Calcular dígito verificador
function calculateDV(numero) {
    let suma = 0;
    let multiplo = 2;

    for (let i = numero.length - 1; i >= 0; i--) {
        suma += parseInt(numero[i]) * multiplo;
        multiplo = multiplo < 7 ? multiplo + 1 : 2;
    }

    const resto = suma % 11;
    const dv = 11 - resto;

    if (dv === 11) return '0';
    if (dv === 10) return 'K';
    return String(dv);
}
