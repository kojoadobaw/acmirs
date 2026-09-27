<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/detail-header.php';

$categoryLabels = ['career' => 'Career', 'bid' => 'Bid / Tender', 'partnership' => 'Partnership', 'press' => 'Press'];

$slug = trim((string) ($_GET['slug'] ?? ''));
$statement = db()->prepare("SELECT * FROM newsroom WHERE slug = ? AND status = 'published' LIMIT 1");
$statement->execute([$slug]);
$item = $statement->fetch();

if (!$item) {
    http_response_code(404);
    detail_header('Newsroom item not found');
    echo '<section class="detail-content"><h1>Item not found</h1><p>This item may have moved or is not yet published.</p><p><a href="' . e(url('newsroom.php')) . '">Back to the newsroom</a></p></section>';
    detail_footer();
    exit;
}

detail_header($item['title'], $item['excerpt']);
?>
<section class="detail-hero"><div><p class="detail-kicker"><?= e($categoryLabels[$item['category']] ?? ucfirst($item['category'])) ?></p><h1><?= e($item['title']) ?></h1><p class="detail-meta">Published <?= e(date('j F Y', strtotime($item['published_at']))) ?><?php if ($item['closing_date']): ?> · Closes <?= e(date('j F Y', strtotime($item['closing_date']))) ?><?php endif; ?><?php if ($item['location']): ?> · <?= e($item['location']) ?><?php endif; ?></p></div></section>
<article class="detail-content">
  <?php if ($item['image_path']): ?><img src="<?= e($item['image_path']) ?>" alt=""><?php endif; ?>
  <p class="lead"><?= e($item['excerpt']) ?></p>
  <?php if ($item['body']): ?><div><?= $item['body'] ?></div><?php endif; ?>
  <?php $externalScheme = strtolower((string) parse_url((string) $item['external_url'], PHP_URL_SCHEME)); ?>
  <?php if ($item['external_url'] && in_array($externalScheme, ['http', 'https'], true)): ?><p><strong><a href="<?= e($item['external_url']) ?>" target="_blank" rel="noopener noreferrer">Apply / view details →</a></strong></p><?php endif; ?>
  <p><a href="<?= e(url('newsroom.php')) ?>">← Back to the newsroom</a></p>
</article>
<?php detail_footer(); ?>
