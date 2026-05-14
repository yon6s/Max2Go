<?php
declare(strict_types=1);

require __DIR__ . '/../../app/bootstrap.php';
require __DIR__ . '/../../app/customer_timelines.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $projectKey = trim((string)($_GET['projectKey'] ?? ''));
    $customerName = trim((string)($_GET['customerName'] ?? ''));
    $limit = (int)($_GET['limit'] ?? 20);
    json_response(['items' => customer_timeline_list($projectKey, $customerName, $limit)]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => '不支持的请求方式。'], 405);
}

$payload = read_json_body();
if (!verify_csrf($payload['csrf'] ?? null)) {
    json_response(['error' => '页面会话已过期，请刷新后重试。'], 419);
}

if (($payload['stage'] ?? '') !== 'recap') {
    json_response(['error' => '只有一线记录与客户洞察模块可以保存客户时间线。'], 422);
}

try {
    $record = customer_timeline_add($payload);
} catch (InvalidArgumentException $error) {
    json_response(['error' => $error->getMessage()], 422);
} catch (Throwable $error) {
    json_response(['error' => '客户时间线保存失败：' . $error->getMessage()], 500);
}

json_response(['record' => $record]);
