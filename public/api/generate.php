<?php
declare(strict_types=1);

require __DIR__ . '/../../app/bootstrap.php';
require __DIR__ . '/../../app/ai_client.php';
require __DIR__ . '/../../app/prompts.php';

require_login();

$payload = read_json_body();
if (!verify_csrf($payload['csrf'] ?? null)) {
    json_response(['error' => '页面会话已过期，请刷新后重试。'], 419);
}

$stage = (string)($payload['stage'] ?? '');
$allowedStages = ['tour', 'objection', 'pricing', 'recap', 'contract', 'floorplan', 'video'];
if (!in_array($stage, $allowedStages, true)) {
    json_response(['error' => '未知模块，请刷新页面后重试。'], 400);
}

$providerKey = ai_active_provider($payload);
$providers = ai_provider_configs();
$provider = $providers[$providerKey];

if ($providerKey === 'demo') {
    json_response([
        'content' => demo_stage_result($stage, $payload),
        'demo' => true,
        'provider' => $providerKey,
        'providerLabel' => $provider['label'],
        'model' => $provider['model'],
    ]);
}

if ($provider['api_key'] === '' && app_config('demo_mode_when_no_key', true)) {
    json_response([
        'content' => demo_stage_result($stage, $payload),
        'demo' => true,
        'provider' => $providerKey,
        'providerLabel' => $provider['label'],
        'model' => $provider['model'],
    ]);
}

if ($provider['api_key'] === '') {
    json_response(['error' => $provider['label'] . ' API Key 尚未配置。'], 500);
}

$messages = stage_prompt($stage, $payload);
try {
    $result = call_ai_chat($providerKey, $messages);
} catch (RuntimeException $error) {
    json_response(['error' => $error->getMessage()], 502);
}

json_response([
    'content' => $result['content'],
    'demo' => false,
    'provider' => $result['provider'],
    'providerLabel' => $result['providerLabel'],
    'model' => $result['model'],
    'usage' => $result['usage'],
]);
