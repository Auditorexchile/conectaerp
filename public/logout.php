<?php
define('CONECTA_ERP', true);
require_once __DIR__ . '/../app/core/bootstrap.php';

// Logout
Session::logout();
redirect('/public/login.php');
