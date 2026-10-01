<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/detail-header.php';

$staff = fetch_all('SELECT * FROM staff WHERE is_active = 1 ORDER BY sort_order, id');

detail_header(t('nav.team'), 'Meet the ACMIRS leadership and advisory team.', 'people-page');
?>
<section class="collection-hero"><div class="container collection-hero-grid">
  <div><p class="editorial-kicker"><?= e(t('staff.kicker')) ?></p><h1><?= e(t('staff.title_line1')) ?><br><em><?= e(t('staff.title_em')) ?></em></h1></div>
  <div class="collection-intro"><span class="intro-rule" aria-hidden="true"></span><p><?= e(t('staff.intro1')) ?></p><p class="collection-description"><?= e(t('staff.intro2')) ?></p><a class="editorial-link" href="#our-people"><?= e(t('staff.meet_link')) ?> <span aria-hidden="true">↓</span></a></div>
</div></section>
<section class="people-section container" id="our-people" aria-labelledby="people-title">
  <div class="section-label-row"><h2 id="people-title"><?= e(t('staff.section_title')) ?></h2><span><?= e(t('staff.section_tagline')) ?></span></div>
  <div class="people-grid <?= count($staff) === 1 ? 'people-grid--single' : '' ?>">
  <?php foreach ($staff as $member): ?>
    <a class="person-card <?= empty($member['image_path']) ? 'person-card--text' : '' ?>" href="<?= e(url('staff-member.php?slug=' . urlencode($member['slug']))) ?>">
      <?php if ($member['image_path']): ?><div class="person-photo"><img src="<?= e($member['image_path']) ?>" alt="" loading="lazy"></div><?php endif; ?>
      <div class="person-copy"><p class="editorial-kicker"><?= e(tf($member, 'role')) ?></p><h3><?= e($member['name']) ?></h3>
      <?php if (tf($member, 'short_bio') !== ''): ?><p class="person-summary"><?= e(tf($member, 'short_bio')) ?></p><?php endif; ?>
      <span class="editorial-link"><?= e(t('staff.view_profile')) ?> <span aria-hidden="true">↗</span></span></div>
    </a>
  <?php endforeach; ?>
  <?php if (!$staff): ?><div class="quiet-empty"><h3><?= e(t('staff.empty_title')) ?></h3><p><?= e(t('staff.empty_body')) ?></p><a class="editorial-link" href="<?= e(url('contact.php')) ?>"><?= e(t('staff.empty_cta')) ?> ↗</a></div><?php endif; ?>
  </div>
</section>
<section class="editorial-closing container"><div><p class="editorial-kicker"><?= e(t('staff.closing_kicker')) ?></p><h2><?= e(t('staff.closing_title')) ?></h2></div><a class="editorial-link" href="<?= e(url('contact.php')) ?>"><?= e(t('staff.closing_cta')) ?> <span aria-hidden="true">↗</span></a></section>
<?php detail_footer(); ?>
