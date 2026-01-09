/**
 * REGISTER.JS - Funcionalidad registro multipaso
 */

let currentSection = 1;
const totalSections = 5;

document.addEventListener('DOMContentLoaded', function() {
    initRegisterForm();
    initPasswordToggles();
    initPasswordStrength();
    initCountryChange();
});

// Inicializar formulario
function initRegisterForm() {
    showSection(1);
}

// Navegación entre secciones
function nextSection(section) {
    if (validateSection(currentSection)) {
        currentSection = section;
        showSection(section);
        updateProgressSteps();
        scrollToTop();
    }
}

function prevSection(section) {
    currentSection = section;
    showSection(section);
    updateProgressSteps();
    scrollToTop();
}

function showSection(section) {
    document.querySelectorAll('.form-section').forEach(s => {
        s.classList.remove('active');
    });

    const sectionEl = document.getElementById('section' + section);
    if (sectionEl) {
        sectionEl.classList.add('active');
    }
}

function updateProgressSteps() {
    document.querySelectorAll('.step').forEach((step, index) => {
        const stepNum = index + 1;
        step.classList.remove('active', 'completed');

        if (stepNum < currentSection) {
            step.classList.add('completed');
        } else if (stepNum === currentSection) {
            step.classList.add('active');
        }
    });
}

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Validar sección
function validateSection(section) {
    const sectionEl = document.getElementById('section' + section);
    if (!sectionEl) return false;

    const requiredFields = sectionEl.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('error');
            showFieldError(field, 'Este campo es obligatorio');
        } else {
            field.classList.remove('error');
            removeFieldError(field);
        }
    });

    // Validaciones específicas por sección
    if (section === 1) {
        const identificador = document.getElementById('identificador_empresa');
        if (identificador && identificador.value) {
            const pais = document.getElementById('pais_id');
            const paisSelected = pais.options[pais.selectedIndex];
            const codigoPais = paisSelected.dataset.codigo;

            if (codigoPais && window.validateIdentifier) {
                const validation = window.validateIdentifier(identificador.value, codigoPais);
                if (!validation.valid) {
                    isValid = false;
                    showFieldError(identificador, validation.error);
                }
            }
        }
    }

    if (section === 3) {
        const email = document.getElementById('email');
        if (email && email.value && !validateEmail(email.value)) {
            isValid = false;
            showFieldError(email, 'Email inválido');
        }
    }

    if (section === 4) {
        const password = document.getElementById('password');
        const passwordConfirm = document.getElementById('password_confirm');

        if (password && password.value.length < 8) {
            isValid = false;
            showFieldError(password, 'Mínimo 8 caracteres');
        }

        if (password && passwordConfirm && password.value !== passwordConfirm.value) {
            isValid = false;
            showFieldError(passwordConfirm, 'Las contraseñas no coinciden');
        }
    }

    if (!isValid) {
        showAlert('Complete todos los campos obligatorios correctamente', 'error');
    }

    return isValid;
}

function showFieldError(field, message) {
    removeFieldError(field);

    const error = document.createElement('div');
    error.className = 'field-error';
    error.style.color = 'var(--error)';
    error.style.fontSize = '0.8125rem';
    error.style.marginTop = 'var(--spacing-xs)';
    error.textContent = message;

    field.parentNode.appendChild(error);
}

function removeFieldError(field) {
    const error = field.parentNode.querySelector('.field-error');
    if (error) {
        error.remove();
    }
}

// Toggle password visibility
function initPasswordToggles() {
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);

            if (input) {
                const type = input.type === 'password' ? 'text' : 'password';
                input.type = type;

                const icon = this.querySelector('.eye-open');
                if (icon) {
                    icon.style.display = type === 'password' ? 'block' : 'none';
                }
            }
        });
    });
}

// Password strength indicator
function initPasswordStrength() {
    const passwordInput = document.getElementById('password');
    const strengthEl = document.getElementById('passwordStrength');

    if (passwordInput && strengthEl) {
        passwordInput.addEventListener('input', function() {
            const strength = calculatePasswordStrength(this.value);

            strengthEl.className = 'password-strength ' + strength;
        });
    }
}

function calculatePasswordStrength(password) {
    if (!password) return '';

    let strength = 0;

    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    if (strength <= 2) return 'weak';
    if (strength <= 4) return 'medium';
    if (strength <= 5) return 'strong';
    return 'very-strong';
}

// Generar contraseña segura
function generatePassword() {
    const length = 16;
    const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*';
    let password = '';

    // Asegurar al menos un carácter de cada tipo
    password += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[Math.floor(Math.random() * 26)];
    password += 'abcdefghijklmnopqrstuvwxyz'[Math.floor(Math.random() * 26)];
    password += '0123456789'[Math.floor(Math.random() * 10)];
    password += '!@#$%&*'[Math.floor(Math.random() * 7)];

    // Completar con caracteres aleatorios
    for (let i = password.length; i < length; i++) {
        password += charset[Math.floor(Math.random() * charset.length)];
    }

    // Mezclar
    password = password.split('').sort(() => Math.random() - 0.5).join('');

    // Asignar a los campos
    document.getElementById('password').value = password;
    document.getElementById('password_confirm').value = password;

    // Actualizar indicador
    const strengthEl = document.getElementById('passwordStrength');
    if (strengthEl) {
        strengthEl.className = 'password-strength very-strong';
    }

    showAlert('Contraseña generada. Guárdela en un lugar seguro.', 'success');
}

// Cambio de país
function initCountryChange() {
    const paisSelect = document.getElementById('pais_id');
    if (paisSelect) {
        paisSelect.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const codigo = option.dataset.codigo;
            const formato = option.dataset.formato;
            const tipo = option.dataset.tipo;

            // Actualizar label del identificador
            const label = document.getElementById('label_identificador');
            if (label) {
                label.textContent = tipo || 'Identificador';
            }

            const labelPersonal = document.getElementById('label_identificador_personal');
            if (labelPersonal) {
                labelPersonal.textContent = tipo || 'Identificación Personal';
            }

            // Actualizar placeholder y ayuda
            const input = document.getElementById('identificador_empresa');
            if (input) {
                input.placeholder = formato || '';
            }

            const ayuda = document.getElementById('formato_ayuda');
            if (ayuda) {
                ayuda.textContent = 'Formato: ' + formato;
            }

            // Configurar formateo automático
            if (codigo && window.setupFormatter) {
                window.setupFormatter('identificador_empresa', codigo);
                window.setupFormatter('identificador_personal', codigo);
            }
        });
    }
}

// Validar email
function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Mostrar alerta
function showAlert(message, type = 'info') {
    const existingAlert = document.querySelector('.alert');
    if (existingAlert) {
        existingAlert.remove();
    }

    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        ${message}
    `;

    const firstSection = document.querySelector('.form-section.active');
    if (firstSection) {
        firstSection.insertBefore(alert, firstSection.firstChild);
    }

    setTimeout(() => alert.remove(), 5000);
}
