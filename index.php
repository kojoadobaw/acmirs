<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

try {
    $services = fetch_all('SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order, id');
} catch (Throwable $exception) {
    redirect('install.php');
}

$sectorRows = fetch_all('SELECT ss.service_id, s.name, s.name_fr, s.name_es, s.slug FROM service_sectors ss JOIN sectors s ON s.id = ss.sector_id WHERE s.is_active = 1 ORDER BY s.sort_order, s.name');
$serviceSectors = [];
foreach ($sectorRows as $row) {
    $serviceSectors[(int) $row['service_id']][] = ['name' => tf($row, 'name'), 'url' => url('sector.php?slug=' . urlencode($row['slug']))];
}

$serviceData = [];
foreach ($services as $service) {
    $itemsJson = json_decode(tf($service, 'items_json'), true);
    $serviceData[] = [
        'key' => $service['slug'],
        'label' => tf($service, 'label'),
        'title' => tf($service, 'title'),
        'blurb' => tf($service, 'blurb'),
        'items' => $itemsJson ?: (json_decode($service['items_json'], true) ?: []),
        'cards' => [
            ['fill' => 'transparent', 'title' => t('home.cards.challenge'), 'body' => tf($service, 'challenge')],
            ['fill' => 'linear-gradient(90deg, #b8763a 50%, transparent 50%)', 'title' => t('home.cards.response'), 'body' => tf($service, 'response')],
            ['fill' => '#b8763a', 'title' => t('home.cards.outcome'), 'body' => tf($service, 'outcome')],
        ],
        'sectors' => $serviceSectors[(int) $service['id']] ?? [],
    ];
}

$mandates = fetch_all('SELECT m.*, s.slug FROM mandates m JOIN services s ON s.id = m.service_id WHERE m.is_active = 1 AND s.is_active = 1 ORDER BY m.sort_order, m.id');
$sectors = fetch_all('SELECT * FROM sectors WHERE is_active = 1 ORDER BY sort_order, id');
$videos = fetch_all('SELECT * FROM videos WHERE is_active = 1 ORDER BY sort_order, id');
$projects = fetch_all("SELECT * FROM projects WHERE status = 'published' ORDER BY sort_order, id");
$insights = fetch_all("SELECT * FROM insights WHERE status = 'published' ORDER BY featured DESC, published_at DESC, id DESC LIMIT 6");
try {
    $offices = fetch_all('SELECT name FROM offices WHERE is_active = 1 ORDER BY sort_order, id');
} catch (Throwable $exception) {
    $offices = [];
}
try {
    $testimonials = fetch_all('SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order, id');
} catch (Throwable $exception) {
    $testimonials = [];
}
$linkedinUrl = social_link(setting('social_linkedin'));
$twitterUrl = social_link(setting('social_twitter'));

$projectsByClass = [];
foreach ($projects as $project) {
    $projectsByClass[tf($project, 'infrastructure_class')][] = $project;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'subscribe') {
    verify_csrf();
    $email = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
    if ($email) {
        $statement = db()->prepare('INSERT INTO newsletter_subscribers (email, is_active) VALUES (?, 1) ON DUPLICATE KEY UPDATE is_active = 1');
        $statement->execute([$email]);
        flash('success', t('home.insights.subscribe_success'));
    } else {
        flash('error', t('home.insights.subscribe_error'));
    }
    redirect('#insights');
}

