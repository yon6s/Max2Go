<?php
declare(strict_types=1);

function db(): ?PDO
{
    static $pdo = null;
    static $failed = false;

    if ($pdo instanceof PDO) {
        return $pdo;
    }
    if ($failed) {
        return null;
    }

    $dbName = trim((string)app_config('db_name', ''));
    $dbUser = trim((string)app_config('db_user', ''));
    if ($dbName === '' || $dbUser === '') {
        $failed = true;
        return null;
    }

    $host = (string)app_config('db_host', '127.0.0.1');
    $port = (string)app_config('db_port', '3306');
    $charset = (string)app_config('db_charset', 'utf8mb4');
    $socket = trim((string)app_config('db_socket', ''));
    if ($socket !== '') {
        $dsn = "mysql:unix_socket={$socket};dbname={$dbName};charset={$charset}";
    } else {
        $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset={$charset}";
    }

    try {
        $pdo = new PDO($dsn, $dbUser, (string)app_config('db_password', ''), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (Throwable) {
        $failed = true;
        return null;
    }
}

function require_db(): PDO
{
    $pdo = db();
    if (!$pdo instanceof PDO) {
        json_response(['error' => '数据库未连接。请先在 app/config.php 配置 MySQL 信息。'], 500);
    }
    return $pdo;
}

function install_knowledge_table(PDO $pdo): void
{
    $pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS knowledge_items (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    project_key VARCHAR(60) NOT NULL DEFAULT 'general',
    type VARCHAR(60) NOT NULL DEFAULT '项目资料',
    title VARCHAR(180) NOT NULL,
    content MEDIUMTEXT NOT NULL,
    stages VARCHAR(255) NOT NULL DEFAULT 'all',
    priority TINYINT UNSIGNED NOT NULL DEFAULT 2,
    enabled TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_project_enabled (project_key, enabled),
    KEY idx_priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL);
}

function install_projects_table(PDO $pdo): void
{
    $pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS projects (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    project_key VARCHAR(60) NOT NULL,
    name VARCHAR(180) NOT NULL,
    enabled TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 100,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_project_key (project_key),
    KEY idx_enabled_sort (enabled, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL);
}
