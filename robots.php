<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

header('Content-Type: text/plain; charset=utf-8');
echo "User-agent: *\n";
echo "Disallow: /admin/\n";
echo "Disallow: /install.php\n";
echo "Disallow: /migrate.php\n";
echo "Allow: /\n";
echo 'Sitemap: ' . absolute_url('sitemap.xml') . "\n";
