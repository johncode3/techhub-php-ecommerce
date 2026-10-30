<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define("APP_URL", dirname(__DIR__));

$localConfig = __DIR__ . '/config.local.php';
if (file_exists($localConfig)) {
    require_once $localConfig;
}

define("SERVER_NAME", getenv("TECHHUB_DB_DSN") ?: "mysql:host=localhost;dbname=techhub;charset=utf8mb4;");
define("USER_NAME", getenv("TECHHUB_DB_USER") ?: "root");
define("PASSWORD", getenv("TECHHUB_DB_PASSWORD") ?: "");

require_once __DIR__ . '/functions.php';

require_once dirname(__DIR__) . '/database/db.php';

define("PAYPAL_CLIENT_ID", getenv("PAYPAL_CLIENT_ID") ?: "");
