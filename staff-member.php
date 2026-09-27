<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/detail-header.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$statement = db()->prepare('SELECT * FROM staff WHERE slug = ? AND is_active = 1 LIMIT 1');
$statement->execute([$slug]);
$member = $statement->fetch();

if (!$member) {
    http_response_code(404);
    detail_header('Team member not found');
    echo '<section class="detail-content"><h1>Team member not found</h1><p>This profile may have moved.</p><p><a href="' . e(url('staff.php')) . '">Back to our team</a></p></section>';
    detail_footer();
    exit;
}

detail_header($member['name'], $member['role']);
?>
<section class="detail-hero"><div><p class="detail-kicker"><?= e($member['role']) ?></p><h1><?= e($member['name']) ?></h1></div></section>
<article class="detail-content">
  <?php if ($member['image_path']): ?><img src="<?= e($member['image_path']) ?>" alt="<?= e($member['name']) ?>"><?php endif; ?>
  <?php if ($member['short_bio']): ?><p class="lead"><?= e($member['short_bio']) ?></p><?php endif; ?>
  <?php if ($member['bio']): ?><div><?= $member['bio'] ?></div><?php endif; ?>
  <p><a href="<?= e(url('staff.php')) ?>">← Back to our team</a></p>
</article>
<?php detail_footer(); ?>
