<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/project_knowledge.php';

function ensure_default_projects(PDO $pdo): void
{
    install_projects_table($pdo);
    $defaults = fallback_project_options();
    $order = 10;
    foreach ($defaults as $key => $name) {
        $stmt = $pdo->prepare(<<<SQL
INSERT INTO projects (project_key, name, enabled, sort_order)
VALUES (:project_key, :name, 1, :sort_order)
ON DUPLICATE KEY UPDATE name = VALUES(name)
SQL);
        $stmt->execute([
            'project_key' => $key,
            'name' => $name,
            'sort_order' => $order,
        ]);
        $order += 10;
    }
}

function project_options(bool $enabledOnly = true): array
{
    $pdo = db();
    if (!$pdo instanceof PDO) {
        return fallback_project_options();
    }

    try {
        ensure_default_projects($pdo);
        $where = $enabledOnly ? 'WHERE enabled = 1' : '';
        $rows = $pdo->query("SELECT project_key, name FROM projects {$where} ORDER BY sort_order ASC, id ASC")->fetchAll();
    } catch (Throwable) {
        return fallback_project_options();
    }

    if (!$rows) {
        return fallback_project_options();
    }

    $projects = [];
    foreach ($rows as $row) {
        $projects[(string)$row['project_key']] = (string)$row['name'];
    }
    return $projects;
}

function project_rows(bool $enabledOnly = false): array
{
    $pdo = require_db();
    ensure_default_projects($pdo);
    $where = $enabledOnly ? 'WHERE enabled = 1' : '';
    return $pdo->query("SELECT id, project_key, name, enabled, sort_order, updated_at FROM projects {$where} ORDER BY sort_order ASC, id ASC")->fetchAll();
}

function normalize_project_key(string $key): string
{
    $key = strtolower(trim($key));
    $key = preg_replace('/[^a-z0-9_\\-]/', '_', $key) ?? '';
    $key = trim($key, '_-');
    return $key;
}

