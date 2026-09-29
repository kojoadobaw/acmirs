<?php
declare(strict_types=1);

function detail_header(string $title, string $description = '', string $pageClass = '')
{
    ?>
    <!doctype html>
    <html lang="en">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width,initial-scale=1">
      <meta name="description" content="<?= e($description) ?>">
      <title><?= e($title) ?> | ACMIRS</title>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="<?= e(asset_url('assets/css/site.css')) ?>">
      <link rel="stylesheet" href="<?= e(asset_url('assets/css/cms-overrides.css')) ?>">
      <link rel="stylesheet" href="<?= e(asset_url('assets/css/detail-pages.css')) ?>">
      <link rel="icon" type="image/svg+xml" href="<?= e(url('assets/brand/Website/Favicon.svg')) ?>">
    </head>
    <body class="detail-page <?= e($pageClass) ?>">
      <a href="#main-content" class="skip-link">Skip to content</a>
      <header class="site-header site-header--solid">
        <a class="brand" href="<?= e(url()) ?>" aria-label="ACMIRS — home"><img src="<?= e(url('assets/brand/Website/Logo-Footer.svg')) ?>" alt="ACMIRS"></a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" data-nav-toggle><span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span><span class="sr-only">Menu</span></button>
        <nav class="primary-nav" id="primary-nav" data-primary-nav>
          <a href="<?= e(url('index.php#services')) ?>">Our Services</a><a href="<?= e(url('index.php#sectors')) ?>">Sectors</a><a href="<?= e(url('index.php#experience')) ?>">Experience</a><a href="<?= e(url('staff.php')) ?>">Our Team</a><a href="<?= e(url('index.php#insights')) ?>">Insights</a><a href="<?= e(url('newsroom.php')) ?>">Newsroom</a><a href="<?= e(url('contact.php')) ?>">Contact</a><a class="nav-cta" href="<?= e(url('contact.php')) ?>">Let's Talk</a>
        </nav>
      </header>
      <main class="detail-main" id="main-content">
    <?php
}

function detail_footer()
{
    $footerServices = fetch_all('SELECT label FROM services WHERE is_active = 1 ORDER BY sort_order LIMIT 6');
    try {
        $footerOffices = fetch_all('SELECT name FROM offices WHERE is_active = 1 ORDER BY sort_order, id');
    } catch (Throwable $exception) {
        $footerOffices = [];
    }
    $linkedinUrl = social_link(setting('social_linkedin'));
    $twitterUrl = social_link(setting('social_twitter'));
    ?>
      </main>
      <footer class="site-footer" id="contact"><div class="container"><div class="footer-grid">
        <div class="footer-section"><div class="footer-logo"><svg class="logo" viewBox="0 0 200 80" xmlns="http://www.w3.org/2000/svg"><image href="<?= e(url('assets/brand/Website/Logo-Footer.svg')) ?>" width="200" height="80"></image></svg></div><p>Global expertise to drive Africa's infrastructure solutions.</p></div>
        <div class="footer-section"><h4>Services</h4><ul><?php foreach ($footerServices as $service): ?><li><a href="<?= e(url('index.php#services')) ?>"><?= e($service['label']) ?></a></li><?php endforeach; ?></ul></div>
        <div class="footer-section"><h4>Company</h4><ul><li><a href="<?= e(url('staff.php')) ?>">Our Team</a></li><li><a href="<?= e(url('newsroom.php')) ?>">Newsroom</a></li></ul></div>
        <?php if ($footerOffices): ?><div class="footer-section"><h4>Offices</h4><ul><?php foreach ($footerOffices as $office): ?><li><?= e($office['name']) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <div class="footer-section"><h4>Connect</h4><ul><li><a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a></li><li><a href="tel:<?= e(preg_replace('/[^+0-9]/', '', setting('contact_phone'))) ?>"><?= e(setting('contact_phone')) ?></a></li><?php if ($linkedinUrl): ?><li><a href="<?= e($linkedinUrl) ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a></li><?php endif; ?><?php if ($twitterUrl): ?><li><a href="<?= e($twitterUrl) ?>" target="_blank" rel="noopener noreferrer">Twitter</a></li><?php endif; ?></ul></div>
      </div><div class="footer-bottom"><p>&copy; <?= date('Y') ?> ACMIRS. All rights reserved.</p><ul class="footer-legal"><li><a href="<?= e(url()) ?>">Privacy</a></li><li><a href="<?= e(url()) ?>">Terms</a></li></ul></div></div></footer>
      <script src="<?= e(asset_url('assets/js/app.js')) ?>"></script>
    </body>
    </html>
    <?php
}
