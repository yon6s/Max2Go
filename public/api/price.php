<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/../../app/pricing.php';
require_once __DIR__ . '/../../app/excel_template.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_template') {
    if (!verify_csrf($_POST['csrf'] ?? null)) {
        json_response(['error' => '页面会话已过期，请刷新后重试。'], 419);
    }
    upload_pricing_template($_FILES['template'] ?? []);
    json_response(['ok' => true, 'template' => pricing_template_status()]);
}

$payload = read_json_body();
if (!verify_csrf($payload['csrf'] ?? null)) {
    json_response(['error' => '页面会话已过期，请刷新后重试。'], 419);
}

if (($payload['action'] ?? '') === 'template_status') {
    json_response(['template' => pricing_template_status()]);
}

$input = $payload['inputs'] ?? [];
if (!is_array($input)) {
    json_response(['error' => '测算参数格式不正确。'], 422);
}

$result = run_excel_pricing($input);
json_response(['result' => $result]);
