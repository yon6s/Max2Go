<?php
declare(strict_types=1);

const APP_ROOT = __DIR__ . '/..';

$sessionPath = APP_ROOT . '/storage/sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0700, true);
}
ini_set('session.save_path', $sessionPath);

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);

$configFile = __DIR__ . '/config.php';
if (is_file($configFile)) {
    $config = require $configFile;
} else {
    $config = require __DIR__ . '/config.example.php';
}

function app_config(string $key, mixed $default = null): mixed
{
    global $config;
    return $config[$key] ?? $default;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['logged_in']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => '请先登录 MAX租赁AI工作台。'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function read_json_body(): array
{
    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf'];
}

function verify_csrf(?string $token): bool
{
    return is_string($token) && hash_equals($_SESSION['csrf'] ?? '', $token);
}
