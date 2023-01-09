<?php

require_once __DIR__ . '/env.php';
ob_start();
session_start([
 'cookie_secure' => SECURE,
 "cookie_path" => '/',
 'cookie_domain' => WEB,
 'cookie_httponly' => HTTPONLY,
 'cookie_samesite' => SAMESITE
]);
$timezone = date_default_timezone_set("Asia/Kolkata");
if (json_decode(file_get_contents('php://input'), true)) {
 $_POST = json_decode(file_get_contents('php://input'), true);
}
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Route/Web.php';
if (isset($_SESSION)) {
 if (isset($_SESSION['user_id'])) {
  session_regenerate_id();
 }
}
