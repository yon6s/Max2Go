<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/../../app/knowledge_store.php';
require_once __DIR__ . '/../../app/project_store.php';

require_login();

$payload = read_json_body();
if (!verify_csrf($payload['csrf'] ?? null)) {
    json_response(['error' => '页面会话已过期，请刷新后重试。'], 419);
}

$action = (string)($payload['action'] ?? 'list');
$pdo = require_db();

try {
    install_knowledge_table($pdo);

    if ($action === 'meta') {
        json_response([
            'projects' => project_options(true),
            'types' => knowledge_types(),
            'stages' => stage_labels(),
        ]);
    }

    if ($action === 'project_list') {
        json_response(['items' => project_rows(false)]);
    }

    if ($action === 'project_save') {
        install_projects_table($pdo);
        $id = (int)($payload['id'] ?? 0);
        $projectKey = normalize_project_key((string)($payload['project_key'] ?? ''));
        $name = trim((string)($payload['name'] ?? ''));
        $enabled = !empty($payload['enabled']) ? 1 : 0;
        $sortOrder = (int)($payload['sort_order'] ?? 100);

        if ($projectKey === '' || $name === '') {
            json_response(['error' => '项目编码和项目名称不能为空。'], 422);
        }
        if ($projectKey === 'general' && $enabled === 0) {
            json_response(['error' => '通用项目不能停用。'], 422);
        }

        if ($id > 0) {
            $stmt = $pdo->prepare(<<<SQL
UPDATE projects
SET project_key = :project_key, name = :name, enabled = :enabled, sort_order = :sort_order
WHERE id = :id
SQL);
            $stmt->execute([
                'id' => $id,
                'project_key' => $projectKey,
                'name' => $name,
                'enabled' => $enabled,
                'sort_order' => $sortOrder,
            ]);
        } else {
            $stmt = $pdo->prepare(<<<SQL
INSERT INTO projects (project_key, name, enabled, sort_order)
VALUES (:project_key, :name, :enabled, :sort_order)
ON DUPLICATE KEY UPDATE name = VALUES(name), enabled = VALUES(enabled), sort_order = VALUES(sort_order)
SQL);
            $stmt->execute([
                'project_key' => $projectKey,
                'name' => $name,
                'enabled' => $enabled,
                'sort_order' => $sortOrder,
            ]);
        }

        json_response(['ok' => true, 'projects' => project_options(true)]);
    }

    if ($action === 'project_delete') {
        $id = (int)($payload['id'] ?? 0);
        if ($id <= 0) {
            json_response(['error' => '缺少项目ID。'], 422);
        }
        $stmt = $pdo->prepare("SELECT project_key FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            json_response(['ok' => true, 'projects' => project_options(true)]);
        }
        if (in_array($row['project_key'], ['general', 'meilanhu'], true)) {
            json_response(['error' => '默认项目不能删除，可以停用美兰湖以外的项目。'], 422);
        }
        $stmt = $pdo->prepare('DELETE FROM projects WHERE id = ?');
        $stmt->execute([$id]);
        json_response(['ok' => true, 'projects' => project_options(true)]);
    }

    if ($action === 'list') {
        $projectKey = (string)($payload['project_key'] ?? 'meilanhu');
        $stmt = $pdo->prepare(<<<SQL
SELECT id, project_key, type, title, content, stages, priority, enabled, updated_at
FROM knowledge_items
WHERE project_key = :project_key
ORDER BY enabled DESC, priority ASC, updated_at DESC
LIMIT 100
SQL);
        $stmt->execute(['project_key' => $projectKey]);
        json_response(['items' => $stmt->fetchAll()]);
    }

    if ($action === 'save') {
        $id = (int)($payload['id'] ?? 0);
        $projectKey = trim((string)($payload['project_key'] ?? 'meilanhu')) ?: 'meilanhu';
        $type = trim((string)($payload['type'] ?? '项目资料')) ?: '项目资料';
        $title = trim((string)($payload['title'] ?? ''));
        $content = trim((string)($payload['content'] ?? ''));
        $stages = normalize_stages($payload['stages'] ?? ['all']);
        $priority = max(1, min(5, (int)($payload['priority'] ?? 2)));
        $enabled = !empty($payload['enabled']) ? 1 : 0;

        if ($title === '' || $content === '') {
            json_response(['error' => '标题和内容不能为空。'], 422);
        }

        if ($id > 0) {
            $stmt = $pdo->prepare(<<<SQL
UPDATE knowledge_items
SET project_key = :project_key, type = :type, title = :title, content = :content,
    stages = :stages, priority = :priority, enabled = :enabled
WHERE id = :id
SQL);
            $stmt->execute([
                'id' => $id,
                'project_key' => $projectKey,
                'type' => $type,
                'title' => $title,
                'content' => $content,
                'stages' => $stages,
                'priority' => $priority,
                'enabled' => $enabled,
            ]);
        } else {
            $stmt = $pdo->prepare(<<<SQL
INSERT INTO knowledge_items (project_key, type, title, content, stages, priority, enabled)
VALUES (:project_key, :type, :title, :content, :stages, :priority, :enabled)
SQL);
            $stmt->execute([
                'project_key' => $projectKey,
                'type' => $type,
                'title' => $title,
                'content' => $content,
                'stages' => $stages,
                'priority' => $priority,
                'enabled' => $enabled,
            ]);
        }

        json_response(['ok' => true]);
    }

    if ($action === 'delete') {
        $id = (int)($payload['id'] ?? 0);
        if ($id <= 0) {
            json_response(['error' => '缺少资料ID。'], 422);
        }
        $stmt = $pdo->prepare('DELETE FROM knowledge_items WHERE id = ?');
        $stmt->execute([$id]);
        json_response(['ok' => true]);
    }

    if ($action === 'seed_meilanhu') {
        $created = seed_meilanhu_knowledge($pdo);
        json_response(['ok' => true, 'created' => $created]);
    }

    json_response(['error' => '未知操作。'], 400);
} catch (Throwable $e) {
    json_response(['error' => '知识库操作失败：' . $e->getMessage()], 500);
}
