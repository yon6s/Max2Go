<?php
declare(strict_types=1);

require __DIR__ . '/../../app/bootstrap.php';
require __DIR__ . '/../../app/contract_generator.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['download'])) {
    $path = contract_download_path((string)$_GET['download']);
    if ($path === null) {
        http_response_code(404);
        echo '合同文件不存在或已被清理。';
        exit;
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="' . basename($path) . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
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
    json_response(['error' => '合同参数格式不正确。'], 422);
}

if (isset($_GET['preview'])) {
    $rows = contract_rent_rows($input);
    json_response(['rows' => $rows]);
}

try {
    $result = generate_contract_docx($input);
} catch (Throwable $error) {
    json_response(['error' => '合同生成失败：' . $error->getMessage()], 500);
}

json_response([
    'filename' => $result['filename'],
    'downloadUrl' => $result['url'],
]);
