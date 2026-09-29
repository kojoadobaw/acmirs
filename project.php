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
    detail_header(t('project.not_found'));
    echo '<section class="detail-content"><h1>' . e(t('project.not_found')) . '</h1><p>' . e(t('project.not_found_body')) . '</p><p><a href="' . e(url('#experience')) . '">' . e(t('project.browse')) . '</a></p></section>';
    detail_footer();
    exit;
}

detail_header(tf($project, 'title'), tf($project, 'summary'), '', (string) $project['image_path']);
?>
<section class="detail-hero"><div><p class="detail-kicker"><?= e(tf($project, 'infrastructure_class')) ?></p><h1><?= e(tf($project, 'title')) ?></h1><p class="detail-meta"><?= e(implode(' · ', array_filter([tf($project, 'location'), tf($project, 'sector_name'), tf($project, 'client')]))) ?></p></div></section>
<article class="detail-content">
  <?php if ($project['image_path']): ?><img src="<?= e($project['image_path']) ?>" alt=""><?php endif; ?>
  <dl class="detail-facts">
    <?php foreach (['scale' => t('project.fact_scale'), 'location' => t('project.fact_location'), 'sector_name' => t('project.fact_sector'), 'client' => t('project.fact_client')] as $key => $label): $factValue = tf($project, $key); if ($factValue === '') continue; ?><div><dt><?= e($label) ?></dt><dd><?= e($factValue) ?></dd></div><?php endforeach; ?>
  </dl>
  <?php if (tf($project, 'summary') !== ''): ?><p class="lead"><?= e(tf($project, 'summary')) ?></p><?php endif; ?>
  <div><?= tf($project, 'body') ?></div>
</article>
<?php detail_footer(); ?>

