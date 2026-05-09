<?php
declare(strict_types=1);

require __DIR__ . '/../../app/bootstrap.php';
require __DIR__ . '/../../app/prompts.php';

require_login();

$payload = read_json_body();
if (!verify_csrf($payload['csrf'] ?? null)) {
    json_response(['error' => '页面会话已过期，请刷新后重试。'], 419);
}

$stage = (string)($payload['stage'] ?? '');
$allowedStages = ['lead', 'space', 'pricing', 'recap', 'proposal', 'negotiation', 'contract', 'dashboard'];
if (!in_array($stage, $allowedStages, true)) {
    json_response(['error' => '未知模块，请刷新页面后重试。'], 400);
}

$apiKey = trim((string)app_config('deepseek_api_key', ''));
if ($apiKey === '' && app_config('demo_mode_when_no_key', true)) {
    json_response(['content' => demo_stage_result($stage, $payload), 'demo' => true]);
}

if ($apiKey === '') {
    json_response(['error' => 'DeepSeek API Key 尚未配置。'], 500);
}

$messages = stage_prompt($stage, $payload);
$baseUrl = rtrim((string)app_config('deepseek_base_url', 'https://api.deepseek.com'), '/');
$model = (string)app_config('deepseek_model', 'deepseek-v4-flash');

$body = [
    'model' => $model,
    'messages' => $messages,
    'temperature' => 0.35,
    'max_tokens' => 1800,
];

$ch = curl_init($baseUrl . '/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ],
    CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_UNICODE),
    CURLOPT_CONNECTTIMEOUT => 12,
    CURLOPT_TIMEOUT => 60,
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $curlError !== '') {
    json_response(['error' => 'AI接口连接失败：' . $curlError], 502);
}

$data = json_decode($response, true);
if ($status < 200 || $status >= 300) {
    $message = $data['error']['message'] ?? $response;
    json_response(['error' => 'AI接口返回异常：' . $message], 502);
}

$content = $data['choices'][0]['message']['content'] ?? '';
if (!is_string($content) || trim($content) === '') {
    json_response(['error' => 'AI接口没有返回有效内容。'], 502);
}

json_response(['content' => trim($content), 'demo' => false]);
