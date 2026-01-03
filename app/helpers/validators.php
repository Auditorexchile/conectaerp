<?php
/**
 * CONECTA ERP - VALIDADORES GLOBALES
 *
 * Validación de identificadores tributarios por país
 * RUT, CUIT, RFC, CNPJ, EIN, NIF, etc.
 */

class Validators {

    // ================================================================
    // VALIDACIÓN RUT CHILENO
    // ================================================================

    public static function validarRutChileno($rut) {
        // Limpiar RUT
        $rut = preg_replace('/[^0-9kK]/', '', strtoupper($rut));

        if (strlen($rut) < 2) {
            return false;
        }

        // Separar número y dígito verificador
        $numero = substr($rut, 0, -1);
        $dv = substr($rut, -1);

        // Calcular dígito verificador
        $suma = 0;
        $multiplo = 2;

        for ($i = strlen($numero) - 1; $i >= 0; $i--) {
            $suma += $numero[$i] * $multiplo;
            $multiplo = $multiplo < 7 ? $multiplo + 1 : 2;
        }

        $dvCalculado = 11 - ($suma % 11);

        if ($dvCalculado == 11) {
            $dvCalculado = '0';
        } elseif ($dvCalculado == 10) {
            $dvCalculado = 'K';
        } else {
            $dvCalculado = (string)$dvCalculado;
        }

        return $dv == $dvCalculado;
    }

    public static function formatearRutChileno($rut) {
        $rut = preg_replace('/[^0-9kK]/', '', strtoupper($rut));

        if (strlen($rut) < 2) {
            return $rut;
        }

        $numero = substr($rut, 0, -1);
        $dv = substr($rut, -1);

        // Formatear con puntos
        $numero = number_format($numero, 0, '', '.');

        return $numero . '-' . $dv;
    }

    // ================================================================
    // VALIDACIÓN CUIT ARGENTINO
    // ================================================================

    public static function validarCuitArgentino($cuit) {
        $cuit = preg_replace('/[^0-9]/', '', $cuit);

        if (strlen($cuit) != 11) {
            return false;
        }

        $multiplicadores = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
        $suma = 0;

        for ($i = 0; $i < 10; $i++) {
            $suma += $cuit[$i] * $multiplicadores[$i];
        }

        $resto = $suma % 11;
        $dvCalculado = 11 - $resto;

        if ($dvCalculado == 11) {
            $dvCalculado = 0;
        } elseif ($dvCalculado == 10) {
            return false; // CUIT inválido
        }

        return $cuit[10] == $dvCalculado;
    }

    public static function formatearCuitArgentino($cuit) {
        $cuit = preg_replace('/[^0-9]/', '', $cuit);

        if (strlen($cuit) != 11) {
            return $cuit;
        }

        return substr($cuit, 0, 2) . '-' . substr($cuit, 2, 8) . '-' . substr($cuit, 10, 1);
    }

    // ================================================================
    // VALIDACIÓN RFC MEXICANO
    // ================================================================

    public static function validarRfcMexicano($rfc) {
        $rfc = strtoupper(preg_replace('/[^A-Z0-9]/', '', $rfc));

        // RFC persona moral: 12 caracteres
        // RFC persona física: 13 caracteres
        if (strlen($rfc) == 12) {
            // Persona moral: 3 letras + 6 dígitos + 3 caracteres
            return preg_match('/^[A-Z]{3}[0-9]{6}[A-Z0-9]{3}$/', $rfc);
        } elseif (strlen($rfc) == 13) {
            // Persona física: 4 letras + 6 dígitos + 3 caracteres
            return preg_match('/^[A-Z]{4}[0-9]{6}[A-Z0-9]{3}$/', $rfc);
        }

        return false;
    }

    public static function formatearRfcMexicano($rfc) {
        return strtoupper(preg_replace('/[^A-Z0-9]/', '', $rfc));
    }

    // ================================================================
    // VALIDACIÓN RUC PERUANO
    // ================================================================

    public static function validarRucPeruano($ruc) {
        $ruc = preg_replace('/[^0-9]/', '', $ruc);

        if (strlen($ruc) != 11) {
            return false;
        }

        // Debe empezar con 10, 15, 17 o 20
        $prefijo = substr($ruc, 0, 2);
        if (!in_array($prefijo, ['10', '15', '17', '20'])) {
            return false;
        }

        return true;
    }

    public static function formatearRucPeruano($ruc) {
        return preg_replace('/[^0-9]/', '', $ruc);
    }

    // ================================================================
    // VALIDACIÓN NIT COLOMBIANO
    // ================================================================

    public static function validarNitColombiano($nit) {
        $nit = preg_replace('/[^0-9]/', '', $nit);

        if (strlen($nit) < 9 || strlen($nit) > 10) {
            return false;
        }

        $numero = substr($nit, 0, -1);
        $dv = substr($nit, -1);

        $multiplicadores = [71, 67, 59, 53, 47, 43, 41, 37, 29, 23, 19, 17, 13, 7, 3];
        $suma = 0;
        $longitud = strlen($numero);

        for ($i = 0; $i < $longitud; $i++) {
            $suma += $numero[$longitud - 1 - $i] * $multiplicadores[$i];
        }

        $resto = $suma % 11;
        $dvCalculado = $resto > 1 ? 11 - $resto : $resto;

        return $dv == $dvCalculado;
    }

