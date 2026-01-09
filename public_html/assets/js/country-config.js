/**
 * COUNTRY-CONFIG.JS - Configuración y validadores por país
 */

const COUNTRY_CONFIG = {
    CL: {
        nombre: 'Chile',
        tipo: 'RUT',
        formato: 'XX.XXX.XXX-X',
        pattern: /^(\d{1,2})\.?(\d{3})\.?(\d{3})-?([0-9kK])$/,
        formatter: (value) => {
            const clean = value.replace(/[^0-9kK]/g, '');
            if (clean.length < 2) return clean;

            const dv = clean.slice(-1);
            const numero = clean.slice(0, -1);

            return numero.replace(/\B(?=(\d{3})+(?!\d))/g, '.') + '-' + dv.toUpperCase();
        },
        validator: (value) => {
            const clean = value.replace(/[^0-9kK]/g, '');
            if (clean.length < 2) return { valid: false, error: 'RUT muy corto' };

            const dv = clean.slice(-1).toUpperCase();
            const numero = clean.slice(0, -1);

            if (!/^\d+$/.test(numero)) return { valid: false, error: 'RUT debe ser numérico' };

            const dvCalculado = calculateDVChile(numero);

            return dv === dvCalculado
                ? { valid: true }
                : { valid: false, error: 'Dígito verificador inválido' };
        }
    },
    AR: {
        nombre: 'Argentina',
        tipo: 'CUIT',
        formato: 'XX-XXXXXXXX-X',
        pattern: /^(\d{2})-?(\d{8})-?(\d)$/,
        formatter: (value) => {
            const clean = value.replace(/\D/g, '');
            if (clean.length < 11) return clean;

            return clean.slice(0, 2) + '-' + clean.slice(2, 10) + '-' + clean.slice(10, 11);
        },
        validator: (value) => {
            const clean = value.replace(/\D/g, '');
            if (clean.length !== 11) return { valid: false, error: 'CUIT debe tener 11 dígitos' };

            return { valid: true };
        }
    },
    PE: {
        nombre: 'Perú',
        tipo: 'RUC',
        formato: 'XXXXXXXXXXX',
        pattern: /^(\d{11})$/,
        formatter: (value) => value.replace(/\D/g, '').slice(0, 11),
        validator: (value) => {
            const clean = value.replace(/\D/g, '');
            if (clean.length !== 11) return { valid: false, error: 'RUC debe tener 11 dígitos' };

            const prefijos = ['10', '15', '17', '20'];
            if (!prefijos.includes(clean.slice(0, 2))) {
                return { valid: false, error: 'RUC inválido' };
            }

            return { valid: true };
        }
    },
    CO: {
        nombre: 'Colombia',
        tipo: 'NIT',
        formato: 'XXXXXXXX-X',
        pattern: /^(\d{8,9})-?(\d)$/,
        formatter: (value) => {
            const clean = value.replace(/\D/g, '');
            if (clean.length < 2) return clean;

            return clean.slice(0, -1) + '-' + clean.slice(-1);
        },
        validator: (value) => {
            const clean = value.replace(/\D/g, '');
            if (clean.length < 9 || clean.length > 10) {
                return { valid: false, error: 'NIT inválido' };
            }

            return { valid: true };
        }
    },
    MX: {
        nombre: 'México',
        tipo: 'RFC',
        formato: 'XXXX000000XXX',
        pattern: /^[A-Z]{3,4}\d{6}[A-Z0-9]{3}$/,
        formatter: (value) => value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 13),
        validator: (value) => {
            const clean = value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            if (clean.length < 12 || clean.length > 13) {
                return { valid: false, error: 'RFC debe tener 12 o 13 caracteres' };
            }

            if (!/^[A-Z]{3,4}\d{6}[A-Z0-9]{3}$/.test(clean)) {
                return { valid: false, error: 'RFC inválido' };
            }

            return { valid: true };
        }
    },
    BR: {
        nombre: 'Brasil',
        tipo: 'CNPJ',
        formato: 'XX.XXX.XXX/XXXX-XX',
        pattern: /^(\d{2})\.?(\d{3})\.?(\d{3})\/?(\d{4})-?(\d{2})$/,
        formatter: (value) => {
            const clean = value.replace(/\D/g, '');
            if (clean.length < 14) return clean;

            return clean.slice(0, 2) + '.' + clean.slice(2, 5) + '.' + clean.slice(5, 8) + '/' + clean.slice(8, 12) + '-' + clean.slice(12, 14);
        },
        validator: (value) => {
            const clean = value.replace(/\D/g, '');
            if (clean.length !== 14) return { valid: false, error: 'CNPJ debe tener 14 dígitos' };

            return { valid: true };
        }
    },
    US: {
        nombre: 'Estados Unidos',
        tipo: 'EIN',
        formato: 'XX-XXXXXXX',
        pattern: /^(\d{2})-?(\d{7})$/,
        formatter: (value) => {
            const clean = value.replace(/\D/g, '');
            if (clean.length < 9) return clean;

            return clean.slice(0, 2) + '-' + clean.slice(2, 9);
        },
        validator: (value) => {
            const clean = value.replace(/\D/g, '');
            if (clean.length !== 9) return { valid: false, error: 'EIN debe tener 9 dígitos' };

            return { valid: true };
        }
    },
    ES: {
        nombre: 'España',
        tipo: 'CIF/NIF',
        formato: 'X0000000X',
        pattern: /^[A-Z]\d{7}[A-Z0-9]$/,
        formatter: (value) => value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 9),
        validator: (value) => {
            const clean = value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            if (clean.length !== 9) return { valid: false, error: 'CIF/NIF debe tener 9 caracteres' };

            return { valid: true };
        }
    },
    FR: {
        nombre: 'Francia',
        tipo: 'SIRET',
        formato: 'XXXXXXXXXXXXXX',
        pattern: /^\d{14}$/,
        formatter: (value) => value.replace(/\D/g, '').slice(0, 14),
        validator: (value) => {
            const clean = value.replace(/\D/g, '');
            if (clean.length !== 14) return { valid: false, error: 'SIRET debe tener 14 dígitos' };

            return { valid: true };
        }
    },
    DE: {
        nombre: 'Alemania',
        tipo: 'USt-IdNr',
        formato: 'DEXXXXXXXXX',
        pattern: /^DE\d{9}$/,
        formatter: (value) => value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 11),
        validator: (value) => {
            const clean = value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            if (!/^DE\d{9}$/.test(clean)) return { valid: false, error: 'USt-IdNr inválido' };

            return { valid: true };
        }
    },
    IT: {
        nombre: 'Italia',
        tipo: 'Partita IVA',
        formato: 'XXXXXXXXXXX',
        pattern: /^\d{11}$/,
        formatter: (value) => value.replace(/\D/g, '').slice(0, 11),
        validator: (value) => {
            const clean = value.replace(/\D/g, '');
            if (clean.length !== 11) return { valid: false, error: 'Partita IVA debe tener 11 dígitos' };

            return { valid: true };
        }
    },
    GB: {
        nombre: 'Reino Unido',
        tipo: 'VAT Number',
        formato: 'GBXXXXXXXXX',
        pattern: /^GB\d{9,12}$/,
        formatter: (value) => value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 14),
        validator: (value) => {
            const clean = value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            if (!/^GB\d{9,12}$/.test(clean)) return { valid: false, error: 'VAT Number inválido' };

            return { valid: true };
        }
    }
};

