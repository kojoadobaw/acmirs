<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/detail-header.php';

$staff = fetch_all('SELECT * FROM staff WHERE is_active = 1 ORDER BY sort_order, id');

detail_header('Our Team', 'Meet the ACMIRS leadership and advisory team.');
?>
<section class="detail-hero"><div><p class="detail-kicker">Our Team</p><h1>The people behind ACMIRS</h1></div></section>
<section class="detail-content">
  <div class="staff-grid">
    <?php foreach ($staff as $member): ?>
      <a class="staff-card" href="<?= e(url('staff-member.php?slug=' . urlencode($member['slug']))) ?>">
        <?php if ($member['image_path']): ?><img src="<?= e($member['image_path']) ?>" alt=""><?php endif; ?>
        <h3><?= e($member['name']) ?></h3>
        <p class="staff-role"><?= e($member['role']) ?></p>
        <?php if ($member['short_bio']): ?><p class="staff-summary"><?= e($member['short_bio']) ?></p><?php endif; ?>
      </a>
    <?php endforeach; ?>
    <?php if (!$staff): ?><p>Team profiles are coming soon.</p><?php endif; ?>
  </div>
</section>
<?php detail_footer(); ?>