    public static function formatearNitColombiano($nit) {
        $nit = preg_replace('/[^0-9]/', '', $nit);

        if (strlen($nit) < 9) {
            return $nit;
        }

        $numero = substr($nit, 0, -1);
        $dv = substr($nit, -1);

        return number_format($numero, 0, '', '.') . '-' . $dv;
    }

    // ================================================================
    // VALIDACIÓN CNPJ BRASILEÑO
    // ================================================================

    public static function validarCnpjBrasileno($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (strlen($cnpj) != 14) {
            return false;
        }

        // Verificar si todos los dígitos son iguales
        if (preg_match('/^(\d)\1+$/', $cnpj)) {
            return false;
        }

        // Validar primer dígito verificador
        $soma = 0;
        $multiplicador = 5;
        for ($i = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $multiplicador;
            $multiplicador = ($multiplicador == 2) ? 9 : $multiplicador - 1;
        }
        $resto = $soma % 11;
        $dv1 = ($resto < 2) ? 0 : 11 - $resto;

        if ($cnpj[12] != $dv1) {
            return false;
        }

        // Validar segundo dígito verificador
        $soma = 0;
        $multiplicador = 6;
        for ($i = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $multiplicador;
            $multiplicador = ($multiplicador == 2) ? 9 : $multiplicador - 1;
        }
        $resto = $soma % 11;
        $dv2 = ($resto < 2) ? 0 : 11 - $resto;

        return $cnpj[13] == $dv2;
    }

    public static function formatearCnpjBrasileno($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (strlen($cnpj) != 14) {
            return $cnpj;
        }

        return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
    }

    // ================================================================
    // VALIDACIÓN EIN ESTADOUNIDENSE
    // ================================================================

    public static function validarEinEstadounidense($ein) {
        $ein = preg_replace('/[^0-9]/', '', $ein);

        if (strlen($ein) != 9) {
            return false;
        }

        // EIN válidos empiezan con ciertos prefijos
        $prefijo = substr($ein, 0, 2);
        $prefijosValidos = ['01', '02', '03', '04', '05', '06', '10', '11', '12', '13', '14', '15', '16', '20', '21', '22', '23', '24', '25', '26', '27', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '50', '51', '52', '53', '54', '55', '56', '57', '58', '59', '60', '61', '62', '63', '64', '65', '66', '67', '68', '71', '72', '73', '74', '75', '76', '77', '80', '81', '82', '83', '84', '85', '86', '87', '88', '90', '91', '92', '93', '94', '95', '98', '99'];

        return in_array($prefijo, $prefijosValidos);
    }

    public static function formatearEinEstadounidense($ein) {
        $ein = preg_replace('/[^0-9]/', '', $ein);

        if (strlen($ein) != 9) {
            return $ein;
        }

        return substr($ein, 0, 2) . '-' . substr($ein, 2);
    }

    // ================================================================
    // VALIDACIÓN NIF/CIF ESPAÑOL
    // ================================================================

    public static function validarNifEspanol($nif) {
        $nif = strtoupper(preg_replace('/[^A-Z0-9]/', '', $nif));

        if (strlen($nif) != 9) {
            return false;
        }

        $letra = $nif[0];
        $numero = substr($nif, 1, 7);
        $control = $nif[8];

        if (preg_match('/[ABCDEFGHJNPQRSUVW]/', $letra)) {
            // CIF (persona jurídica)
            $suma = 0;
            for ($i = 1; $i < 8; $i++) {
                if ($i % 2 == 0) {
                    $suma += intval($numero[$i]);
                } else {
                    $doble = intval($numero[$i]) * 2;
                    $suma += $doble >= 10 ? $doble - 9 : $doble;
                }
            }

            $unidad = $suma % 10;
            $digitoControl = $unidad != 0 ? 10 - $unidad : 0;

            if (is_numeric($control)) {
                return $control == $digitoControl;
            } else {
                $letrasControl = 'JABCDEFGHI';
                return $control == $letrasControl[$digitoControl];
            }
        } else {
            // NIF (persona física)
            $letrasNif = 'TRWAGMYFPDXBNJZSQVHLCKE';
            $letraCalculada = $letrasNif[intval($numero) % 23];
            return $control == $letraCalculada;
        }
    }

    public static function formatearNifEspanol($nif) {
        return strtoupper(preg_replace('/[^A-Z0-9]/', '', $nif));
    }

    // ================================================================
    // VALIDACIÓN GENÉRICA POR PAÍS
    // ================================================================

