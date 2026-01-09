<?php
/**
 * Helpers - Funciones auxiliares globales
 */

function config($key, $default = null) {
    static $config = null;

    if ($config === null) {
        $config = require CONFIG_PATH . '/app.php';
    }

    return $config[$key] ?? $default;
}

function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function back() {
    redirect($_SERVER['HTTP_REFERER'] ?? '/');
}

function old($key, $default = '') {
    return $_POST[$key] ?? $default;
}

function formatMoney($amount, $currency = 'CLP') {
    $currencies = require CONFIG_PATH . '/currencies.php';
    $config = $currencies[$currency] ?? $currencies['CLP'];

    return $config['symbol'] . ' ' . number_format(
        $amount,
        $config['decimals'],
        $config['decimal_separator'],
        $config['thousands_separator']
    );
}

function formatDate($date, $format = null) {
    if (!$date) return '';

    $format = $format ?? config('date_format', 'd/m/Y');

    if (is_string($date)) {
        $date = new DateTime($date);
    }

    return $date->format($format);
}

function trans($key, $lang = null) {
    $lang = $lang ?? SessionManager::get('idioma_codigo', 'es');

    // Aquí iría la lógica de traducciones
    // Por ahora retorna la key
    return $key;
}

function asset($path) {
    return '/assets/' . ltrim($path, '/');
}

function url($path = '') {
    return rtrim(config('url'), '/') . '/' . ltrim($path, '/');
}

function isProduction() {
    return config('env') === 'production';
}

function isDevelopment() {
    return config('env') === 'development';
}

function generatePassword($length = 16) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*';
    $password = '';

    // Al menos una de cada tipo
    $password .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[random_int(0, 25)];
    $password .= 'abcdefghijklmnopqrstuvwxyz'[random_int(0, 25)];
    $password .= '0123456789'[random_int(0, 9)];
    $password .= '!@#$%&*'[random_int(0, 6)];

    // Completar el resto
    for ($i = 4; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }

    return str_shuffle($password);
}

function json_response($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function error_response($message, $code = 400) {
    json_response(['error' => $message], $code);
}

function success_response($data, $message = 'Success') {
    json_response(['success' => true, 'message' => $message, 'data' => $data]);
}

function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);

    return empty($text) ? 'n-a' : $text;
}