$frontMessages = pull_flashes();
$heroDescription = 'ACMIRS: Global Expertise to Drive Africa Infrastructure Solutions';
?>
<!DOCTYPE html>
<html lang="<?= e(current_locale()) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e($heroDescription) ?>">
  <title>ACMIRS | Infrastructure Advisory</title>
  <?php render_social_meta('ACMIRS | Infrastructure Advisory', $heroDescription, current_url()); ?>
  <?php render_hreflang(); ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(asset_url('assets/css/site.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset_url('assets/css/hero-reveal.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset_url('assets/css/cms-overrides.css')) ?>">
  <link rel="icon" type="image/svg+xml" href="assets/brand/Website/Favicon.svg">
  <link rel="apple-touch-icon" href="assets/brand/Website/Apple-Touch-Icon.svg">
  <?php render_analytics(); ?>
</head>
<body>
  <a href="#top" class="skip-link"><?= e(t('skip.content')) ?></a>
  <svg aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden"><defs><clipPath id="aRevealMask" clipPathUnits="objectBoundingBox"><path d="M0.5 0.0625 L0.96875 0.90625 L0.75 0.90625 L0.5 0.453125 L0.25 0.90625 L0.03125 0.90625 Z"></path><rect x="0.3125" y="0.703125" width="0.375" height="0.109375"></rect></clipPath></defs></svg>

  <header class="site-header">
    <a class="brand" href="#top" aria-label="ACMIRS — home"><img src="assets/brand/Website/Logo-Footer.svg" alt="ACMIRS"></a>
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" data-nav-toggle><span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span><span class="sr-only"><?= e(t('nav.menu')) ?></span></button>
    <nav class="primary-nav" id="primary-nav" data-primary-nav>
      <a href="#services"><?= e(t('nav.services')) ?></a><a href="#sectors"><?= e(t('nav.sectors')) ?></a><a href="#experience"><?= e(t('nav.experience')) ?></a><a href="<?= e(url('staff.php')) ?>"><?= e(t('nav.team')) ?></a><a href="#insights"><?= e(t('nav.insights')) ?></a><a href="<?= e(url('newsroom.php')) ?>"><?= e(t('nav.newsroom')) ?></a><a href="<?= e(url('contact.php')) ?>"><?= e(t('nav.contact')) ?></a><a class="nav-cta" href="<?= e(url('contact.php')) ?>"><?= e(t('nav.cta')) ?></a><?php render_language_switcher(); ?>
    </nav>
  </header>

  <main>
    <section class="hero" id="top">
      <div class="hero-media" data-hero-media aria-hidden="true"><video autoplay muted playsinline preload="metadata" poster="assets/img/hero-golden-hour.jpg" data-hero-video data-src-hd="assets/img/hero.mp4" data-hero-playlist="assets/img/hero_energy.mp4,assets/img/hero_telecoms.mp4,assets/img/hero-golden-hour.mp4"><source src="assets/img/hero-1280.mp4" type="video/mp4"><img src="assets/img/hero-golden-hour.jpg" alt=""></video></div>
      <div class="hero-mask" data-hero-mask aria-hidden="true"><video data-hero-mask-video muted loop playsinline preload="none"><source src="assets/img/hero-1280.mp4" type="video/mp4"></video></div>
      <div class="hero-wash hero-wash--side" data-hero-wash aria-hidden="true"></div><div class="hero-wash hero-wash--vertical" data-hero-wash aria-hidden="true"></div>
      <div class="hero-inner">
        <div class="hero-copy">
          <p class="eyebrow eyebrow--light" data-hero-eyebrow><?= e(t('home.hero.eyebrow')) ?></p>
          <h1 data-hero-headline><?= wrapped_words(t('home.hero.headline')) ?></h1>
          <p class="hero-lead" data-hero-lead><?= e(t('home.hero.lead')) ?></p>
          <a class="underline-link underline-link--copper" href="#services" data-hero-lead><?= e(t('home.hero.learn_more')) ?> <span aria-hidden="true">⟶</span></a>
          <div class="hero-actions" data-hero-actions><a class="btn btn--copper" href="#services"><?= e(t('home.hero.explore_services')) ?></a><a class="btn btn--ghost" href="#experience"><?= e(t('home.hero.view_experience')) ?> <span aria-hidden="true">⟶</span></a></div>
        </div>
        <aside class="dossier" aria-label="Service overview" data-hero-dossier>
          <div class="dossier-head"><span><?= e(t('home.hero.mandate')) ?></span><span class="dossier-count"><?= count($mandates) ?> / <?= count($mandates) ?></span></div>
          <?php foreach ($mandates as $index => $mandate): ?>
            <a class="dossier-row" href="#service-<?= e($mandate['slug']) ?>" data-service-key="<?= e($mandate['slug']) ?>"><span class="dossier-icon"><svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="10"></circle><path d="M10 16h12M16 10v12"></path></svg></span><span class="dossier-label"><?= e(tf($mandate, 'custom_label')) ?></span><span class="dossier-chevron" aria-hidden="true">›</span></a>
          <?php endforeach; ?>
        </aside>
      </div>
      <p class="hero-footnote" aria-hidden="true"><span><?= e(t('home.hero.footnote')) ?></span><span class="hero-ring"></span></p>
    </section>

    <section class="metrics-strip"><div class="container"><div class="metrics-grid">
      <?php for ($i = 1; $i <= 3; $i++): $value = setting('metric_' . $i . '_value', '0'); ?>
        <div class="metric"><div class="metric-value"><span data-count-to="<?= e($value) ?>" data-suffix="<?= e(setting('metric_' . $i . '_suffix')) ?>"><?= e($value . setting('metric_' . $i . '_suffix')) ?></span></div><p class="metric-label"><?= e(setting_t('metric_' . $i . '_label')) ?></p></div>
      <?php endfor; ?>
    </div></div></section>

    <section class="service-finder" id="services"><div class="container"><h2 class="section-title"><?= e(setting_t('services_title', 'Our Services')) ?></h2><p class="section-intro"><?= e(setting_t('services_intro')) ?></p>
      <div data-finder class="finder"><div class="finder-tabs" role="tablist"></div><div class="finder-body"><div class="finder-grid"><div class="finder-main"><h3 data-finder-title><?= e(t('home.finder.placeholder')) ?></h3><p data-finder-blurb class="finder-blurb"></p><div class="finder-items" data-finder-items></div></div><div class="finder-sidebar"><div class="finder-cards" data-finder-cards></div></div></div><div class="finder-sectors"><p class="finder-sectors-label"><?= e(t('home.finder.relevant_sectors')) ?></p><div class="finder-sectors-list" data-finder-sectors></div></div></div></div>
    </div></section>

    <section class="lifecycle" id="delivery"><div class="container"><div class="lifecycle-inner"><div class="lifecycle-copy"><h2><?= e(setting_t('lifecycle_title')) ?></h2><p><?= e(setting_t('lifecycle_intro')) ?></p></div><ol class="lifecycle-steps">
      <?php foreach (['home.lifecycle.step1', 'home.lifecycle.step2', 'home.lifecycle.step3', 'home.lifecycle.step4', 'home.lifecycle.step5', 'home.lifecycle.step6'] as $index => $stepKey): ?><li><span class="step-node"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span><span class="step-label"><?= e(t($stepKey)) ?></span></li><?php endforeach; ?>
    </ol></div></div></section>

    <section class="sectors-section" id="sectors"><div class="container"><h2 class="section-title"><?= e(setting_t('sectors_title')) ?></h2><p class="section-intro"><?= e(setting_t('sectors_intro')) ?></p><div class="sectors-grid">
      <?php foreach ($sectors as $sector): ?><a class="sector-card" href="sector.php?slug=<?= urlencode($sector['slug']) ?>"><span class="sector-icon"><?= icon_svg($sector['icon']) ?></span><h3><?= e(tf($sector, 'name')) ?></h3><p><?= e(tf($sector, 'description')) ?></p></a><?php endforeach; ?>
    </div></div></section>

    <section class="video-section"><div class="container"><h2 class="section-title"><?= e(setting_t('videos_title')) ?></h2><div class="video-grid">
      <?php foreach ($videos as $video): ?><div class="video-card"><?= video_embed_html($video) ?><?php if (!empty($video['description'])): ?><p class="video-description"><?= e(tf($video, 'description')) ?></p><?php endif; ?></div><?php endforeach; ?>
    </div></div></section>

    <section class="experience-section" id="experience"><div class="container"><h2 class="section-title"><?= e(setting_t('experience_title')) ?></h2><p class="section-intro"><?= e(setting_t('experience_intro')) ?></p>
      <?php foreach ($projectsByClass as $class => $classProjects): ?><div class="exp-tier"><h3 class="exp-tier-title"><?= e($class) ?></h3><ul class="exp-list"><?php foreach ($classProjects as $project): $meta = implode(' · ', array_filter([tf($project, 'location'), tf($project, 'sector_name'), tf($project, 'client')])); ?><li class="exp-row"><span class="exp-scale"><?= e(tf($project, 'scale')) ?></span><span class="exp-detail"><a class="exp-name" href="project.php?slug=<?= urlencode($project['slug']) ?>"><?= e(tf($project, 'title')) ?></a><span class="exp-meta"><?= e($meta) ?></span></span></li><?php endforeach; ?></ul></div><?php endforeach; ?>
    </div></section>

    <section class="delivery-model" id="delivery-model"><div class="container"><h2 class="section-title"><?= e(setting_t('delivery_title')) ?></h2><p class="section-intro"><?= e(setting_t('delivery_intro')) ?></p><div class="model-grid">
      <div class="model-card"><h3><?= e(t('home.delivery.card1_title')) ?></h3><p><?= e(t('home.delivery.card1_body')) ?></p></div><div class="model-card"><h3><?= e(t('home.delivery.card2_title')) ?></h3><p><?= e(t('home.delivery.card2_body')) ?></p></div><div class="model-card"><h3><?= e(t('home.delivery.card3_title')) ?></h3><p><?= e(t('home.delivery.card3_body')) ?></p></div><div class="model-card"><h3><?= e(t('home.delivery.card4_title')) ?></h3><p><?= e(t('home.delivery.card4_body')) ?></p></div>
    </div></div></section>

    <section class="insights-section" id="insights"><div class="container"><h2 class="section-title"><?= e(setting_t('insights_title')) ?></h2><p class="section-intro"><?= e(setting_t('insights_intro')) ?></p>
      <?php foreach ($frontMessages as $message): ?><div class="public-message public-message--<?= e($message['type']) ?>"><?= e($message['message']) ?></div><?php endforeach; ?>
      <div class="insights-grid"><?php foreach ($insights as $insight): ?><article class="insight-card"><?php if ($insight['image_path']): ?><img class="insight-image" src="<?= e($insight['image_path']) ?>" alt=""><?php endif; ?><div class="insight-meta"><time datetime="<?= e(date('Y-m-d', strtotime($insight['published_at']))) ?>"><?= e(date('Y', strtotime($insight['published_at']))) ?></time><span class="category"><?= e(tf($insight, 'category')) ?></span></div><h3><?= e(tf($insight, 'title')) ?></h3><p><?= e(tf($insight, 'excerpt')) ?></p><a href="article.php?slug=<?= urlencode($insight['slug']) ?>" class="insight-link"><?= e(tf($insight, 'link_label')) ?> →</a></article><?php endforeach; ?><?php if (!$insights): ?><p class="insights-empty"><?= e(t('home.insights.empty')) ?></p><?php endif; ?></div>
      <div class="insights-cta"><p><?= e(t('home.insights.newsletter_prompt')) ?></p><form class="newsletter-form" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="subscribe"><input type="email" name="email" placeholder="<?= e(t('home.insights.email_placeholder')) ?>" autocomplete="email" required><button type="submit" class="btn btn-primary"><?= e(t('home.insights.subscribe')) ?></button></form></div>
    </div></section>

    <?php if ($testimonials): ?>
    <section class="testimonials-section" id="testimonials"><div class="container">
      <h2 class="section-title"><?= e(setting_t('testimonials_title', 'What Our Clients Say')) ?></h2>
      <?php if (setting_t('testimonials_intro')): ?><p class="section-intro"><?= e(setting_t('testimonials_intro')) ?></p><?php endif; ?>
      <div class="testimonials-grid">
        <?php foreach ($testimonials as $testimonial): ?>
          <blockquote class="testimonial-card">
            <?php if ($testimonial['logo_path']): ?><img class="testimonial-logo" src="<?= e($testimonial['logo_path']) ?>" alt="<?= e($testimonial['company']) ?>"><?php endif; ?>
            <p class="testimonial-quote">&ldquo;<?= e(tf($testimonial, 'quote')) ?>&rdquo;</p>
            <footer class="testimonial-author">
              <strong><?= e($testimonial['author_name']) ?></strong>
              <?php $role = trim(tf($testimonial, 'author_title')); $company = trim((string) $testimonial['company']); if ($role !== '' || $company !== ''): ?>
                <span><?= e(implode(', ', array_filter([$role, $company]))) ?></span>
              <?php endif; ?>
            </footer>
          </blockquote>
        <?php endforeach; ?>
      </div>
    </div></section>
    <?php endif; ?>

    <section class="cta-section"><div class="container"><div class="cta-content"><h2><?= e(setting_t('cta_title')) ?></h2><p><?= e(setting_t('cta_body')) ?></p><a href="<?= e(url('contact.php')) ?>" class="btn btn-primary btn-large"><?= e(t('home.cta.button')) ?></a></div></div></section>
  </main>

  <footer class="site-footer" id="contact"><div class="container"><div class="footer-grid"><div class="footer-section"><div class="footer-logo"><svg class="logo" viewBox="0 0 200 80" xmlns="http://www.w3.org/2000/svg"><image href="assets/brand/Website/Logo-Footer.svg" width="200" height="80"></image></svg></div><p><?= e(t('footer.tagline')) ?></p></div><div class="footer-section"><h4><?= e(t('footer.services')) ?></h4><ul><?php foreach (array_slice($services, 0, 6) as $service): ?><li><a href="#services"><?= e(tf($service, 'label')) ?></a></li><?php endforeach; ?></ul></div><?php if ($sectors): ?><div class="footer-section"><h4><?= e(t('footer.sectors')) ?></h4><ul><?php foreach ($sectors as $sector): ?><li><a href="<?= e(url('sector.php?slug=' . urlencode($sector['slug']))) ?>"><?= e(tf($sector, 'name')) ?></a></li><?php endforeach; ?></ul></div><?php endif; ?><div class="footer-section"><h4><?= e(t('footer.company')) ?></h4><ul><li><a href="<?= e(url('staff.php')) ?>"><?= e(t('nav.team')) ?></a></li><li><a href="<?= e(url('newsroom.php')) ?>"><?= e(t('nav.newsroom')) ?></a></li></ul></div><?php if ($offices): ?><div class="footer-section"><h4><?= e(t('footer.offices')) ?></h4><ul><?php foreach ($offices as $office): ?><li><?= e($office['name']) ?></li><?php endforeach; ?></ul></div><?php endif; ?><div class="footer-section"><h4><?= e(t('footer.connect')) ?></h4><ul><li><a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a></li><li><a href="tel:<?= e(preg_replace('/[^+0-9]/', '', setting('contact_phone'))) ?>"><?= e(setting('contact_phone')) ?></a></li><?php if ($linkedinUrl): ?><li><a href="<?= e($linkedinUrl) ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a></li><?php endif; ?><?php if ($twitterUrl): ?><li><a href="<?= e($twitterUrl) ?>" target="_blank" rel="noopener noreferrer">Twitter</a></li><?php endif; ?></ul></div></div><div class="footer-bottom"><p>&copy; <?= date('Y') ?> ACMIRS. <?= e(t('footer.rights')) ?></p><ul class="footer-legal"><li><a href="<?= e(url('privacy.php')) ?>"><?= e(t('footer.privacy')) ?></a></li><li><a href="<?= e(url('terms.php')) ?>"><?= e(t('footer.terms')) ?></a></li></ul></div></div></footer>

  <script type="application/json" id="service-data"><?= json_encode($serviceData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
  <script src="assets/js/hero-reveal.js"></script>
  <script src="<?= e(asset_url('assets/js/app.js')) ?>"></script>
</body>
</html>
