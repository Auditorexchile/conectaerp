/**
 * CONECTA ERP - REGISTRO JAVASCRIPT
 * Validaciones dinámicas y funcionalidades interactivas
 */

(function() {
    'use strict';

    // Configuración de países (mapeo de tipos de identificadores)
    const paisesConfig = {
        'CL': { tipo: 'RUT', formato: 'XX.XXX.XXX-X', ejemplo: '12.345.678-9' },
        'AR': { tipo: 'CUIT', formato: 'XX-XXXXXXXX-X', ejemplo: '20-12345678-9' },
        'PE': { tipo: 'RUC', formato: 'XXXXXXXXXXX', ejemplo: '20123456789' },
        'CO': { tipo: 'NIT', formato: 'XXXXXXXX-X', ejemplo: '12345678-9' },
        'MX': { tipo: 'RFC', formato: 'XXXX000000XXX', ejemplo: 'ABC123456D12' },
        'BR': { tipo: 'CNPJ', formato: 'XX.XXX.XXX/0001-XX', ejemplo: '12.345.678/0001-90' },
        'US': { tipo: 'EIN', formato: 'XX-XXXXXXX', ejemplo: '12-3456789' },
        'ES': { tipo: 'CIF/NIF', formato: 'X0000000X', ejemplo: 'A12345678' },
        'FR': { tipo: 'SIRET', formato: 'XXXXXXXXXXXXXX', ejemplo: '12345678901234' },
        'DE': { tipo: 'USt-IdNr', formato: 'DEXXXXXXXXX', ejemplo: 'DE123456789' },
        'IT': { tipo: 'P.IVA', formato: 'XXXXXXXXXXX', ejemplo: '12345678901' },
        'GB': { tipo: 'VAT', formato: 'GBXXXXXXXXX', ejemplo: 'GB123456789' }
    };

    // ===== MANEJO DE CAMBIO DE PAÍS =====
    window.handlePaisChange = function(paisCodigo) {
        if (!paisCodigo) return;

        const config = paisesConfig[paisCodigo];
        if (!config) return;

        // Actualizar label del identificador
        const identificadorLabel = document.getElementById('identificadorLabel');
        if (identificadorLabel) {
            identificadorLabel.innerHTML = config.tipo + ' <span class="required">*</span>';
        }

        // Actualizar placeholder
        const identificadorInput = document.getElementById('identificador');
        if (identificadorInput) {
            identificadorInput.placeholder = config.ejemplo;
        }

        // Actualizar hint
        const identificadorHint = document.getElementById('identificadorHint');
        if (identificadorHint) {
            identificadorHint.textContent = 'Formato: ' + config.formato;
        }

        // Cargar regiones y giros del país (AJAX)
        cargarDatosPais(paisCodigo);
    };

    // ===== CARGAR DATOS DINÁMICOS DEL PAÍS =====
    function cargarDatosPais(paisCodigo) {
        // Esta función haría una llamada AJAX para cargar regiones y giros
        // Por ahora, solo recargamos la página para actualizar los datos
        // En producción, esto debería ser AJAX para mejor UX
        console.log('Cargando datos para país:', paisCodigo);
    }

    // ===== MANEJO DE CAMBIO DE REGIÓN =====
    window.handleRegionChange = function(regionId) {
        if (!regionId) {
            const comunaSelect = document.getElementById('comunaSelect');
            if (comunaSelect) {
                comunaSelect.innerHTML = '<option value="">Seleccione comuna</option>';
            }
            return;
        }

        // Cargar comunas de la región (AJAX)
        cargarComunas(regionId);
    };

    // ===== CARGAR COMUNAS =====
    function cargarComunas(regionId) {
        console.log('Cargando comunas para región:', regionId);
        // En producción, esto debería hacer una llamada AJAX
    }

    // ===== TOGGLE PASSWORD VISIBILITY =====
    window.togglePassword = function(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    };

    // ===== GENERAR CONTRASEÑA SEGURA =====
    window.generatePassword = function() {
        const length = 12;
        const uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        const lowercase = 'abcdefghjkmnpqrstuvwxyz';
        const numbers = '23456789';
        const symbols = '!@#$%&*';

        let password = '';

        // Garantizar al menos un carácter de cada tipo
        password += uppercase[Math.floor(Math.random() * uppercase.length)];
        password += lowercase[Math.floor(Math.random() * lowercase.length)];
        password += numbers[Math.floor(Math.random() * numbers.length)];
        password += symbols[Math.floor(Math.random() * symbols.length)];

        // Completar el resto con caracteres aleatorios
        const allChars = uppercase + lowercase + numbers + symbols;
        for (let i = 4; i < length; i++) {
            password += allChars[Math.floor(Math.random() * allChars.length)];
        }

        // Mezclar el password
        password = password.split('').sort(() => Math.random() - 0.5).join('');

        // Asignar al input
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirm');

        if (passwordInput) {
            passwordInput.value = password;
            passwordInput.type = 'text';
            calcularFortaleza(password);
        }

        if (passwordConfirmInput) {
            passwordConfirmInput.value = password;
        }

        // Volver a password después de 2 segundos
        setTimeout(() => {
            if (passwordInput) {
                passwordInput.type = 'password';
            }
        }, 2000);
    };

    // ===== CALCULAR FORTALEZA DE CONTRASEÑA =====
    function calcularFortaleza(password) {
        if (!password) {
            document.getElementById('passwordStrength').style.display = 'none';
            return;
        }

        let score = 0;

        // Longitud
        score += Math.min(password.length * 4, 40);

        // Mayúsculas
        if (/[A-Z]/.test(password)) {
            score += 10;
        }

        // Minúsculas
        if (/[a-z]/.test(password)) {
            score += 10;
        }

        // Números
        if (/[0-9]/.test(password)) {
            score += 10;
        }

        // Caracteres especiales
        if (/[^A-Za-z0-9]/.test(password)) {
            score += 20;
        }

        // Variedad de caracteres
        const uniqueChars = new Set(password.split('')).size;
        score += Math.min(uniqueChars * 2, 20);

        score = Math.min(score, 100);

        // Determinar nivel
        let nivel, color, texto;
        if (score < 40) {
            nivel = 'debil';
            color = '#ef4444';
            texto = 'Débil';
        } else if (score < 70) {
            nivel = 'media';
            color = '#f59e0b';
            texto = 'Media';
        } else {
            nivel = 'fuerte';
            color = '#10b981';
            texto = 'Fuerte';
        }

        // Mostrar
        const strengthDiv = document.getElementById('passwordStrength');
        const strengthBarFill = document.getElementById('strengthBarFill');
        const strengthText = document.getElementById('strengthText');

        if (strengthDiv) {
            strengthDiv.style.display = 'block';
        }

        if (strengthBarFill) {
            strengthBarFill.style.width = score + '%';
            strengthBarFill.style.backgroundColor = color;
        }

        if (strengthText) {
            strengthText.textContent = texto;
            strengthText.style.color = color;
        }
    }

    // ===== EVENT LISTENER PARA PASSWORD INPUT =====
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            calcularFortaleza(this.value);
        });
    }

    // ===== MODAL DE TÉRMINOS Y CONDICIONES =====
    window.openTermsModal = function(event, tipo) {
        event.preventDefault();

        const modal = document.getElementById('termsModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');

        if (!modal) return;

        if (tipo === 'terminos') {
            modalTitle.textContent = 'Términos y Condiciones';
            modalBody.innerHTML = `
                <h3>TÉRMINOS Y CONDICIONES DE USO</h3>
                <p><strong>Última actualización: ${new Date().toLocaleDateString('es-CL')}</strong></p>

                <h4>1. ACEPTACIÓN DE LOS TÉRMINOS</h4>
                <p>Al registrarse en Conecta ERP, usted acepta estos términos y condiciones en su totalidad.</p>

                <h4>2. DESCRIPCIÓN DEL SERVICIO</h4>
                <p>Conecta ERP es un sistema de gestión empresarial (ERP) en la nube que proporciona módulos de contabilidad, ventas, inventario, RRHH y más.</p>

                <h4>3. PERÍODO DE PRUEBA (TRIAL)</h4>
                <ul>
                    <li>Todos los planes incluyen 14 días de prueba gratuita</li>
                    <li>Durante el trial tiene acceso completo a las funcionalidades del plan seleccionado</li>
                    <li>No se requiere tarjeta de crédito para iniciar el trial</li>
                    <li>Al finalizar el trial, debe contratar un plan para continuar usando el sistema</li>
                </ul>

                <h4>4. PLANES Y PAGOS</h4>
                <ul>
                    <li>Los precios se muestran en la moneda local de su país</li>
                    <li>Los pagos son mensuales o anuales según su elección</li>
                    <li>Las renovaciones son automáticas</li>
                    <li>Puede cancelar en cualquier momento</li>
                </ul>

                <h4>5. USO ACEPTABLE</h4>
                <ul>
                    <li>No usar el sistema para actividades ilegales</li>
                    <li>No intentar acceder a datos de otras empresas</li>
                    <li>Mantener la confidencialidad de sus credenciales</li>
                    <li>No revender o sublicenciar el servicio</li>
                </ul>

                <h4>6. PRIVACIDAD Y SEGURIDAD</h4>
                <ul>
                    <li>Sus datos están protegidos con encriptación</li>
                    <li>No compartimos su información con terceros sin su consentimiento</li>
                    <li>Cumplimos con las leyes de protección de datos aplicables</li>
                </ul>

                <h4>7. PROPIEDAD INTELECTUAL</h4>
                <ul>
                    <li>Conecta ERP es propiedad de sus desarrolladores</li>
                    <li>Los datos que usted ingresa son de su propiedad</li>
                    <li>Puede exportar sus datos en cualquier momento</li>
                </ul>

                <h4>8. CONTACTO</h4>
                <p>Para consultas sobre estos términos: <strong>contacto@conectaerp.com</strong></p>
            `;
        } else if (tipo === 'privacidad') {
            modalTitle.textContent = 'Política de Privacidad';
            modalBody.innerHTML = `
                <h3>POLÍTICA DE PRIVACIDAD</h3>
                <p><strong>Última actualización: ${new Date().toLocaleDateString('es-CL')}</strong></p>

                <h4>1. RECOPILACIÓN DE DATOS</h4>
                <p>Recopilamos la siguiente información:</p>
                <ul>
                    <li>Datos de la empresa (RUT, razón social, dirección)</li>
                    <li>Datos del representante legal</li>
                    <li>Datos de contacto (email, teléfono)</li>
                    <li>Datos de facturación</li>
                    <li>Datos de uso del sistema</li>
                </ul>

                <h4>2. USO DE DATOS</h4>
                <p>Utilizamos sus datos para:</p>
                <ul>
                    <li>Proveer el servicio ERP</li>
                    <li>Facturación y cobros</li>
                    <li>Soporte técnico</li>
                    <li>Mejoras del sistema</li>
                    <li>Comunicaciones importantes</li>
                </ul>

                <h4>3. PROTECCIÓN DE DATOS</h4>
                <ul>
                    <li>Encriptación SSL/TLS en todas las comunicaciones</li>
                    <li>Contraseñas hasheadas con Argon2id</li>
                    <li>Backups diarios automáticos</li>
                    <li>Acceso restringido por roles</li>
                </ul>

                <h4>4. COMPARTIR DATOS</h4>
                <p>NO compartimos sus datos con terceros, excepto:</p>
                <ul>
                    <li>Cuando es requerido por ley</li>
                    <li>Con su consentimiento explícito</li>
                    <li>Integraciones autorizadas por usted (SII, Previred, etc.)</li>
                </ul>

                <h4>5. SUS DERECHOS</h4>
                <p>Usted tiene derecho a:</p>
                <ul>
                    <li>Acceder a sus datos</li>
                    <li>Corregir datos incorrectos</li>
                    <li>Eliminar su cuenta y datos</li>
                    <li>Exportar sus datos</li>
                    <li>Optar por no recibir comunicaciones de marketing</li>
                </ul>

                <h4>6. COOKIES</h4>
                <p>Utilizamos cookies para:</p>
                <ul>
                    <li>Mantener su sesión activa</li>
                    <li>Recordar preferencias</li>
                    <li>Analítica básica de uso</li>
                </ul>

                <h4>7. CONTACTO</h4>
                <p>Para consultas sobre privacidad: <strong>contacto@conectaerp.com</strong></p>
            `;
        }

        modal.style.display = 'flex';
    };

    window.closeTermsModal = function() {
        const modal = document.getElementById('termsModal');
        if (modal) {
            modal.style.display = 'none';
        }
    };

    // Cerrar modal al hacer clic fuera
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('termsModal');
        if (modal && event.target === modal) {
            closeTermsModal();
        }
    });

    // ===== VALIDACIÓN DE FORMULARIO ANTES DE ENVIAR =====
    const wizardForm = document.getElementById('wizardForm');
    if (wizardForm) {
        wizardForm.addEventListener('submit', function(event) {
            // Aquí puedes agregar validaciones adicionales
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirm');

            if (passwordInput && confirmInput) {
                if (passwordInput.value !== confirmInput.value) {
                    event.preventDefault();
                    alert('Las contraseñas no coinciden');
                    return false;
                }
            }
        });
    }

    // ===== INICIALIZACIÓN AL CARGAR LA PÁGINA =====
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-trigger cambio de país si ya hay uno seleccionado
        const paisSelect = document.getElementById('paisSelect');
        if (paisSelect && paisSelect.value) {
            handlePaisChange(paisSelect.value);
        }

        // Calcular fortaleza inicial si hay password
        const passwordInput = document.getElementById('password');
        if (passwordInput && passwordInput.value) {
            calcularFortaleza(passwordInput.value);
        }

        console.log('✓ Registro JavaScript inicializado');
    });

})();
