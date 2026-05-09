<?php
declare(strict_types=1);

function ai_provider_configs(): array
{
    return [
        'deepseek' => [
            'label' => 'DeepSeek',
            'api_key' => trim((string)app_config('deepseek_api_key', '')),
            'base_url' => rtrim((string)app_config('deepseek_base_url', 'https://api.deepseek.com'), '/'),
            'model' => (string)app_config('deepseek_model', 'deepseek-v4-flash'),
        ],
        'qwen' => [
            'label' => '通义千问',
            'api_key' => trim((string)app_config('qwen_api_key', '')),
            'base_url' => rtrim((string)app_config('qwen_base_url', 'https://dashscope.aliyuncs.com/compatible-mode/v1'), '/'),
            'model' => (string)app_config('qwen_model', 'qwen-plus'),
        ],
    ];
}

function ai_active_provider(array $payload = []): string
{
    $requested = trim((string)($payload['provider'] ?? ''));
    $configured = trim((string)app_config('ai_provider', 'deepseek'));
    $provider = $requested !== '' ? $requested : $configured;
    return array_key_exists($provider, ai_provider_configs()) ? $provider : 'deepseek';
}

function ai_public_meta(): array
{
    $providers = ai_provider_configs();
    $items = [];
    foreach ($providers as $key => $provider) {
        $items[$key] = [
            'label' => $provider['label'],
            'model' => $provider['model'],
            'configured' => $provider['api_key'] !== '',
        ];
    }

    return [
        'active' => ai_active_provider(),
        'providers' => $items,
        'demoWhenNoKey' => (bool)app_config('demo_mode_when_no_key', true),
    ];
}

function call_ai_chat(string $providerKey, array $messages): array
{
    $providers = ai_provider_configs();
    if (!isset($providers[$providerKey])) {
        throw new RuntimeException('未知模型接口。');
    }

    $provider = $providers[$providerKey];
    if ($provider['api_key'] === '') {
        throw new RuntimeException($provider['label'] . ' API Key 尚未配置。');
    }

    $body = [
        'model' => $provider['model'],
        'messages' => $messages,
        'temperature' => 0.35,
        'max_tokens' => 1800,
    ];

    $ch = curl_init($provider['base_url'] . '/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $provider['api_key'],
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
        throw new RuntimeException($provider['label'] . ' 接口连接失败：' . $curlError);
    }

    $data = json_decode($response, true);
    if ($status < 200 || $status >= 300) {
        $message = is_array($data) ? ($data['error']['message'] ?? $response) : $response;
        throw new RuntimeException($provider['label'] . ' 接口返回异常：' . $message);
    }

    $content = is_array($data) ? ($data['choices'][0]['message']['content'] ?? '') : '';
    if (!is_string($content) || trim($content) === '') {
        throw new RuntimeException($provider['label'] . ' 接口没有返回有效内容。');
    }

    return [
        'content' => trim($content),
        'provider' => $providerKey,
        'providerLabel' => $provider['label'],
        'model' => $provider['model'],
        'usage' => is_array($data) ? ($data['usage'] ?? null) : null,
    ];
}
