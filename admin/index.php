<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/admin-layout.php';
$user = require_admin();

$collections = [
    'services' => ['Services', 'The service finder and advisory offering'],
    'sectors' => ['Sectors', 'Sector cards and service relationships'],
    'videos' => ['Videos', 'Infrastructure in Action videos'],
    'projects' => ['Experience', 'Projects and selected mandates'],
    'insights' => ['Insights', 'Articles, research and podcasts'],
    'mandates' => ['Hero Mandate', 'The ACMIRS Mandate menu in the hero'],
];

$counts = [];
foreach (array_keys($collections) as $table) {
    $counts[$table] = (int) db()->query('SELECT COUNT(*) FROM `' . $table . '`')->fetchColumn();
}

$latest = fetch_all("SELECT title, slug, category, published_at FROM insights WHERE status = 'published' ORDER BY published_at DESC LIMIT 5");

admin_header('Overview');
?>
<div class="admin-heading">
  <div><p class="eyebrow-admin">Overview</p><h1>Welcome, <?= e($user['username']) ?></h1></div>
  <a class="admin-button" href="<?= e(url()) ?>" target="_blank">View live site</a>
</div>

<div class="dashboard-grid">
  <?php foreach ($collections as $key => $collection): ?>
    <a class="dashboard-card" href="<?= e(url('admin/content.php?type=' . $key)) ?>">
      <span class="dashboard-count"><?= $counts[$key] ?></span>
      <h2><?= e($collection[0]) ?></h2>
      <p><?= e($collection[1]) ?></p>
      <span class="text-link">Manage content →</span>
    </a>
  <?php endforeach; ?>
</div>

<section class="admin-panel">
  <div class="panel-heading"><h2>Recently published insights</h2><a href="<?= e(url('admin/content.php?type=insights&action=new')) ?>">Add insight</a></div>
  <?php if (!$latest): ?><p>No published insights yet.</p><?php endif; ?>
  <?php foreach ($latest as $item): ?>
    <div class="compact-row"><div><strong><?= e($item['title']) ?></strong><small><?= e($item['category']) ?> · <?= e(date('j M Y', strtotime($item['published_at']))) ?></small></div><a href="<?= e(url('article.php?slug=' . urlencode($item['slug']))) ?>" target="_blank">Preview</a></div>
  <?php endforeach; ?>
</section>
<?php admin_footer(); ?>
