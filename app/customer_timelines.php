<?php
declare(strict_types=1);

const CUSTOMER_TIMELINE_FILE = APP_ROOT . '/storage/customer_timelines.json';

function customer_timeline_load_all(): array
{
    if (!is_file(CUSTOMER_TIMELINE_FILE)) {
        return [];
    }
    $data = json_decode((string)file_get_contents(CUSTOMER_TIMELINE_FILE), true);
    return is_array($data) ? $data : [];
}

function customer_timeline_save_all(array $items): void
{
    $dir = dirname(CUSTOMER_TIMELINE_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    file_put_contents(
        CUSTOMER_TIMELINE_FILE,
        json_encode(array_values($items), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
        LOCK_EX
    );
}

function customer_timeline_text(array $inputs, string $key): string
{
    return trim((string)($inputs[$key] ?? ''));
}

function customer_timeline_array(array $inputs, string $key): array
{
    $value = $inputs[$key] ?? [];
    if (!is_array($value)) {
        return [];
    }
    return array_values(array_filter(array_map(static fn($item): string => trim((string)$item), $value)));
}

function customer_timeline_excerpt(string $text, int $maxLength = 220): string
{
    $text = preg_replace('/\s+/u', ' ', trim($text)) ?? '';
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        return mb_strlen($text, 'UTF-8') > $maxLength
            ? mb_substr($text, 0, $maxLength, 'UTF-8') . '...'
            : $text;
    }
    return strlen($text) > $maxLength ? substr($text, 0, $maxLength) . '...' : $text;
}

function customer_timeline_clean_record(array $payload): array
{
    $inputs = $payload['inputs'] ?? [];
    if (!is_array($inputs)) {
        throw new InvalidArgumentException('一线记录参数格式不正确。');
    }

    $customerName = customer_timeline_text($inputs, 'customerName');
    if ($customerName === '') {
        throw new InvalidArgumentException('请先填写客户/公司名称，再保存到时间线。');
    }

    $project = $payload['project'] ?? [];
    if (!is_array($project)) {
        $project = [];
    }

    $transcript = customer_timeline_text($inputs, 'transcript');
    $salesSummary = customer_timeline_text($inputs, 'salesSummary');
    $outcomeReason = customer_timeline_text($inputs, 'outcomeReason');
    $aiContent = trim((string)($payload['aiContent'] ?? ''));
    if ($transcript === '' && $salesSummary === '' && $outcomeReason === '' && $aiContent === '') {
        throw new InvalidArgumentException('请先填写或生成一线记录内容。');
    }

    return [
        'id' => bin2hex(random_bytes(8)),
        'createdAt' => date(DATE_ATOM),
        'projectKey' => trim((string)($project['key'] ?? 'general')),
        'projectName' => trim((string)($project['name'] ?? 'MAX科技园（通用）')),
        'customerName' => $customerName,
        'recordType' => customer_timeline_text($inputs, 'recordType'),
        'recordCompleteness' => customer_timeline_text($inputs, 'recordCompleteness'),
        'dealStage' => customer_timeline_text($inputs, 'dealStage'),
        'visitorRole' => customer_timeline_text($inputs, 'visitorRole'),
        'recapFocus' => customer_timeline_text($inputs, 'recapFocus'),
        'outcome' => customer_timeline_text($inputs, 'outcome'),
        'observations' => customer_timeline_array($inputs, 'observations'),
        'salesSummary' => $salesSummary,
        'outcomeReason' => $outcomeReason,
        'transcript' => $transcript,
        'transcriptExcerpt' => customer_timeline_excerpt($transcript),
        'aiContent' => $aiContent,
        'aiExcerpt' => customer_timeline_excerpt($aiContent),
    ];
}

function customer_timeline_add(array $payload): array
{
    $record = customer_timeline_clean_record($payload);
    $items = customer_timeline_load_all();
    $items[] = $record;
    usort($items, static fn(array $a, array $b): int => strcmp((string)($b['createdAt'] ?? ''), (string)($a['createdAt'] ?? '')));
    $items = array_slice($items, 0, 1000);
    customer_timeline_save_all($items);
    return $record;
}

function customer_timeline_list(string $projectKey, string $customerName = '', int $limit = 20): array
{
    $projectKey = trim($projectKey);
    $customerName = trim($customerName);
    $items = array_filter(customer_timeline_load_all(), static function (array $item) use ($projectKey, $customerName): bool {
        if ($projectKey !== '' && (string)($item['projectKey'] ?? '') !== $projectKey) {
            return false;
        }
        if ($customerName !== '' && (string)($item['customerName'] ?? '') !== $customerName) {
            return false;
        }
        return true;
    });
    return array_slice(array_values($items), 0, max(1, min(100, $limit)));
}
