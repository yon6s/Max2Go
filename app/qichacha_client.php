<?php
declare(strict_types=1);

function qichacha_config(): array
{
    return [
        'app_key' => trim((string)app_config('qichacha_app_key', '')),
        'secret_key' => trim((string)app_config('qichacha_secret_key', '')),
        'base_url' => 'https://api.qichacha.com/ECIV4/GetBasicDetailsByName',
    ];
}

function qichacha_company_profile(string $keyword): array
{
    $keyword = trim($keyword);
    if ($keyword === '') {
        throw new InvalidArgumentException('请先填写承租方名称。');
    }

    $config = qichacha_config();
    if ($config['app_key'] === '' || $config['secret_key'] === '') {
        throw new RuntimeException('企查查 AppKey 或 SecretKey 尚未配置。');
    }

    $timespan = (string)time();
    $token = strtoupper(md5($config['app_key'] . $timespan . $config['secret_key']));
    $url = $config['base_url'] . '?' . http_build_query([
        'key' => $config['app_key'],
        'keyword' => $keyword,
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPGET => true,
        CURLOPT_HTTPHEADER => [
            'Token: ' . $token,
            'Timespan: ' . $timespan,
        ],
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $curlError !== '') {
        throw new RuntimeException('企查查接口连接失败：' . $curlError);
    }

    $data = json_decode($response, true);
    if ($status < 200 || $status >= 300 || !is_array($data)) {
        throw new RuntimeException('企查查接口返回异常。');
    }

    if ((string)($data['Status'] ?? '') !== '200') {
        throw new RuntimeException('企查查查询失败：' . (string)($data['Message'] ?? '未知错误'));
    }

    $result = $data['Result'] ?? null;
    if (!is_array($result)) {
        throw new RuntimeException('企查查没有返回企业工商详情。');
    }

    return [
        'tenantName' => trim((string)($result['Name'] ?? $keyword)),
        'creditCode' => trim((string)($result['CreditCode'] ?? '')),
        'registeredAddress' => trim((string)($result['Address'] ?? '')),
        'legalRepresentative' => trim((string)($result['OperName'] ?? '')),
        'tenantPhone' => '',
        'contactPerson' => trim((string)($result['OperName'] ?? '')),
        'noticeAddress' => trim((string)($result['Address'] ?? '')),
        'source' => [
            'provider' => 'qichacha',
            'status' => (string)($result['Status'] ?? ''),
            'updatedDate' => (string)($result['UpdatedDate'] ?? ''),
            'orderNumber' => (string)($data['OrderNumber'] ?? ''),
        ],
    ];
}
