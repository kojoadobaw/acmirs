<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/detail-header.php';

$staff = fetch_all('SELECT * FROM staff WHERE is_active = 1 ORDER BY sort_order, id');

detail_header('Our Team', 'Meet the ACMIRS leadership and advisory team.', 'people-page');
?>
<section class="collection-hero"><div class="container collection-hero-grid">
  <div><p class="editorial-kicker">Our people</p><h1>Expertise with<br><em>purpose.</em></h1></div>
  <div class="collection-intro"><span class="intro-rule" aria-hidden="true"></span><p>The people behind ACMIRS.</p><p class="collection-description">Meet the advisers working to prepare, structure and deliver Africa’s infrastructure.</p><a class="editorial-link" href="#our-people">Meet our team <span aria-hidden="true">↓</span></a></div>
</div></section>
<section class="people-section container" id="our-people" aria-labelledby="people-title">
  <div class="section-label-row"><h2 id="people-title">Our team</h2><span>Global expertise. African experience.</span></div>
  <div class="people-grid <?= count($staff) === 1 ? 'people-grid--single' : '' ?>">
  <?php foreach ($staff as $member): ?>
    <a class="person-card <?= empty($member['image_path']) ? 'person-card--text' : '' ?>" href="<?= e(url('staff-member.php?slug=' . urlencode($member['slug']))) ?>">
      <?php if ($member['image_path']): ?><div class="person-photo"><img src="<?= e($member['image_path']) ?>" alt="" loading="lazy"></div><?php endif; ?>
      <div class="person-copy"><p class="editorial-kicker"><?= e($member['role']) ?></p><h3><?= e($member['name']) ?></h3>
      <?php if ($member['short_bio']): ?><p class="person-summary"><?= e($member['short_bio']) ?></p><?php endif; ?>
      <span class="editorial-link">View profile <span aria-hidden="true">↗</span></span></div>
    </a>
  <?php endforeach; ?>
  <?php if (!$staff): ?><div class="quiet-empty"><h3>Meet our team soon.</h3><p>Our profiles are being prepared. In the meantime, get in touch to discuss your project.</p><a class="editorial-link" href="<?= e(url('contact.php')) ?>">Contact ACMIRS ↗</a></div><?php endif; ?>
  </div>
</section>
<section class="editorial-closing container"><div><p class="editorial-kicker">Start a conversation</p><h2>Let’s move infrastructure forward.</h2></div><a class="editorial-link" href="<?= e(url('contact.php')) ?>">Talk to our team <span aria-hidden="true">↗</span></a></section>
<?php detail_footer(); ?>
