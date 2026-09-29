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

detail_header($sector['name'], $sector['description'], 'sector-page');
?>
<section class="sector-intro"><div class="container"><a class="editorial-back" href="<?= e(url('index.php#sectors')) ?>">← Our sectors</a><div class="sector-intro-grid"><div><p class="editorial-kicker">Sector expertise</p><h1><?= e($sector['name']) ?></h1><p class="sector-deck"><?= e($sector['description']) ?></p></div><div class="sector-emblem" aria-hidden="true"><?= icon_svg($sector['icon']) ?></div></div></div></section>
<?php if ($sector['image_path']): ?><figure class="sector-image container"><img src="<?= e($sector['image_path']) ?>" alt=""></figure><?php endif; ?>
<section class="sector-overview container"><div class="sector-overview-copy"><p class="editorial-section-label">Sector overview</p><div class="editorial-prose"><?php if ($sector['body']): ?><?= $sector['body'] ?><?php else: ?><p><?= e($sector['description']) ?></p><?php endif; ?></div></div>
  <?php if ($relatedServices): ?><aside class="sector-services" aria-labelledby="services-title"><p class="editorial-kicker">How we help</p><h2 id="services-title">Our capabilities</h2><ul>
    <?php foreach ($relatedServices as $service): ?><li><a href="<?= e(url('index.php#service-' . $service['slug'])) ?>"><?= e($service['label']) ?><span aria-hidden="true">↗</span></a></li><?php endforeach; ?>
  </ul></aside><?php endif; ?>
</section>
<section class="editorial-closing container"><div><p class="editorial-kicker">From ambition to delivery</p><h2>Discuss your next project.</h2></div><a class="editorial-link" href="<?= e(url('index.php#contact')) ?>">Start a conversation <span aria-hidden="true">↗</span></a></section>
<?php detail_footer(); ?>
