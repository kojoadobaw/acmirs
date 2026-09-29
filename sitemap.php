<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');

$urls = [
    ['loc' => absolute_url(''), 'priority' => '1.0'],
    ['loc' => absolute_url('contact.php'), 'priority' => '0.8'],
    ['loc' => absolute_url('staff.php'), 'priority' => '0.6'],
    ['loc' => absolute_url('newsroom.php'), 'priority' => '0.6'],
];

try {
    foreach (fetch_all('SELECT slug FROM sectors WHERE is_active = 1') as $row) {
        $urls[] = ['loc' => absolute_url('sector.php?slug=' . urlencode($row['slug'])), 'priority' => '0.6'];
    }
    foreach (fetch_all("SELECT slug FROM projects WHERE status = 'published'") as $row) {
        $urls[] = ['loc' => absolute_url('project.php?slug=' . urlencode($row['slug'])), 'priority' => '0.6'];
    }
    foreach (fetch_all("SELECT slug FROM insights WHERE status = 'published'") as $row) {
        $urls[] = ['loc' => absolute_url('article.php?slug=' . urlencode($row['slug'])), 'priority' => '0.6'];
    }
    foreach (fetch_all('SELECT slug FROM staff WHERE is_active = 1') as $row) {
        $urls[] = ['loc' => absolute_url('staff-member.php?slug=' . urlencode($row['slug'])), 'priority' => '0.5'];
    }
    foreach (fetch_all("SELECT slug FROM newsroom WHERE status = 'published'") as $row) {
        $urls[] = ['loc' => absolute_url('newsroom-item.php?slug=' . urlencode($row['slug'])), 'priority' => '0.5'];
    }
} catch (Throwable $exception) {
    // If the database isn't reachable, still emit the static pages above.
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $entry) {
    echo '  <url><loc>' . htmlspecialchars($entry['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc><priority>' . $entry['priority'] . '</priority></url>' . "\n";
}
echo '</urlset>' . "\n";
