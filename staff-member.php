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
<article class="adviser-profile container">
  <a class="editorial-back" href="<?= e(url('staff.php')) ?>">← Our team</a>
  <div class="adviser-layout <?= empty($member['image_path']) ? 'adviser-layout--text' : '' ?>">
    <?php if ($member['image_path']): ?>
    <aside class="adviser-visual" aria-label="Portrait and contact">
      <figure class="adviser-portrait"><img src="<?= e($member['image_path']) ?>" alt="<?= e($member['name']) ?>" fetchpriority="high"></figure>
      <div class="adviser-caption"><div><p class="editorial-kicker">ACMIRS</p><p>Infrastructure advisory</p></div><a class="adviser-contact" href="<?= e(url('contact.php')) ?>">Get in touch <span aria-hidden="true">↗</span></a></div>
    </aside>
    <?php endif; ?>
    <div class="adviser-content">
      <header class="adviser-heading">
        <p class="editorial-kicker">Our people</p>
        <h1><?= e($member['name']) ?></h1>
        <p class="adviser-role"><?= e($member['role']) ?></p>
        <?php if ($member['short_bio']): ?><p class="adviser-summary"><?= e($member['short_bio']) ?></p><?php endif; ?>
      </header>
      <?php if ($member['bio']): ?>
      <section class="adviser-biography" aria-labelledby="biography-title">
        <h2 id="biography-title"><span aria-hidden="true"></span>Biography</h2>
        <div class="editorial-prose"><?= $member['bio'] ?></div>
      </section>
      <?php endif; ?>
      <div class="adviser-next"><p class="editorial-kicker">Our work</p><h2>Expertise in action.</h2><p>Explore ACMIRS’s infrastructure advisory experience across Africa.</p><a class="editorial-link" href="<?= e(url('index.php#experience')) ?>">View our experience <span aria-hidden="true">↗</span></a></div>
    </div>
  </div>
</article>
<section class="editorial-closing container">
  <div><p class="editorial-kicker">Expertise. Perspective. Partnership.</p><h2>Meet the people behind ACMIRS.</h2></div>
  <a class="editorial-link" href="<?= e(url('staff.php')) ?>">Explore our team <span aria-hidden="true">↗</span></a>
</section>
<?php detail_footer(); ?>
