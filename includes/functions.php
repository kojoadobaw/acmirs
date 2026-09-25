<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';

function e($value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_base_url(): string
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $marker = '/admin/';
    if (($position = strpos($script, $marker)) !== false) {
        return rtrim(substr($script, 0, $position), '/');
    }

    $directory = str_replace('\\', '/', dirname($script));
    return $directory === '/' ? '' : rtrim($directory, '/');
}

function url(string $path = ''): string
{
    return app_base_url() . '/' . ltrim($path, '/');
}

function redirect(string $path)
{
    header('Location: ' . (strpos($path, 'http') === 0 ? $path : url($path)));
    exit;
}

function slugify(string $value): string
{
    $value = trim(function_exists('mb_strtolower') ? mb_strtolower($value) : strtolower($value));
    $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    return trim($value, '-') ?: 'item-' . time();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf()
{
    $provided = (string) ($_POST['csrf_token'] ?? '');
    if ($provided === '' || !hash_equals(csrf_token(), $provided)) {
        http_response_code(419);
        exit('Your session token expired. Please go back, refresh the page, and try again.');
    }
}

function flash(string $type, string $message)
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function pull_flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return is_array($messages) ? $messages : [];
}

function admin_user()
{
    if (empty($_SESSION['admin_id'])) {
        return null;
    }
    try {
        $statement = db()->prepare('SELECT id, username, must_change_password FROM admins WHERE id = ? LIMIT 1');
        $statement->execute([(int) $_SESSION['admin_id']]);
        return $statement->fetch() ?: null;
    } catch (Throwable $exception) {
        return null;
    }
}

function require_admin(bool $allowPasswordChange = false): array
{
    $user = admin_user();
    if (!$user) {
        redirect('admin/login.php');
    }
    if (!$allowPasswordChange && (int) $user['must_change_password'] === 1) {
        redirect('admin/change-password.php');
    }
    return $user;
}

function setting(string $key, string $fallback = ''): string
{
    static $settings = null;
    if ($settings === null) {
        $settings = [];
        try {
            foreach (db()->query('SELECT setting_key, setting_value FROM site_settings') as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Throwable $exception) {
            return $fallback;
        }
    }
    return array_key_exists($key, $settings) ? (string) $settings[$key] : $fallback;
}

function fetch_all(string $sql, array $params = []): array
{
    $statement = db()->prepare($sql);
    $statement->execute($params);
    return $statement->fetchAll();
}

function icon_svg(string $key): string
{
    $icons = [
        'energy' => '<path d="M18 2L7 18h7l-2 12 11-16h-7z"/>',
        'transport' => '<path d="M2 24h28M6 24V8h20v16M6 14h20M12 8v16M20 8v16"/><circle cx="10" cy="28" r="2"/><circle cx="22" cy="28" r="2"/>',
        'water' => '<path d="M16 3s9 10 9 16a9 9 0 0 1-18 0c0-6 9-16 9-16z"/>',
        'industry' => '<path d="M2 29h28"/><path d="M4 29V14l8 5V14l8 5V8h8v21"/>',
        'marine' => '<circle cx="16" cy="6" r="3"/><path d="M16 9v20M8 15H24"/><path d="M4 18a12 12 0 0 0 24 0"/>',
        'urban' => '<path d="M2 29h28"/><rect x="5" y="12" width="9" height="17"/><rect x="18" y="4" width="9" height="25"/><path d="M8 17h3M8 22h3M21 9h3M21 15h3M21 21h3"/>',
        'digital' => '<circle cx="16" cy="22" r="2.5"/><path d="M10.5 17.5a8 8 0 0 1 11 0M6 13a14 14 0 0 1 20 0"/>',
        'agriculture' => '<path d="M16 29V13"/><path d="M16 17c0-5 4-9 9-9 0 5-4 9-9 9zM16 22c0-5-4-9-9-9 0 5 4 9 9 9z"/><path d="M6 29h20"/>',
        'oil-gas' => '<path d="M6 29V11l8-5 8 5v18"/><path d="M22 16h4v13M2 29h28"/><path d="M11 15h6M11 21h6"/>',
    ];
    $body = $icons[$key] ?? '<circle cx="16" cy="16" r="11"/><path d="M16 9v14M9 16h14"/>';
    return '<svg viewBox="0 0 32 32" aria-hidden="true">' . $body . '</svg>';
}

function video_embed_html(array $video): string
{
    $source = trim((string) ($video['video_url'] ?? ''));
    $title = e((string) ($video['title'] ?? 'Video'));
    if ($source === '') {
        return '<div class="video-placeholder"><div class="placeholder-icon">▶</div><p>' . $title . '</p></div>';
    }

    if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([A-Za-z0-9_-]{6,})~', $source, $matches)) {
        return '<iframe src="https://www.youtube-nocookie.com/embed/' . e($matches[1]) . '" title="' . $title . '" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
    }
    if (preg_match('~vimeo\.com/(\d+)~', $source, $matches)) {
        return '<iframe src="https://player.vimeo.com/video/' . e($matches[1]) . '" title="' . $title . '" loading="lazy" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
    }
    if (preg_match('~\.(mp4|webm|ogg)(?:\?.*)?$~i', $source)) {
        return '<video controls preload="metadata" src="' . e($source) . '"></video>';
    }
    return '<a class="video-external" href="' . e($source) . '" target="_blank" rel="noopener"><span>▶</span><strong>' . $title . '</strong><small>Watch video</small></a>';
}
