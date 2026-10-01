<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/detail-header.php';

$content = setting_t('terms_of_service');

detail_header(t('terms.meta_title'), t('terms.meta_description'), 'legal-page');
?>
<section class="collection-hero"><div class="container collection-hero-grid">
  <div><p class="editorial-kicker"><?= e(t('privacy.kicker')) ?></p><h1><?= e(t('terms.title_line1')) ?><br><em><?= e(t('terms.title_em')) ?></em></h1></div>
</div></section>
<section class="contact-section container">
  <?php if ($content !== ''): ?>
    <div class="editorial-prose"><?= $content ?></div>
  <?php else: ?>
    <p><?= e(t('terms.placeholder_before')) ?> <a href="<?= e(url('contact.php')) ?>"><?= e(t('terms.placeholder_link')) ?></a> <?= e(t('terms.placeholder_after')) ?></p>
  <?php endif; ?>
</section>
<?php detail_footer(); ?>