    public static function validarIdentificadorPorPais($pais, $identificador) {
        switch ($pais) {
            case 'CL':
                return self::validarRutChileno($identificador);
            case 'AR':
                return self::validarCuitArgentino($identificador);
            case 'MX':
                return self::validarRfcMexicano($identificador);
            case 'PE':
                return self::validarRucPeruano($identificador);
            case 'CO':
                return self::validarNitColombiano($identificador);
            case 'BR':
                return self::validarCnpjBrasileno($identificador);
            case 'US':
                return self::validarEinEstadounidense($identificador);
            case 'ES':
                return self::validarNifEspanol($identificador);
            default:
                // Para países sin validación específica, solo verificar que no esté vacío
                return !empty($identificador) && strlen($identificador) >= 5;
        }
    }

    public static function formatearIdentificadorPorPais($pais, $identificador) {
        switch ($pais) {
            case 'CL':
                return self::formatearRutChileno($identificador);
            case 'AR':
                return self::formatearCuitArgentino($identificador);
            case 'MX':
                return self::formatearRfcMexicano($identificador);
            case 'PE':
                return self::formatearRucPeruano($identificador);
            case 'CO':
                return self::formatearNitColombiano($identificador);
            case 'BR':
                return self::formatearCnpjBrasileno($identificador);
            case 'US':
                return self::formatearEinEstadounidense($identificador);
            case 'ES':
                return self::formatearNifEspanol($identificador);
            default:
                return trim($identificador);
        }
    }

    // ================================================================
    // VALIDACIÓN DE EMAIL
    // ================================================================

    public static function validarEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // ================================================================
    // VALIDACIÓN DE CONTRASEÑA
    // ================================================================

    public static function validarPassword($password) {
        // Mínimo 8 caracteres, al menos una mayúscula, una minúscula, un número
        if (strlen($password) < 8) {
            return ['valido' => false, 'mensaje' => 'La contraseña debe tener al menos 8 caracteres'];
        }

        if (!preg_match('/[A-Z]/', $password)) {
            return ['valido' => false, 'mensaje' => 'La contraseña debe tener al menos una letra mayúscula'];
        }

        if (!preg_match('/[a-z]/', $password)) {
            return ['valido' => false, 'mensaje' => 'La contraseña debe tener al menos una letra minúscula'];
        }

        if (!preg_match('/[0-9]/', $password)) {
            return ['valido' => false, 'mensaje' => 'La contraseña debe tener al menos un número'];
        }

        return ['valido' => true, 'mensaje' => 'Contraseña válida'];
    }

    public static function calcularFortalezaPassword($password) {
        $fortaleza = 0;

        // Longitud
        $fortaleza += min(strlen($password) * 4, 40);

        // Mayúsculas
        if (preg_match('/[A-Z]/', $password)) {
            $fortaleza += 10;
        }

        // Minúsculas
        if (preg_match('/[a-z]/', $password)) {
            $fortaleza += 10;
        }

        // Números
        if (preg_match('/[0-9]/', $password)) {
            $fortaleza += 10;
        }

        // Caracteres especiales
        if (preg_match('/[^A-Za-z0-9]/', $password)) {
            $fortaleza += 20;
        }

        // Variedad de caracteres
        $caracteres = array_unique(str_split($password));
        $fortaleza += min(count($caracteres) * 2, 20);

        $fortaleza = min($fortaleza, 100);

        if ($fortaleza < 40) {
            return ['score' => $fortaleza, 'nivel' => 'debil', 'color' => '#ef4444'];
        } elseif ($fortaleza < 70) {
            return ['score' => $fortaleza, 'nivel' => 'media', 'color' => '#f59e0b'];
        } else {
            return ['score' => $fortaleza, 'nivel' => 'fuerte', 'color' => '#10b981'];
        }
    }

    public static function generarPasswordSeguro($longitud = 12) {
        $mayusculas = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $minusculas = 'abcdefghjkmnpqrstuvwxyz';
        $numeros = '23456789';
        $especiales = '!@#$%&*';

        $password = '';
        $password .= $mayusculas[random_int(0, strlen($mayusculas) - 1)];
        $password .= $minusculas[random_int(0, strlen($minusculas) - 1)];
        $password .= $numeros[random_int(0, strlen($numeros) - 1)];
        $password .= $especiales[random_int(0, strlen($especiales) - 1)];

        $todos = $mayusculas . $minusculas . $numeros . $especiales;

        for ($i = 4; $i < $longitud; $i++) {
            $password .= $todos[random_int(0, strlen($todos) - 1)];
        }

        return str_shuffle($password);
    }

    // ================================================================
    // VALIDACIÓN DE TELÉFONO
    // ================================================================

    public static function validarTelefono($telefono, $pais = 'CL') {
        $telefono = preg_replace('/[^0-9+]/', '', $telefono);

        if (empty($telefono)) {
            return false;
        }

        // Validación básica: entre 8 y 15 dígitos
        $longitud = strlen($telefono);
        return $longitud >= 8 && $longitud <= 15;
    }

    public static function formatearTelefono($telefono, $pais = 'CL') {
        $telefono = preg_replace('/[^0-9]/', '', $telefono);

        if ($pais == 'CL' && strlen($telefono) == 9) {
            // Formato chileno: +56 9 8574 5559
            return '+56 ' . substr($telefono, 0, 1) . ' ' . substr($telefono, 1, 4) . ' ' . substr($telefono, 5, 4);
        }

        return $telefono;
    }
}
