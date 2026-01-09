<?php
/**
 * Validadores por País - Formateo Automático
 * Soporta 12 países con validación real
 */

class CountryValidator {

    // Validar según país
    public static function validate($value, $countryCode) {
        $method = 'validate' . strtoupper($countryCode);
        if (method_exists(self::class, $method)) {
            return self::$method($value);
        }
        return ['valid' => false, 'error' => 'País no soportado'];
    }

    // Formatear según país
    public static function format($value, $countryCode) {
        $method = 'format' . strtoupper($countryCode);
        if (method_exists(self::class, $method)) {
            return self::$method($value);
        }
        return $value;
    }

    // ==================== CHILE ====================
    public static function validateCL($rut) {
        $rut = preg_replace('/[^0-9kK]/', '', $rut);
        if (strlen($rut) < 2) return ['valid' => false, 'error' => 'RUT inválido'];

        $dv = substr($rut, -1);
        $numero = substr($rut, 0, -1);

        if (!is_numeric($numero)) return ['valid' => false, 'error' => 'RUT debe ser numérico'];

        $dvCalculado = self::calculateDVChile($numero);

        if (strtoupper($dv) !== strtoupper($dvCalculado)) {
            return ['valid' => false, 'error' => 'Dígito verificador inválido'];
        }

        return ['valid' => true, 'formatted' => self::formatCL($rut)];
    }

    public static function formatCL($rut) {
        $rut = preg_replace('/[^0-9kK]/', '', $rut);
        if (strlen($rut) < 2) return $rut;

        $dv = substr($rut, -1);
        $numero = substr($rut, 0, -1);

        return number_format($numero, 0, '', '.') . '-' . strtoupper($dv);
    }

    private static function calculateDVChile($numero) {
        $suma = 0;
        $multiplo = 2;

        for ($i = strlen($numero) - 1; $i >= 0; $i--) {
            $suma += $numero[$i] * $multiplo;
            $multiplo = $multiplo < 7 ? $multiplo + 1 : 2;
        }

        $resto = $suma % 11;
        $dv = 11 - $resto;

        if ($dv == 11) return '0';
        if ($dv == 10) return 'K';
        return (string)$dv;
    }

    // ==================== ARGENTINA ====================
    public static function validateAR($cuit) {
        $cuit = preg_replace('/[^0-9]/', '', $cuit);
        if (strlen($cuit) != 11) {
            return ['valid' => false, 'error' => 'CUIT debe tener 11 dígitos'];
        }

        $mult = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
        $suma = 0;

        for ($i = 0; $i < 10; $i++) {
            $suma += $cuit[$i] * $mult[$i];
        }

        $verificador = (11 - ($suma % 11)) % 11;

        if ($verificador != $cuit[10]) {
            return ['valid' => false, 'error' => 'CUIT inválido'];
        }

        return ['valid' => true, 'formatted' => self::formatAR($cuit)];
    }

    public static function formatAR($cuit) {
        $cuit = preg_replace('/[^0-9]/', '', $cuit);
        if (strlen($cuit) != 11) return $cuit;
        return substr($cuit, 0, 2) . '-' . substr($cuit, 2, 8) . '-' . substr($cuit, 10, 1);
    }

    // ==================== PERÚ ====================
    public static function validatePE($ruc) {
        $ruc = preg_replace('/[^0-9]/', '', $ruc);
        if (strlen($ruc) != 11) {
            return ['valid' => false, 'error' => 'RUC debe tener 11 dígitos'];
        }
        if (!in_array(substr($ruc, 0, 2), ['10', '15', '17', '20'])) {
            return ['valid' => false, 'error' => 'RUC inválido'];
        }
        return ['valid' => true, 'formatted' => $ruc];
    }

    public static function formatPE($ruc) {
        return preg_replace('/[^0-9]/', '', $ruc);
    }

    // ==================== OTROS PAÍSES ====================
    public static function validateCO($nit) {
        $nit = preg_replace('/[^0-9]/', '', $nit);
        if (strlen($nit) < 9 || strlen($nit) > 10) {
            return ['valid' => false, 'error' => 'NIT inválido'];
        }
        return ['valid' => true, 'formatted' => self::formatCO($nit)];
    }

