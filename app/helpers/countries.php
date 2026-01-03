<?php
/**
 * CONECTA ERP - HELPERS DE PAÍSES E IDIOMAS
 *
 * Funciones auxiliares para manejar países, idiomas, monedas
 */

class CountryHelper {

    /**
     * Obtener configuración completa de un país
     */
    public static function getCountryConfig($paisCodigo) {
        $db = db();

        $sql = "SELECT p.*, pc.*
                FROM paises p
                LEFT JOIN paises_configuracion pc ON p.codigo = pc.pais_codigo
                WHERE p.codigo = :codigo
                LIMIT 1";

        return $db->selectOne($sql, ['codigo' => $paisCodigo]);
    }

    /**
     * Obtener lista de todos los países activos
     */
    public static function getActiveCountries() {
        $db = db();

        $sql = "SELECT codigo, nombre, bandera, idioma_principal
                FROM paises
                WHERE activo = 1
                ORDER BY orden ASC, nombre ASC";

        return $db->select($sql);
    }

    /**
     * Obtener nombre del tipo de identificador por país
     */
    public static function getIdentifierType($paisCodigo) {
        $config = self::getCountryConfig($paisCodigo);
        return $config ? $config['identificador_tipo'] : 'ID';
    }

    /**
     * Obtener formato del identificador por país
     */
    public static function getIdentifierFormat($paisCodigo) {
        $config = self::getCountryConfig($paisCodigo);
        return $config ? $config['identificador_formato'] : '';
    }

    /**
     * Obtener configuración de moneda por país
     */
    public static function getCurrencyInfo($paisCodigo) {
        $config = self::getCountryConfig($paisCodigo);

        if (!$config) {
            return ['codigo' => 'USD', 'nombre' => 'Dólar', 'simbolo' => '$', 'decimales' => 2];
        }

        return [
            'codigo' => $config['moneda_codigo'],
            'nombre' => $config['moneda_nombre'],
            'simbolo' => $config['moneda_simbolo'],
            'decimales' => $config['moneda_decimales']
        ];
    }

    /**
     * Formatear número según configuración del país
     */
    public static function formatNumber($numero, $paisCodigo, $decimales = 2) {
        $config = self::getCountryConfig($paisCodigo);

        if (!$config) {
            return number_format($numero, $decimales, '.', ',');
        }

        $decimalSep = $config['formato_numero_decimal'] ?: '.';
        $milesSep = $config['formato_numero_miles'] ?: ',';

        return number_format($numero, $decimales, $decimalSep, $milesSep);
    }

    /**
     * Formatear moneda según país
     */
    public static function formatCurrency($monto, $paisCodigo) {
        $config = self::getCurrencyInfo($paisCodigo);
        $montoFormateado = self::formatNumber($monto, $paisCodigo, $config['decimales']);

        return $config['simbolo'] . ' ' . $montoFormateado;
    }

    /**
     * Formatear fecha según país
     */
    public static function formatDate($fecha, $paisCodigo) {
        $config = self::getCountryConfig($paisCodigo);
        $formato = $config ? $config['formato_fecha'] : 'DD/MM/YYYY';

        // Convertir formato a PHP
        $formatoPHP = str_replace(
            ['DD', 'MM', 'YYYY', 'YY'],
            ['d', 'm', 'Y', 'y'],
            $formato
        );

        if (is_string($fecha)) {
            $fecha = new DateTime($fecha);
        }

        return $fecha->format($formatoPHP);
    }

    /**
     * Obtener regiones de un país
     */
    public static function getRegions($paisCodigo) {
        $db = db();

        $sql = "SELECT id, codigo, nombre, numero
                FROM regiones
                WHERE pais_codigo = :pais AND activo = 1
                ORDER BY orden ASC, nombre ASC";

        return $db->select($sql, ['pais' => $paisCodigo]);
    }

    /**
     * Obtener comunas de una región
     */
    public static function getCommunesByRegion($regionId) {
        $db = db();

        $sql = "SELECT id, codigo, nombre
                FROM comunas
                WHERE region_id = :region AND activo = 1
                ORDER BY nombre ASC";

        return $db->select($sql, ['region' => $regionId]);
    }

    /**
     * Obtener giros comerciales de un país
     */
    public static function getBusinessActivities($paisCodigo) {
        $db = db();

        $sql = "SELECT id, codigo, nombre, categoria
                FROM giros_comerciales
                WHERE pais_codigo = :pais AND activo = 1
                ORDER BY categoria ASC, nombre ASC";

        return $db->select($sql, ['pais' => $paisCodigo]);
    }

    /**
     * Verificar si un país tiene facturación electrónica
     */
    public static function hasElectronicInvoicing($paisCodigo) {
        $config = self::getCountryConfig($paisCodigo);
        return $config && $config['tiene_facturacion_electronica'] == 1;
    }

    /**
     * Verificar si un país tiene integración fiscal
     */
    public static function hasFiscalIntegration($paisCodigo) {
        $config = self::getCountryConfig($paisCodigo);
        return $config && $config['tiene_integracion_fiscal'] == 1;
    }

    /**
     * Obtener nombre de la entidad fiscal del país
     */
    public static function getFiscalEntity($paisCodigo) {
        $config = self::getCountryConfig($paisCodigo);
        return $config ? $config['nombre_entidad_fiscal'] : null;
    }

