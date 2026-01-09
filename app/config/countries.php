<?php
/**
 * Configuración de Países
 */

return [
    'CL' => [
        'name' => 'Chile',
        'code' => 'CL',
        'phone_code' => '+56',
        'currency' => 'CLP',
        'timezone' => 'America/Santiago',
        'date_format' => 'DD/MM/YYYY',
        'identifier_type' => 'RUT',
        'identifier_format' => 'XX.XXX.XXX-X',
        'tax_name' => 'IVA',
        'tax_rate' => 19.00,
        'integrations' => ['SII', 'Previred']
    ],
    'AR' => [
        'name' => 'Argentina',
        'code' => 'AR',
        'phone_code' => '+54',
        'currency' => 'ARS',
        'timezone' => 'America/Buenos_Aires',
        'date_format' => 'DD/MM/YYYY',
        'identifier_type' => 'CUIT',
        'identifier_format' => 'XX-XXXXXXXX-X',
        'tax_name' => 'IVA',
        'tax_rate' => 21.00,
        'integrations' => ['AFIP']
    ],
    'PE' => [
        'name' => 'Perú',
        'code' => 'PE',
        'phone_code' => '+51',
        'currency' => 'PEN',
        'timezone' => 'America/Lima',
        'date_format' => 'DD/MM/YYYY',
        'identifier_type' => 'RUC',
        'identifier_format' => 'XXXXXXXXXXX',
        'tax_name' => 'IGV',
        'tax_rate' => 18.00,
        'integrations' => ['SUNAT']
    ],
    'CO' => [
        'name' => 'Colombia',
        'code' => 'CO',
        'phone_code' => '+57',
        'currency' => 'COP',
        'timezone' => 'America/Bogota',
        'date_format' => 'DD/MM/YYYY',
        'identifier_type' => 'NIT',
        'identifier_format' => 'XXXXXXXX-X',
        'tax_name' => 'IVA',
        'tax_rate' => 19.00,
        'integrations' => ['DIAN']
    ],
    'MX' => [
        'name' => 'México',
        'code' => 'MX',
        'phone_code' => '+52',
        'currency' => 'MXN',
        'timezone' => 'America/Mexico_City',
        'date_format' => 'DD/MM/YYYY',
        'identifier_type' => 'RFC',
        'identifier_format' => 'XXXX000000XXX',
        'tax_name' => 'IVA',
        'tax_rate' => 16.00,
        'integrations' => ['SAT']
    ],
    'BR' => [
        'name' => 'Brasil',
        'code' => 'BR',
        'phone_code' => '+55',
        'currency' => 'BRL',
        'timezone' => 'America/Sao_Paulo',
        'date_format' => 'DD/MM/YYYY',
        'identifier_type' => 'CNPJ',
        'identifier_format' => 'XX.XXX.XXX/0001-XX',
        'tax_name' => 'ICMS',
        'tax_rate' => 18.00,
        'integrations' => []
    ],
    'US' => [
        'name' => 'Estados Unidos',
        'code' => 'US',
        'phone_code' => '+1',
        'currency' => 'USD',
        'timezone' => 'America/New_York',
        'date_format' => 'MM/DD/YYYY',
        'identifier_type' => 'EIN',
        'identifier_format' => 'XX-XXXXXXX',
        'tax_name' => 'Sales Tax',
        'tax_rate' => 0.00,
        'integrations' => []
    ],
    'ES' => [
        'name' => 'España',
        'code' => 'ES',
        'phone_code' => '+34',
        'currency' => 'EUR',
        'timezone' => 'Europe/Madrid',
        'date_format' => 'DD/MM/YYYY',
        'identifier_type' => 'CIF',
        'identifier_format' => 'X0000000X',
        'tax_name' => 'IVA',
        'tax_rate' => 21.00,
        'integrations' => ['AEAT']
    ],
    'FR' => [
        'name' => 'Francia',
        'code' => 'FR',
        'phone_code' => '+33',
        'currency' => 'EUR',
        'timezone' => 'Europe/Paris',
        'date_format' => 'DD/MM/YYYY',
        'identifier_type' => 'SIRET',
        'identifier_format' => 'XXXXXXXXXXXXXX',
        'tax_name' => 'TVA',
        'tax_rate' => 20.00,
        'integrations' => []
    ],
    'DE' => [
        'name' => 'Alemania',
        'code' => 'DE',
        'phone_code' => '+49',
        'currency' => 'EUR',
        'timezone' => 'Europe/Berlin',
        'date_format' => 'DD/MM/YYYY',
        'identifier_type' => 'USt-IdNr',
        'identifier_format' => 'DEXXXXXXXXX',
        'tax_name' => 'MwSt',
        'tax_rate' => 19.00,
        'integrations' => []
    ],
    'IT' => [
        'name' => 'Italia',
        'code' => 'IT',
        'phone_code' => '+39',
        'currency' => 'EUR',
        'timezone' => 'Europe/Rome',
        'date_format' => 'DD/MM/YYYY',
        'identifier_type' => 'Partita IVA',
        'identifier_format' => 'XXXXXXXXXXX',
        'tax_name' => 'IVA',
        'tax_rate' => 22.00,
        'integrations' => []
    ],
    'GB' => [
        'name' => 'Reino Unido',
        'code' => 'GB',
        'phone_code' => '+44',
        'currency' => 'GBP',
        'timezone' => 'Europe/London',
        'date_format' => 'DD/MM/YYYY',
        'identifier_type' => 'VAT',
        'identifier_format' => 'GBXXXXXXXXX',
        'tax_name' => 'VAT',
        'tax_rate' => 20.00,
        'integrations' => []
    ]
];
