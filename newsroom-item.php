<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/detail-header.php';

$categoryLabels = ['career' => t('newsroom.category.career'), 'bid' => t('newsroom.category.bid'), 'partnership' => t('newsroom.category.partnership'), 'press' => t('newsroom.category.press')];

$slug = trim((string) ($_GET['slug'] ?? ''));
$statement = db()->prepare("SELECT * FROM newsroom WHERE slug = ? AND status = 'published' LIMIT 1");
$statement->execute([$slug]);
$item = $statement->fetch();

if (!$item) {
    http_response_code(404);
    detail_header(t('newsroom_item.not_found'));
    echo '<section class="detail-content"><h1>' . e(t('newsroom_item.item_not_found')) . '</h1><p>' . e(t('newsroom_item.not_found_body')) . '</p><p><a href="' . e(url('newsroom.php')) . '">' . e(t('newsroom_item.back')) . '</a></p></section>';
    detail_footer();
    exit;
}

detail_header(tf($item, 'title'), tf($item, 'excerpt'), '', (string) $item['image_path']);
?>
<section class="detail-hero"><div><p class="detail-kicker"><?= e($categoryLabels[$item['category']] ?? ucfirst($item['category'])) ?></p><h1><?= e(tf($item, 'title')) ?></h1><p class="detail-meta"><?= e(t('newsroom_item.published')) ?> <?= e(date('j F Y', strtotime($item['published_at']))) ?><?php if ($item['closing_date']): ?> · <?= e(t('newsroom_item.closes')) ?> <?= e(date('j F Y', strtotime($item['closing_date']))) ?><?php endif; ?><?php if ($item['location']): ?> · <?= e(tf($item, 'location')) ?><?php endif; ?></p></div></section>
<article class="detail-content">
  <?php if ($item['image_path']): ?><img src="<?= e($item['image_path']) ?>" alt=""><?php endif; ?>
  <p class="lead"><?= e(tf($item, 'excerpt')) ?></p>
  <?php if (tf($item, 'body') !== ''): ?><div><?= tf($item, 'body') ?></div><?php endif; ?>
  <?php $externalScheme = strtolower((string) parse_url((string) $item['external_url'], PHP_URL_SCHEME)); ?>
  <?php if ($item['external_url'] && in_array($externalScheme, ['http', 'https'], true)): ?><p><strong><a href="<?= e($item['external_url']) ?>" target="_blank" rel="noopener noreferrer"><?= e(t('newsroom_item.apply')) ?> →</a></strong></p><?php endif; ?>
  <p><a href="<?= e(url('newsroom.php')) ?>">← <?= e(t('newsroom_item.back')) ?></a></p>
</article>
<?php detail_footer(); ?>
