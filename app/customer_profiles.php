<?php
declare(strict_types=1);

const CUSTOMER_PROFILE_FILE = APP_ROOT . '/storage/customer_profiles.json';

function customer_profile_fields(): array
{
    return [
        'tenantName',
        'creditCode',
        'registeredAddress',
        'legalRepresentative',
        'tenantPhone',
        'contactPerson',
        'noticeAddress',
    ];
}

function customer_profile_normalize_name(string $name): string
{
    $name = trim($name);
    return function_exists('mb_strtolower') ? mb_strtolower($name, 'UTF-8') : strtolower($name);
}

function customer_profile_load_all(): array
{
    if (!is_file(CUSTOMER_PROFILE_FILE)) {
        return [];
    }
    $data = json_decode((string)file_get_contents(CUSTOMER_PROFILE_FILE), true);
    return is_array($data) ? $data : [];
}

function customer_profile_save_all(array $profiles): void
{
    $dir = dirname(CUSTOMER_PROFILE_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    file_put_contents(
        CUSTOMER_PROFILE_FILE,
        json_encode(array_values($profiles), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
        LOCK_EX
    );
}

function customer_profile_clean(array $input): array
{
    $profile = [];
    foreach (customer_profile_fields() as $field) {
        $profile[$field] = trim((string)($input[$field] ?? ''));
    }
    $profile['tenantName'] = trim($profile['tenantName']);
    return $profile;
}

function customer_profile_find(string $tenantName): ?array
{
    $needle = customer_profile_normalize_name($tenantName);
    if ($needle === '') {
        return null;
    }
    foreach (customer_profile_load_all() as $profile) {
        $name = customer_profile_normalize_name((string)($profile['tenantName'] ?? ''));
        if ($name === $needle) {
            return $profile;
        }
    }
    return null;
}

function customer_profile_upsert(array $input): array
{
    $profile = customer_profile_clean($input);
    if ($profile['tenantName'] === '') {
        throw new InvalidArgumentException('请先填写承租方名称。');
    }

    $profiles = customer_profile_load_all();
    $needle = customer_profile_normalize_name($profile['tenantName']);
    $saved = false;
    foreach ($profiles as $index => $existing) {
        $name = customer_profile_normalize_name((string)($existing['tenantName'] ?? ''));
        if ($name === $needle) {
            $profiles[$index] = array_merge($existing, $profile, ['updatedAt' => date(DATE_ATOM)]);
            $saved = true;
            break;
        }
    }

    if (!$saved) {
        $profile['createdAt'] = date(DATE_ATOM);
        $profile['updatedAt'] = $profile['createdAt'];
        $profiles[] = $profile;
    }

    usort($profiles, static fn(array $a, array $b): int => strcmp((string)($b['updatedAt'] ?? ''), (string)($a['updatedAt'] ?? '')));
    $profiles = array_slice($profiles, 0, 200);
    customer_profile_save_all($profiles);

    return customer_profile_find($profile['tenantName']) ?? $profile;
}
