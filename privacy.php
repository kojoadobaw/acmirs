<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/detail-header.php';

$content = setting('privacy_policy');

detail_header('Privacy Policy', 'ACMIRS privacy policy.', 'legal-page');
?>
<section class="collection-hero"><div class="container collection-hero-grid">
  <div><p class="editorial-kicker">Legal</p><h1>Privacy<br><em>Policy.</em></h1></div>
</div></section>
<section class="contact-section container">
  <?php if ($content !== ''): ?>
    <div class="editorial-prose"><?= $content ?></div>
  <?php else: ?>
    <p>This page is being prepared. In the meantime, please <a href="<?= e(url('contact.php')) ?>">contact us</a> with any privacy-related questions.</p>
  <?php endif; ?>
</section>
<?php detail_footer(); ?>
