<?php

require_once __DIR__ . '/vendor/autoload.php';

use Api\ContactController;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Environment
$debug       = $_ENV['APP_DEBUG'] === 'true';
$allowedOrigin = $_ENV['CORS_ALLOWED_ORIGIN'];

// Error reporting
if ($debug) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// CORS headers
header("Access-Control-Allow-Origin: $allowedOrigin");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Boot controller
$controller = new ContactController();
$controller->handle();