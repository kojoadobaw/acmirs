<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/detail-header.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$statement = db()->prepare('SELECT * FROM sectors WHERE slug = ? AND is_active = 1 LIMIT 1');
$statement->execute([$slug]);
$sector = $statement->fetch();

if (!$sector) {
    http_response_code(404);
    detail_header('Sector not found');
    echo '<section class="detail-content"><h1>Sector not found</h1><p>The sector may have moved or is not currently listed.</p><p><a href="' . e(url('#sectors')) . '">Browse ACMIRS sectors</a></p></section>';
    detail_footer();
    exit;
}

$relatedServices = fetch_all(
    'SELECT s.label, s.slug FROM services s JOIN service_sectors ss ON ss.service_id = s.id WHERE ss.sector_id = ? AND s.is_active = 1 ORDER BY s.sort_order',
    [$sector['id']]
);

detail_header($sector['name'], $sector['description']);
?>
<section class="detail-hero"><div><p class="detail-kicker">Sector</p><h1><?= e($sector['name']) ?></h1><p class="detail-meta"><?= e($sector['description']) ?></p></div></section>
<article class="detail-content">
  <?php if ($sector['image_path']): ?><img src="<?= e($sector['image_path']) ?>" alt=""><?php endif; ?>
  <?php if ($sector['body']): ?><div><?= $sector['body'] ?></div><?php endif; ?>
  <?php if ($relatedServices): ?>
    <h2>Related services</h2>
    <ul class="detail-related-list">
      <?php foreach ($relatedServices as $service): ?><li><a href="<?= e(url('#services')) ?>"><?= e($service['label']) ?></a></li><?php endforeach; ?>
    </ul>
  <?php endif; ?>
</article>
<?php detail_footer(); ?>
