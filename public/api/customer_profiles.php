<?php
declare(strict_types=1);

require __DIR__ . '/../../app/bootstrap.php';
require __DIR__ . '/../../app/customer_profiles.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $name = trim((string)($_GET['tenantName'] ?? ''));
    json_response(['profile' => customer_profile_find($name)]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => '不支持的请求方式。'], 405);
}

$payload = read_json_body();
if (!verify_csrf($payload['csrf'] ?? null)) {
    json_response(['error' => '页面会话已过期，请刷新后重试。'], 419);
}

$input = $payload['inputs'] ?? [];
if (!is_array($input)) {
    json_response(['error' => '客户档案参数格式不正确。'], 422);
}

try {
    $profile = customer_profile_upsert($input);
} catch (InvalidArgumentException $error) {
    json_response(['error' => $error->getMessage()], 422);
} catch (Throwable $error) {
    json_response(['error' => '客户档案保存失败：' . $error->getMessage()], 500);
}

json_response(['profile' => $profile]);