// Calcular DV Chile
function calculateDVChile(numero) {
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

// Configurar formateador automático para un input
window.setupFormatter = function(inputId, countryCode) {
    const input = document.getElementById(inputId);
    const config = COUNTRY_CONFIG[countryCode];

    if (!input || !config) return;

    // Remover event listeners anteriores
    const newInput = input.cloneNode(true);
    input.parentNode.replaceChild(newInput, input);

    // Agregar nuevo event listener
    newInput.addEventListener('input', function(e) {
        const cursorPos = this.selectionStart;
        const oldValue = this.value;
        const newValue = config.formatter(oldValue);

        if (newValue !== oldValue) {
            this.value = newValue;

            // Mantener posición del cursor
            const diff = newValue.length - oldValue.length;
            this.setSelectionRange(cursorPos + diff, cursorPos + diff);
        }
    });

    // Validación en blur
    newInput.addEventListener('blur', function() {
        if (this.value) {
            const validation = config.validator(this.value);
            const feedback = this.parentNode.querySelector('.validation-feedback');

            if (validation.valid) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                if (feedback) feedback.remove();
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');

                if (!feedback) {
                    const feedbackEl = document.createElement('div');
                    feedbackEl.className = 'validation-feedback';
                    feedbackEl.style.color = 'var(--error)';
                    feedbackEl.style.fontSize = '0.8125rem';
                    feedbackEl.style.marginTop = 'var(--spacing-xs)';
                    feedbackEl.textContent = validation.error;
                    this.parentNode.appendChild(feedbackEl);
                } else {
                    feedback.textContent = validation.error;
                }
            }
        }
    });
};

// Validar identificador
window.validateIdentifier = function(value, countryCode) {
    const config = COUNTRY_CONFIG[countryCode];
    if (!config) return { valid: false, error: 'País no soportado' };

    return config.validator(value);
};

// Formatear identificador
window.formatIdentifier = function(value, countryCode) {
    const config = COUNTRY_CONFIG[countryCode];
    if (!config) return value;

    return config.formatter(value);
};
