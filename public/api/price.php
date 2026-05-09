<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/../../app/pricing.php';

require_login();

$payload = read_json_body();
if (!verify_csrf($payload['csrf'] ?? null)) {
    json_response(['error' => '页面会话已过期，请刷新后重试。'], 419);
}

$input = $payload['inputs'] ?? [];
if (!is_array($input)) {
    json_response(['error' => '测算参数格式不正确。'], 422);
}

$result = calculate_pricing($input);
json_response(['result' => $result]);

