<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/detail-header.php';

$categoryLabels = ['career' => 'Career', 'bid' => 'Bid / Tender', 'partnership' => 'Partnership', 'press' => 'Press'];
$items = fetch_all("SELECT * FROM newsroom WHERE status = 'published' ORDER BY featured DESC, published_at DESC, id DESC");

detail_header('Newsroom', 'Careers, continental opportunities, bids for partnerships, and press from ACMIRS.', 'news-page');
?>
<section class="news-intro container"><p class="editorial-kicker">Inside ACMIRS</p><div class="news-intro-grid"><h1>News &amp;<br><em>opportunities.</em></h1><p>Announcements, careers and opportunities to work with ACMIRS.</p></div></section>
<section class="news-list container" aria-label="Newsroom updates">
  <div class="news-list-grid">
    <?php foreach ($items as $item): ?>
      <article class="news-story">
        <?php if ($item['image_path']): ?><img src="<?= e($item['image_path']) ?>" alt=""><?php endif; ?>
        <div class="newsroom-card-meta">
          <time datetime="<?= e(date('Y-m-d', strtotime($item['published_at']))) ?>"><?= e(date('j M Y', strtotime($item['published_at']))) ?></time>
          <span class="newsroom-category"><?= e($categoryLabels[$item['category']] ?? ucfirst($item['category'])) ?></span>
        </div>
        <h2><a href="<?= e(url('newsroom-item.php?slug=' . urlencode($item['slug']))) ?>"><?= e($item['title']) ?></a></h2>
        <p><?= e($item['excerpt']) ?></p>
        <?php if ($item['closing_date']): ?><p class="newsroom-closing">Closes <?= e(date('j M Y', strtotime($item['closing_date']))) ?></p><?php endif; ?>
        <a href="<?= e(url('newsroom-item.php?slug=' . urlencode($item['slug']))) ?>" class="editorial-link">Read more ↗</a>
      </article>
    <?php endforeach; ?>
    <?php if (!$items): ?><div class="news-empty"><div><p class="editorial-kicker">Newsroom</p><h2>More to come.</h2></div><div><p>There are no published updates at the moment. Explore our latest perspectives on Africa’s infrastructure, or contact our team.</p><div class="empty-actions"><a class="editorial-link" href="<?= e(url('index.php#insights')) ?>">Explore insights ↗</a><a class="editorial-link" href="<?= e(url('index.php#contact')) ?>">Get in touch ↗</a></div></div></div><?php endif; ?>
  </div>
</section>
<?php detail_footer(); ?>