    public static function formatCO($nit) {
        $nit = preg_replace('/[^0-9]/', '', $nit);
        if (strlen($nit) < 2) return $nit;
        return substr($nit, 0, -1) . '-' . substr($nit, -1);
    }

    public static function validateMX($rfc) {
        $rfc = strtoupper(preg_replace('/[^A-Z0-9]/', '', $rfc));
        if (strlen($rfc) != 12 && strlen($rfc) != 13) {
            return ['valid' => false, 'error' => 'RFC debe tener 12 o 13 caracteres'];
        }
        return ['valid' => true, 'formatted' => $rfc];
    }

    public static function formatMX($rfc) {
        return strtoupper(preg_replace('/[^A-Z0-9]/', '', $rfc));
    }

    public static function validateBR($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        if (strlen($cnpj) != 14) {
            return ['valid' => false, 'error' => 'CNPJ debe tener 14 dígitos'];
        }
        return ['valid' => true, 'formatted' => self::formatBR($cnpj)];
    }

    public static function formatBR($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        if (strlen($cnpj) != 14) return $cnpj;
        return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
    }

    public static function validateUS($ein) {
        $ein = preg_replace('/[^0-9]/', '', $ein);
        if (strlen($ein) != 9) {
            return ['valid' => false, 'error' => 'EIN debe tener 9 dígitos'];
        }
        return ['valid' => true, 'formatted' => self::formatUS($ein)];
    }

    public static function formatUS($ein) {
        $ein = preg_replace('/[^0-9]/', '', $ein);
        if (strlen($ein) != 9) return $ein;
        return substr($ein, 0, 2) . '-' . substr($ein, 2);
    }

    public static function validateES($cif) {
        $cif = strtoupper(preg_replace('/[^A-Z0-9]/', '', $cif));
        if (strlen($cif) != 9) {
            return ['valid' => false, 'error' => 'CIF/NIF debe tener 9 caracteres'];
        }
        return ['valid' => true, 'formatted' => $cif];
    }

    public static function formatES($cif) {
        return strtoupper(preg_replace('/[^A-Z0-9]/', '', $cif));
    }

    public static function validateFR($siret) {
        $siret = preg_replace('/[^0-9]/', '', $siret);
        if (strlen($siret) != 14) {
            return ['valid' => false, 'error' => 'SIRET debe tener 14 dígitos'];
        }
        return ['valid' => true, 'formatted' => $siret];
    }

    public static function formatFR($siret) {
        return preg_replace('/[^0-9]/', '', $siret);
    }

    public static function validateDE($ust) {
        $ust = strtoupper(preg_replace('/[^A-Z0-9]/', '', $ust));
        if (!preg_match('/^DE[0-9]{9}$/', $ust)) {
            return ['valid' => false, 'error' => 'USt-IdNr inválido'];
        }
        return ['valid' => true, 'formatted' => $ust];
    }

    public static function formatDE($ust) {
        return strtoupper(preg_replace('/[^A-Z0-9]/', '', $ust));
    }

    public static function validateIT($iva) {
        $iva = preg_replace('/[^0-9]/', '', $iva);
        if (strlen($iva) != 11) {
            return ['valid' => false, 'error' => 'Partita IVA debe tener 11 dígitos'];
        }
        return ['valid' => true, 'formatted' => $iva];
    }

    public static function formatIT($iva) {
        return preg_replace('/[^0-9]/', '', $iva);
    }

    public static function validateGB($vat) {
        $vat = strtoupper(preg_replace('/[^A-Z0-9]/', '', $vat));
        if (!preg_match('/^GB[0-9]{9,12}$/', $vat)) {
            return ['valid' => false, 'error' => 'VAT Number inválido'];
        }
        return ['valid' => true, 'formatted' => $vat];
    }

    public static function formatGB($vat) {
        return strtoupper(preg_replace('/[^A-Z0-9]/', '', $vat));
    }
}
