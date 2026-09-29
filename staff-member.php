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
    detail_header(t('staff_member.not_found'));
    echo '<section class="detail-content"><h1>' . e(t('staff_member.not_found')) . '</h1><p>' . e(t('staff_member.not_found_body')) . '</p><p><a href="' . e(url('staff.php')) . '">' . e(t('staff_member.back_link')) . '</a></p></section>';
    detail_footer();
    exit;
}

detail_header($member['name'], tf($member, 'role'), 'profile-page', (string) $member['image_path']);
?>
<article class="adviser-profile container">
  <a class="editorial-back" href="<?= e(url('staff.php')) ?>">← <?= e(t('staff_member.back')) ?></a>
  <div class="adviser-layout <?= empty($member['image_path']) ? 'adviser-layout--text' : '' ?>">
    <?php if ($member['image_path']): ?>
    <aside class="adviser-visual" aria-label="Portrait and contact">
      <figure class="adviser-portrait"><img src="<?= e($member['image_path']) ?>" alt="<?= e($member['name']) ?>" fetchpriority="high"></figure>
      <div class="adviser-caption"><div><p class="editorial-kicker"><?= e(t('staff_member.acmirs')) ?></p><p><?= e(t('staff_member.advisory')) ?></p></div><a class="adviser-contact" href="<?= e(url('contact.php')) ?>"><?= e(t('staff_member.get_in_touch')) ?> <span aria-hidden="true">↗</span></a></div>
    </aside>
    <?php endif; ?>
    <div class="adviser-content">
      <header class="adviser-heading">
        <p class="editorial-kicker"><?= e(t('staff_member.kicker')) ?></p>
        <h1><?= e($member['name']) ?></h1>
        <p class="adviser-role"><?= e(tf($member, 'role')) ?></p>
        <?php if (tf($member, 'short_bio') !== ''): ?><p class="adviser-summary"><?= e(tf($member, 'short_bio')) ?></p><?php endif; ?>
      </header>
      <?php if (tf($member, 'bio') !== ''): ?>
      <section class="adviser-biography" aria-labelledby="biography-title">
        <h2 id="biography-title"><span aria-hidden="true"></span><?= e(t('staff_member.biography')) ?></h2>
        <div class="editorial-prose"><?= tf($member, 'bio') ?></div>
      </section>
      <?php endif; ?>
      <div class="adviser-next"><p class="editorial-kicker"><?= e(t('staff_member.next_kicker')) ?></p><h2><?= e(t('staff_member.next_title')) ?></h2><p><?= e(t('staff_member.next_body')) ?></p><a class="editorial-link" href="<?= e(url('index.php#experience')) ?>"><?= e(t('staff_member.view_experience')) ?> <span aria-hidden="true">↗</span></a></div>
    </div>
  </div>
</article>
<section class="editorial-closing container">
  <div><p class="editorial-kicker"><?= e(t('staff_member.closing_kicker')) ?></p><h2><?= e(t('staff_member.closing_title')) ?></h2></div>
  <a class="editorial-link" href="<?= e(url('staff.php')) ?>"><?= e(t('staff_member.closing_cta')) ?> <span aria-hidden="true">↗</span></a>
</section>
<?php detail_footer(); ?>
