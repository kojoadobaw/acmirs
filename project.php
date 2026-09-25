<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/detail-header.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$statement = db()->prepare("SELECT * FROM projects WHERE slug = ? AND status = 'published' LIMIT 1");
$statement->execute([$slug]);
$project = $statement->fetch();

if (!$project) {
    http_response_code(404);
    detail_header('Project not found');
    echo '<section class="detail-content"><h1>Project not found</h1><p>The project may have moved or is not yet published.</p><p><a href="' . e(url('#experience')) . '">Browse ACMIRS experience</a></p></section>';
    detail_footer();
    exit;
}

detail_header($project['title'], $project['summary']);
?>
<section class="detail-hero"><div><p class="detail-kicker"><?= e($project['infrastructure_class']) ?></p><h1><?= e($project['title']) ?></h1><p class="detail-meta"><?= e(implode(' · ', array_filter([$project['location'], $project['sector_name'], $project['client']]))) ?></p></div></section>
<article class="detail-content">
  <?php if ($project['image_path']): ?><img src="<?= e($project['image_path']) ?>" alt=""><?php endif; ?>
  <dl class="detail-facts">
    <?php foreach (['scale' => 'Scale', 'location' => 'Location', 'sector_name' => 'Sector', 'client' => 'Client / partner'] as $key => $label): if (!$project[$key]) continue; ?><div><dt><?= e($label) ?></dt><dd><?= e($project[$key]) ?></dd></div><?php endforeach; ?>
  </dl>
  <?php if ($project['summary']): ?><p class="lead"><?= e($project['summary']) ?></p><?php endif; ?>
  <div><?= nl2br(e($project['body'])) ?></div>
</article>
<?php detail_footer(); ?>

