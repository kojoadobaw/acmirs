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

detail_header($member['name'], $member['role'], 'profile-page');
?>
<section class="profile-intro">
  <div class="container">
    <a class="editorial-back" href="<?= e(url('staff.php')) ?>">← Our team</a>
    <div class="profile-grid <?= empty($member['image_path']) ? 'profile-grid--text' : '' ?>">
      <div class="profile-copy">
        <p class="editorial-kicker">Our people</p>
        <h1><?= e($member['name']) ?></h1>
        <p class="profile-position"><?= e($member['role']) ?></p>
        <?php if ($member['short_bio']): ?><p class="profile-summary"><?= e($member['short_bio']) ?></p><?php endif; ?>
      </div>
      <?php if ($member['image_path']): ?><figure class="profile-portrait"><img src="<?= e($member['image_path']) ?>" alt="<?= e($member['name']) ?>"></figure><?php endif; ?>
    </div>
  </div>
</section>
<?php if ($member['bio']): ?>
<section class="profile-biography container">
  <div class="editorial-section-label">Biography</div>
  <div class="editorial-prose"><?= $member['bio'] ?></div>
</section>
<?php endif; ?>
<section class="editorial-closing container">
  <div><p class="editorial-kicker">Expertise. Perspective. Partnership.</p><h2>Meet the people behind ACMIRS.</h2></div>
  <a class="editorial-link" href="<?= e(url('staff.php')) ?>">Explore our team <span aria-hidden="true">↗</span></a>
</section>
<?php detail_footer(); ?>
