<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/detail-header.php';

$categoryLabels = ['career' => t('newsroom.category.career'), 'bid' => t('newsroom.category.bid'), 'partnership' => t('newsroom.category.partnership'), 'press' => t('newsroom.category.press')];
$items = fetch_all("SELECT * FROM newsroom WHERE status = 'published' ORDER BY featured DESC, published_at DESC, id DESC");

detail_header(t('nav.newsroom'), 'Careers, continental opportunities, bids for partnerships, and press from ACMIRS.', 'news-page');
?>
<section class="news-intro container"><p class="editorial-kicker"><?= e(t('newsroom.kicker')) ?></p><div class="news-intro-grid"><h1><?= e(t('newsroom.title_line1')) ?><br><em><?= e(t('newsroom.title_em')) ?></em></h1><p><?= e(t('newsroom.intro')) ?></p></div></section>
<section class="news-list container" aria-label="Newsroom updates">
  <div class="news-list-grid">
    <?php foreach ($items as $item): ?>
      <article class="news-story">
        <?php if ($item['image_path']): ?><img src="<?= e($item['image_path']) ?>" alt=""><?php endif; ?>
        <div class="newsroom-card-meta">
          <time datetime="<?= e(date('Y-m-d', strtotime($item['published_at']))) ?>"><?= e(date('j M Y', strtotime($item['published_at']))) ?></time>
          <span class="newsroom-category"><?= e($categoryLabels[$item['category']] ?? ucfirst($item['category'])) ?></span>
        </div>
        <h2><a href="<?= e(url('newsroom-item.php?slug=' . urlencode($item['slug']))) ?>"><?= e(tf($item, 'title')) ?></a></h2>
        <p><?= e(tf($item, 'excerpt')) ?></p>
        <?php if ($item['closing_date']): ?><p class="newsroom-closing"><?= e(t('newsroom.closes')) ?> <?= e(date('j M Y', strtotime($item['closing_date']))) ?></p><?php endif; ?>
        <a href="<?= e(url('newsroom-item.php?slug=' . urlencode($item['slug']))) ?>" class="editorial-link"><?= e(t('newsroom.read_more')) ?> ↗</a>
      </article>
    <?php endforeach; ?>
    <?php if (!$items): ?><div class="news-empty"><div><p class="editorial-kicker"><?= e(t('newsroom.empty_kicker')) ?></p><h2><?= e(t('newsroom.empty_title')) ?></h2></div><div><p><?= e(t('newsroom.empty_body')) ?></p><div class="empty-actions"><a class="editorial-link" href="<?= e(url('index.php#insights')) ?>"><?= e(t('newsroom.explore_insights')) ?> ↗</a><a class="editorial-link" href="<?= e(url('contact.php')) ?>"><?= e(t('newsroom.get_in_touch')) ?> ↗</a></div></div></div><?php endif; ?>
  </div>
</section>
<?php detail_footer(); ?>
