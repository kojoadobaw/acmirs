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
    detail_header(t('sector.not_found'));
    echo '<section class="detail-content"><h1>' . e(t('sector.not_found')) . '</h1><p>' . e(t('sector.not_found_body')) . '</p><p><a href="' . e(url('#sectors')) . '">' . e(t('sector.browse')) . '</a></p></section>';
    detail_footer();
    exit;
}

$relatedServices = fetch_all(
    'SELECT s.label, s.label_fr, s.label_es, s.slug FROM services s JOIN service_sectors ss ON ss.service_id = s.id WHERE ss.sector_id = ? AND s.is_active = 1 ORDER BY s.sort_order',
    [$sector['id']]
);

detail_header(tf($sector, 'name'), tf($sector, 'description'), 'sector-page', (string) $sector['image_path']);
?>
<section class="sector-intro"><div class="container"><a class="editorial-back" href="<?= e(url('index.php#sectors')) ?>">← <?= e(t('sector.back')) ?></a><div class="sector-intro-grid"><div><p class="editorial-kicker"><?= e(t('sector.kicker')) ?></p><h1><?= e(tf($sector, 'name')) ?></h1><p class="sector-deck"><?= e(tf($sector, 'description')) ?></p></div><div class="sector-emblem" aria-hidden="true"><?= icon_svg($sector['icon']) ?></div></div></div></section>
<?php if ($sector['image_path']): ?><figure class="sector-image container"><img src="<?= e($sector['image_path']) ?>" alt=""></figure><?php endif; ?>
<section class="sector-overview container"><div class="sector-overview-copy"><p class="editorial-section-label"><?= e(t('sector.overview_label')) ?></p><div class="editorial-prose"><?php if (tf($sector, 'body') !== ''): ?><?= tf($sector, 'body') ?><?php else: ?><p><?= e(tf($sector, 'description')) ?></p><?php endif; ?></div></div>
  <?php if ($relatedServices): ?><aside class="sector-services" aria-labelledby="services-title"><p class="editorial-kicker"><?= e(t('sector.help_kicker')) ?></p><h2 id="services-title"><?= e(t('sector.capabilities_title')) ?></h2><ul>
    <?php foreach ($relatedServices as $service): ?><li><a href="<?= e(url('index.php#service-' . $service['slug'])) ?>"><?= e(tf($service, 'label')) ?><span aria-hidden="true">↗</span></a></li><?php endforeach; ?>
  </ul></aside><?php endif; ?>
</section>
<section class="editorial-closing container"><div><p class="editorial-kicker"><?= e(t('sector.closing_kicker')) ?></p><h2><?= e(t('sector.closing_title')) ?></h2></div><a class="editorial-link" href="<?= e(url('contact.php')) ?>"><?= e(t('sector.closing_cta')) ?> <span aria-hidden="true">↗</span></a></section>
<?php detail_footer(); ?>
