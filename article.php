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
    detail_header('Insight not found');
    echo '<section class="detail-content"><h1>Insight not found</h1><p>The item may have moved or is not yet published.</p><p><a href="' . e(url('#insights')) . '">Browse ACMIRS insights</a></p></section>';
    detail_footer();
    exit;
}

detail_header($article['title'], $article['excerpt']);
?>
<section class="detail-hero"><div><p class="detail-kicker"><?= e($article['category']) ?></p><h1><?= e($article['title']) ?></h1><p class="detail-meta">Published <?= e(date('j F Y', strtotime($article['published_at']))) ?></p></div></section>
<article class="detail-content">
  <?php if ($article['image_path']): ?><img src="<?= e($article['image_path']) ?>" alt=""><?php endif; ?>
  <p class="lead"><?= e($article['excerpt']) ?></p>
  <div><?= $article['body'] ?></div>
</article>
<?php detail_footer(); ?>
