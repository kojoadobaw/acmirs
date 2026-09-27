<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/detail-header.php';

$categoryLabels = ['career' => 'Career', 'bid' => 'Bid / Tender', 'partnership' => 'Partnership', 'press' => 'Press'];
$items = fetch_all("SELECT * FROM newsroom WHERE status = 'published' ORDER BY featured DESC, published_at DESC, id DESC");

detail_header('Newsroom', 'Careers, continental opportunities, bids for partnerships, and press from ACMIRS.');
?>
<section class="detail-hero"><div><p class="detail-kicker">Newsroom</p><h1>News, careers &amp; opportunities</h1></div></section>
<section class="detail-content">
  <div class="newsroom-grid">
    <?php foreach ($items as $item): ?>
      <article class="newsroom-card">
        <?php if ($item['image_path']): ?><img src="<?= e($item['image_path']) ?>" alt=""><?php endif; ?>
        <div class="newsroom-card-meta">
          <time datetime="<?= e(date('Y-m-d', strtotime($item['published_at']))) ?>"><?= e(date('j M Y', strtotime($item['published_at']))) ?></time>
          <span class="newsroom-category"><?= e($categoryLabels[$item['category']] ?? ucfirst($item['category'])) ?></span>
        </div>
        <h3><?= e($item['title']) ?></h3>
        <p><?= e($item['excerpt']) ?></p>
        <?php if ($item['closing_date']): ?><p class="newsroom-closing">Closes <?= e(date('j M Y', strtotime($item['closing_date']))) ?></p><?php endif; ?>
        <a href="<?= e(url('newsroom-item.php?slug=' . urlencode($item['slug']))) ?>">Read more →</a>
      </article>
    <?php endforeach; ?>
    <?php if (!$items): ?><p>No newsroom items are available yet.</p><?php endif; ?>
  </div>
</section>
<?php detail_footer(); ?>
