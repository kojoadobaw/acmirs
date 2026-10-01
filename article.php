<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/detail-header.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$statement = db()->prepare("SELECT * FROM insights WHERE slug = ? AND status = 'published' LIMIT 1");
$statement->execute([$slug]);
$article = $statement->fetch();

if (!$article) {
    http_response_code(404);
    detail_header(t('article.not_found'));
    echo '<section class="detail-content"><h1>' . e(t('article.item_not_found')) . '</h1><p>' . e(t('article.not_found_body')) . '</p><p><a href="' . e(url('#insights')) . '">' . e(t('article.browse')) . '</a></p></section>';
    detail_footer();
    exit;
}

$relatedStatement = db()->prepare("SELECT slug, title, title_fr, title_es, category, category_fr, category_es, image_path FROM insights WHERE status = 'published' AND id <> ? ORDER BY (category = ?) DESC, published_at DESC LIMIT 3");
$relatedStatement->execute([$article['id'], $article['category']]);
$relatedArticles = $relatedStatement->fetchAll();
$readingMinutes = max(1, (int) ceil(str_word_count(strip_tags($article['excerpt'] . ' ' . $article['body'])) / 220));
detail_header(tf($article, 'title'), tf($article, 'excerpt'), 'insight-page', (string) $article['image_path']);
?>
<article class="editorial-article">
  <header class="article-intro container">
    <a class="editorial-back" href="<?= e(url('index.php#insights')) ?>">← <?= e(t('article.back')) ?></a>
    <div class="article-heading">
      <p class="editorial-kicker"><?= e(tf($article, 'category')) ?></p>
      <h1><?= e(tf($article, 'title')) ?></h1>
      <p class="article-deck"><?= e(tf($article, 'excerpt')) ?></p>
      <div class="article-meta"><time datetime="<?= e(date('Y-m-d', strtotime($article['published_at']))) ?>"><?= e(date('j F Y', strtotime($article['published_at']))) ?></time><span><?= $readingMinutes ?> <?= e(t('article.min_read')) ?></span></div>
    </div>
  </header>
  <?php if ($article['image_path']): ?><figure class="article-image container"><img src="<?= e($article['image_path']) ?>" alt="" fetchpriority="high"></figure><?php endif; ?>
  <div class="article-body editorial-prose"><?= tf($article, 'body') ?></div>
</article>
<section class="article-related container" aria-labelledby="related-title">
  <div class="related-heading"><div><p class="editorial-kicker"><?= e(t('article.related_kicker')) ?></p><h2 id="related-title"><?= e(t('article.related_title')) ?></h2></div><a class="editorial-link" href="<?= e(url('index.php#insights')) ?>"><?= e(t('article.all_insights')) ?> <span aria-hidden="true">↗</span></a></div>
  <?php if ($relatedArticles): ?><div class="editorial-related-grid">
    <?php foreach ($relatedArticles as $related): ?><a class="editorial-related-card" href="<?= e(url('article.php?slug=' . urlencode($related['slug']))) ?>">
      <?php if ($related['image_path']): ?><img src="<?= e($related['image_path']) ?>" alt="" loading="lazy"><?php endif; ?>
      <p class="editorial-kicker"><?= e(tf($related, 'category')) ?></p><h3><?= e(tf($related, 'title')) ?></h3><span class="editorial-link"><?= e(t('article.read_insight')) ?> <span aria-hidden="true">↗</span></span>
    </a><?php endforeach; ?>
  </div><?php endif; ?>
</section>
<?php detail_footer(); ?>
