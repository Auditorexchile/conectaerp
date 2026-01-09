<?php
/**
 * Validators - Validaciones para registro y actualización
 */

function validateEmpresaData($data) {
    $errors = [];

    // Validar nombre comercial
    if (empty($data['nombre_comercial'])) {
        $errors[] = 'Nombre comercial es requerido';
    }

    // Validar razón social
    if (empty($data['razon_social'])) {
        $errors[] = 'Razón social es requerida';
    }

    // Validar identificador fiscal
    if (empty($data['identificador_fiscal'])) {
        $errors[] = 'Identificador fiscal es requerido';
    } else {
        // Validar según país
        $validator = getCountryValidator($data['pais_id']);
        if ($validator && !$validator($data['identificador_fiscal'])) {
            $errors[] = 'Identificador fiscal inválido';
        }
    }

    // Validar país
    if (empty($data['pais_id'])) {
        $errors[] = 'País es requerido';
    }

    // Validar email empresa
    if (empty($data['email_empresa'])) {
        $errors[] = 'Email de empresa es requerido';
    } elseif (!filter_var($data['email_empresa'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email de empresa inválido';
    }

    // Validar dirección
    if (empty($data['direccion'])) {
        $errors[] = 'Dirección es requerida';
    }

    // Validar ciudad
    if (empty($data['ciudad'])) {
        $errors[] = 'Ciudad es requerida';
    }

    // Validar datos del representante
    if (empty($data['nombre'])) {
        $errors[] = 'Nombre del representante es requerido';
    }

    if (empty($data['apellido'])) {
        $errors[] = 'Apellido del representante es requerido';
    }

    if (empty($data['email'])) {
        $errors[] = 'Email del representante es requerido';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email del representante inválido';
    }

    // Validar identificador personal
    if (empty($data['identificador_personal'])) {
        $errors[] = 'Identificador personal es requerido';
    } else {
        $validator = getCountryValidator($data['pais_id']);
        if ($validator && !$validator($data['identificador_personal'])) {
            $errors[] = 'Identificador personal inválido';
        }
    }

    // Validar contraseña
    if (empty($data['password'])) {
        $errors[] = 'Contraseña es requerida';
    } elseif (strlen($data['password']) < 8) {
        $errors[] = 'La contraseña debe tener al menos 8 caracteres';
    } elseif (!preg_match('/[A-Z]/', $data['password'])) {
        $errors[] = 'La contraseña debe contener al menos una mayúscula';
    } elseif (!preg_match('/[a-z]/', $data['password'])) {
        $errors[] = 'La contraseña debe contener al menos una minúscula';
    } elseif (!preg_match('/[0-9]/', $data['password'])) {
        $errors[] = 'La contraseña debe contener al menos un número';
    } elseif (!preg_match('/[^A-Za-z0-9]/', $data['password'])) {
        $errors[] = 'La contraseña debe contener al menos un carácter especial';
    }

    // Validar confirmación de contraseña
    if ($data['password'] !== $data['password_confirmation']) {
        $errors[] = 'Las contraseñas no coinciden';
    }

    // Validar términos y condiciones
    if (!isset($data['acepta_terminos'])) {
        $errors[] = 'Debes aceptar los términos y condiciones';
    }

    if (!empty($errors)) {
        return ['valid' => false, 'error' => implode('. ', $errors)];
    }

    return ['valid' => true];
}

function validatePasswordReset($password, $confirmation) {
    $errors = [];

    if (empty($password)) {
        $errors[] = 'Contraseña es requerida';
    } elseif (strlen($password) < 8) {
        $errors[] = 'La contraseña debe tener al menos 8 caracteres';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'La contraseña debe contener al menos una mayúscula';
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'La contraseña debe contener al menos una minúscula';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'La contraseña debe contener al menos un número';
    } elseif (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = 'La contraseña debe contener al menos un carácter especial';
    }

    if ($password !== $confirmation) {
        $errors[] = 'Las contraseñas no coinciden';
    }

    if (!empty($errors)) {
        return ['valid' => false, 'error' => implode('. ', $errors)];
    }

    return ['valid' => true];
}
