<?php
declare(strict_types=1);

session_start();

date_default_timezone_set('America/Sao_Paulo');

const APP_NAME = 'Workplace Formula';
const DB_PATH = __DIR__ . '/storage/app.sqlite';

function ensureStorage(): void
{
    $storageDir = __DIR__ . '/storage';
    if (!is_dir($storageDir)) {
        mkdir($storageDir, 0777, true);
    }
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    ensureStorage();
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON;');
    migrate($pdo);

    return $pdo;
}

function migrate(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            created_at TEXT NOT NULL
        )'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS tasks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT DEFAULT "",
            status TEXT NOT NULL,
            priority TEXT NOT NULL,
            due_date TEXT DEFAULT NULL,
            created_by INTEGER NOT NULL,
            assigned_to INTEGER DEFAULT NULL,
            created_at TEXT NOT NULL,
            updated_at TEXT NOT NULL,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
        )'
    );
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function nowIso(): string
{
    return (new DateTimeImmutable())->format('Y-m-d H:i:s');
}

function redirectTo(string $path = 'index.php'): void
{
    header('Location: ' . $path);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function getFlashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
        http_response_code(419);
        exit('Token CSRF inválido.');
    }
}

function currentUser(): ?array
{
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        return null;
    }

    $stmt = db()->prepare('SELECT id, name, email, created_at FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        unset($_SESSION['user_id']);
        return null;
    }

    return $user;
}

function requireAuth(): array
{
    $user = currentUser();
    if (!$user) {
        flash('error', 'Faça login para continuar.');
        redirectTo('index.php');
    }

    return $user;
}

function usersList(): array
{
    return db()->query('SELECT id, name, email FROM users ORDER BY name ASC')->fetchAll();
}

function taskStatuses(): array
{
    return [
        'todo' => 'Backlog',
        'in_progress' => 'Em andamento',
        'review' => 'Revisão',
        'done' => 'Concluído',
    ];
}

function taskPriorities(): array
{
    return [
        'low' => 'Baixa',
        'medium' => 'Média',
        'high' => 'Alta',
        'urgent' => 'Urgente',
    ];
}

function normalizeTaskStatus(string $value): string
{
    return array_key_exists($value, taskStatuses()) ? $value : 'todo';
}

function normalizeTaskPriority(string $value): string
{
    return array_key_exists($value, taskPriorities()) ? $value : 'medium';
}

function dueDateForStorage(?string $value): ?string
{
    $value = trim((string) $value);
    if ($value === '') {
        return null;
    }

    $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);
    return $date ? $date->format('Y-m-d') : null;
}

function allTasks(): array
{
    $sql = 'SELECT
                t.*,
                creator.name AS creator_name,
                creator.email AS creator_email,
                assignee.name AS assignee_name,
                assignee.email AS assignee_email
            FROM tasks t
            INNER JOIN users creator ON creator.id = t.created_by
            LEFT JOIN users assignee ON assignee.id = t.assigned_to
            ORDER BY
                CASE t.status
                    WHEN "in_progress" THEN 1
                    WHEN "review" THEN 2
                    WHEN "todo" THEN 3
                    WHEN "done" THEN 4
                    ELSE 5
                END,
                t.updated_at DESC';

    return db()->query($sql)->fetchAll();
}

function tasksByStatus(array $tasks): array
{
    $grouped = [];
    foreach (array_keys(taskStatuses()) as $status) {
        $grouped[$status] = [];
    }

    foreach ($tasks as $task) {
        $status = normalizeTaskStatus((string) $task['status']);
        $grouped[$status][] = $task;
    }

    return $grouped;
}

function dashboardStats(array $tasks): array
{
    $stats = [
        'total' => count($tasks),
        'done' => 0,
        'due_today' => 0,
        'urgent' => 0,
    ];

    $today = (new DateTimeImmutable('today'))->format('Y-m-d');

    foreach ($tasks as $task) {
        if ($task['status'] === 'done') {
            $stats['done']++;
        }
        if (($task['due_date'] ?? null) === $today) {
            $stats['due_today']++;
        }
        if ($task['priority'] === 'urgent') {
            $stats['urgent']++;
        }
    }

    return $stats;
}

function countMyAssignedTasks(array $tasks, int $userId): int
{
    $count = 0;
    foreach ($tasks as $task) {
        if ((int) ($task['assigned_to'] ?? 0) === $userId && $task['status'] !== 'done') {
            $count++;
        }
    }
    return $count;
}