    /**
     * Obtener tasa de impuesto de ventas del país
     */
    public static function getSalesTaxRate($paisCodigo) {
        $config = self::getCountryConfig($paisCodigo);
        return $config ? floatval($config['impuesto_ventas_tasa']) : 0.0;
    }

    /**
     * Obtener nombre del impuesto de ventas del país
     */
    public static function getSalesTaxName($paisCodigo) {
        $config = self::getCountryConfig($paisCodigo);
        return $config ? $config['impuesto_ventas_nombre'] : 'IVA';
    }
}

class LanguageHelper {

    /**
     * Obtener todos los idiomas activos
     */
    public static function getActiveLanguages() {
        $db = db();

        $sql = "SELECT codigo, nombre, nombre_nativo, bandera, direccion_texto
                FROM idiomas
                WHERE activo = 1
                ORDER BY orden ASC, nombre ASC";

        return $db->select($sql);
    }

    /**
     * Obtener información de un idioma
     */
    public static function getLanguageInfo($codigoIdioma) {
        $db = db();

        $sql = "SELECT *
                FROM idiomas
                WHERE codigo = :codigo
                LIMIT 1";

        return $db->selectOne($sql, ['codigo' => $codigoIdioma]);
    }

    /**
     * Obtener traducción
     */
    public static function translate($clave, $idioma = 'es', $default = null) {
        $db = db();

        $sql = "SELECT texto
                FROM traducciones
                WHERE clave = :clave AND idioma_codigo = :idioma
                LIMIT 1";

        $result = $db->selectOne($sql, ['clave' => $clave, 'idioma' => $idioma]);

        if ($result) {
            return $result['texto'];
        }

        return $default !== null ? $default : $clave;
    }

    /**
     * Cargar todas las traducciones de un módulo
     */
    public static function loadModuleTranslations($modulo, $idioma = 'es') {
        $db = db();

        $sql = "SELECT clave, texto
                FROM traducciones
                WHERE modulo = :modulo AND idioma_codigo = :idioma";

        $results = $db->select($sql, ['modulo' => $modulo, 'idioma' => $idioma]);

        $translations = [];
        foreach ($results as $row) {
            $translations[$row['clave']] = $row['texto'];
        }

        return $translations;
    }
}

class CurrencyHelper {

    /**
     * Obtener todas las monedas activas
     */
    public static function getActiveCurrencies() {
        $db = db();

        $sql = "SELECT codigo, nombre, simbolo, decimales
                FROM monedas
                WHERE activo = 1
                ORDER BY codigo ASC";

        return $db->select($sql);
    }

    /**
     * Obtener tipo de cambio
     */
    public static function getExchangeRate($monedaOrigen, $monedaDestino, $fecha = null) {
        $db = db();

        if (!$fecha) {
            $fecha = date('Y-m-d');
        }

        // Buscar tipo de cambio exacto
        $sql = "SELECT tasa
                FROM tipos_cambio
                WHERE fecha = :fecha
                  AND moneda_origen = :origen
                  AND moneda_destino = :destino
                LIMIT 1";

        $result = $db->selectOne($sql, [
            'fecha' => $fecha,
            'origen' => $monedaOrigen,
            'destino' => $monedaDestino
        ]);

        if ($result) {
            return floatval($result['tasa']);
        }

        // Si no encuentra, buscar el más reciente
        $sql = "SELECT tasa
                FROM tipos_cambio
                WHERE fecha <= :fecha
                  AND moneda_origen = :origen
                  AND moneda_destino = :destino
                ORDER BY fecha DESC
                LIMIT 1";

        $result = $db->selectOne($sql, [
            'fecha' => $fecha,
            'origen' => $monedaOrigen,
            'destino' => $monedaDestino
        ]);

        return $result ? floatval($result['tasa']) : 1.0;
    }

    /**
     * Convertir monto entre monedas
     */
    public static function convert($monto, $monedaOrigen, $monedaDestino, $fecha = null) {
        if ($monedaOrigen == $monedaDestino) {
            return $monto;
        }

        $tasa = self::getExchangeRate($monedaOrigen, $monedaDestino, $fecha);
        return $monto * $tasa;
    }
}

class PlanHelper {

    /**
     * Obtener todos los planes activos
     */
    public static function getActivePlans() {
        $db = db();

        $sql = "SELECT *
                FROM planes_suscripcion
                WHERE activo = 1 AND visible_publico = 1
                ORDER BY orden ASC";

        return $db->select($sql);
    }

    /**
     * Obtener información de un plan
     */
    public static function getPlanInfo($codigoPlan) {
        $db = db();

        $sql = "SELECT *
                FROM planes_suscripcion
                WHERE codigo = :codigo
                LIMIT 1";

        return $db->selectOne($sql, ['codigo' => $codigoPlan]);
    }

    /**
     * Obtener precio de un plan para un país
     */
    public static function getPlanPrice($codigoPlan, $paisCodigo) {
        $plan = self::getPlanInfo($codigoPlan);

        if (!$plan) {
            return null;
        }

        $precios = json_decode($plan['precios_por_pais'], true);

        if (isset($precios[$paisCodigo])) {
            return $precios[$paisCodigo];
        }

        return $plan['precio_base_usd'];
    }

    /**
     * Verificar si un plan incluye un módulo
     */
    public static function planHasModule($codigoPlan, $codigoModulo) {
        $plan = self::getPlanInfo($codigoPlan);

        if (!$plan) {
            return false;
        }

        $modulos = json_decode($plan['modulos_incluidos'], true);
        return in_array($codigoModulo, $modulos);
    }
}
