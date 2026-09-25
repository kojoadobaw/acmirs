<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

try {
    $services = fetch_all('SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order, id');
} catch (Throwable $exception) {
    redirect('install.php');
}

$sectorRows = fetch_all('SELECT ss.service_id, s.name FROM service_sectors ss JOIN sectors s ON s.id = ss.sector_id WHERE s.is_active = 1 ORDER BY s.sort_order, s.name');
$serviceSectors = [];
foreach ($sectorRows as $row) {
    $serviceSectors[(int) $row['service_id']][] = $row['name'];
}

$serviceData = [];
foreach ($services as $service) {
    $serviceData[] = [
        'key' => $service['slug'],
        'label' => $service['label'],
        'title' => $service['title'],
        'blurb' => $service['blurb'],
        'items' => json_decode($service['items_json'], true) ?: [],
        'cards' => [
            ['fill' => 'transparent', 'title' => 'Client Challenge', 'body' => $service['challenge']],
            ['fill' => 'linear-gradient(90deg, #b8763a 50%, transparent 50%)', 'title' => 'ACMIRS Response', 'body' => $service['response']],
            ['fill' => '#b8763a', 'title' => 'Your Outcome', 'body' => $service['outcome']],
        ],
        'sectors' => $serviceSectors[(int) $service['id']] ?? [],
    ];
}

$mandates = fetch_all('SELECT m.*, s.slug FROM mandates m JOIN services s ON s.id = m.service_id WHERE m.is_active = 1 AND s.is_active = 1 ORDER BY m.sort_order, m.id');
$sectors = fetch_all('SELECT * FROM sectors WHERE is_active = 1 ORDER BY sort_order, id');
$videos = fetch_all('SELECT * FROM videos WHERE is_active = 1 ORDER BY sort_order, id');
$projects = fetch_all("SELECT * FROM projects WHERE status = 'published' ORDER BY sort_order, id");
$insights = fetch_all("SELECT * FROM insights WHERE status = 'published' ORDER BY featured DESC, published_at DESC, id DESC LIMIT 6");

$projectsByClass = [];
foreach ($projects as $project) {
    $projectsByClass[$project['infrastructure_class']][] = $project;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'subscribe') {
    verify_csrf();
    $email = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
    if ($email) {
        $statement = db()->prepare('INSERT INTO newsletter_subscribers (email, is_active) VALUES (?, 1) ON DUPLICATE KEY UPDATE is_active = 1');
        $statement->execute([$email]);
        flash('success', 'Thank you. You are on the ACMIRS insights list.');
    } else {
        flash('error', 'Please enter a valid email address.');
    }
    redirect('#insights');
}

$frontMessages = pull_flashes();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="ACMIRS: Global Expertise to Drive Africa Infrastructure Solutions">
  <title>ACMIRS | Infrastructure Advisory</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/site.css">
  <link rel="stylesheet" href="assets/css/hero-reveal.css">
  <link rel="stylesheet" href="assets/css/cms-overrides.css">
  <link rel="icon" type="image/svg+xml" href="assets/brand/Website/Favicon.svg">
  <link rel="apple-touch-icon" href="assets/brand/Website/Apple-Touch-Icon.svg">
</head>
<body>
  <a href="#top" class="skip-link">Skip to content</a>
  <svg aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden"><defs><clipPath id="aRevealMask" clipPathUnits="objectBoundingBox"><path d="M0.5 0.0625 L0.96875 0.90625 L0.75 0.90625 L0.5 0.453125 L0.25 0.90625 L0.03125 0.90625 Z"></path><rect x="0.3125" y="0.703125" width="0.375" height="0.109375"></rect></clipPath></defs></svg>

  <header class="site-header">
    <a class="brand" href="#top" aria-label="ACMIRS — home"><img src="assets/brand/Website/Logo-Footer.svg" alt="ACMIRS"></a>
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" data-nav-toggle><span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span><span class="sr-only">Menu</span></button>
    <nav class="primary-nav" id="primary-nav" data-primary-nav>
      <a href="#services">Our Services</a><a href="#sectors">Sectors</a><a href="#experience">Experience</a><a href="../staff.html">Our Team</a><a href="#insights">Insights</a><a href="#contact">Contact</a><a class="nav-cta" href="#contact">Let's Talk</a>
    </nav>
  </header>

  <main>
    <section class="hero" id="top">
      <div class="hero-media" data-hero-media aria-hidden="true"><video autoplay muted loop playsinline preload="metadata" poster="assets/img/hero-golden-hour.jpg" data-hero-video data-src-hd="assets/img/hero.mp4"><source src="assets/img/hero-1280.mp4" type="video/mp4"><img src="assets/img/hero-golden-hour.jpg" alt=""></video></div>
      <div class="hero-mask" data-hero-mask aria-hidden="true"><video data-hero-mask-video muted loop playsinline preload="none"><source src="assets/img/hero-1280.mp4" type="video/mp4"></video></div>
      <div class="hero-wash hero-wash--side" data-hero-wash aria-hidden="true"></div><div class="hero-wash hero-wash--vertical" data-hero-wash aria-hidden="true"></div>
      <div class="hero-inner">
        <div class="hero-copy">
          <p class="eyebrow eyebrow--light" data-hero-eyebrow>Strategic Advisory · Timely Execution · Lasting Impact</p>
          <h1 data-hero-headline><span class="word-wrap"><span class="word">Global</span></span> <span class="word-wrap"><span class="word">Expertise</span></span> <span class="word-wrap"><span class="word">to</span></span> <span class="word-wrap"><span class="word">Drive</span></span> <span class="word-wrap"><span class="word">Africa</span></span> <span class="word-wrap"><span class="word">Infrastructure</span></span> <span class="word-wrap"><span class="word">Solutions</span></span></h1>
          <p class="hero-lead" data-hero-lead>ACMIRS partners with governments, DFIs and private capital to prepare, structure, finance and deliver infrastructure that unlocks growth and transforms lives across Africa.</p>
          <a class="underline-link underline-link--copper" href="#services" data-hero-lead>Learn more about our services <span aria-hidden="true">⟶</span></a>
          <div class="hero-actions" data-hero-actions><a class="btn btn--copper" href="#services">Explore Our Services</a><a class="btn btn--ghost" href="#experience">View Our Experience <span aria-hidden="true">⟶</span></a></div>
        </div>
        <aside class="dossier" aria-label="Service overview" data-hero-dossier>
          <div class="dossier-head"><span>The ACMIRS Mandate</span><span class="dossier-count"><?= count($mandates) ?> / <?= count($mandates) ?></span></div>
          <?php foreach ($mandates as $index => $mandate): ?>
            <a class="dossier-row" href="#service-<?= e($mandate['slug']) ?>" data-service-key="<?= e($mandate['slug']) ?>"><span class="dossier-icon"><svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="10"></circle><path d="M10 16h12M16 10v12"></path></svg></span><span class="dossier-label"><?= e($mandate['custom_label']) ?></span><span class="dossier-chevron" aria-hidden="true">›</span></a>
          <?php endforeach; ?>
        </aside>
      </div>
      <p class="hero-footnote" aria-hidden="true"><span>Global Expertise. African Experience.</span><span class="hero-ring"></span></p>
    </section>

    <section class="metrics-strip"><div class="container"><div class="metrics-grid">
      <?php for ($i = 1; $i <= 3; $i++): $value = setting('metric_' . $i . '_value', '0'); ?>
        <div class="metric"><div class="metric-value"><span data-count-to="<?= e($value) ?>" data-suffix="<?= e(setting('metric_' . $i . '_suffix')) ?>"><?= e($value . setting('metric_' . $i . '_suffix')) ?></span></div><p class="metric-label"><?= e(setting('metric_' . $i . '_label')) ?></p></div>
      <?php endfor; ?>
    </div></div></section>

    <section class="service-finder" id="services"><div class="container"><h2 class="section-title"><?= e(setting('services_title', 'Our Services')) ?></h2><p class="section-intro"><?= e(setting('services_intro')) ?></p>
      <div data-finder class="finder"><div class="finder-tabs" role="tablist"></div><div class="finder-body"><div class="finder-grid"><div class="finder-main"><h3 data-finder-title>Service</h3><p data-finder-blurb class="finder-blurb"></p><div class="finder-items" data-finder-items></div></div><div class="finder-sidebar"><div class="finder-cards" data-finder-cards></div></div></div><div class="finder-sectors"><p class="finder-sectors-label">Relevant sectors:</p><div class="finder-sectors-list" data-finder-sectors></div></div></div></div>
    </div></section>

    <section class="lifecycle" id="delivery"><div class="container"><div class="lifecycle-inner"><div class="lifecycle-copy"><h2><?= e(setting('lifecycle_title')) ?></h2><p><?= e(setting('lifecycle_intro')) ?></p></div><ol class="lifecycle-steps">
      <?php foreach (['Preparation','Structuring','Capital Raising','Transaction','Implementation','Operation'] as $index => $step): ?><li><span class="step-node"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span><span class="step-label"><?= e($step) ?></span></li><?php endforeach; ?>
    </ol></div></div></section>

    <section class="sectors-section" id="sectors"><div class="container"><h2 class="section-title"><?= e(setting('sectors_title')) ?></h2><p class="section-intro"><?= e(setting('sectors_intro')) ?></p><div class="sectors-grid">
      <?php foreach ($sectors as $sector): ?><div class="sector-card"><span class="sector-icon"><?= icon_svg($sector['icon']) ?></span><h3><?= e($sector['name']) ?></h3><p><?= e($sector['description']) ?></p></div><?php endforeach; ?>
    </div></div></section>

    <section class="video-section"><div class="container"><h2 class="section-title"><?= e(setting('videos_title')) ?></h2><div class="video-grid">
      <?php foreach ($videos as $video): ?><div class="video-card"><?= video_embed_html($video) ?><?php if (!empty($video['description'])): ?><p class="video-description"><?= e($video['description']) ?></p><?php endif; ?></div><?php endforeach; ?>
    </div></div></section>

    <section class="experience-section" id="experience"><div class="container"><h2 class="section-title"><?= e(setting('experience_title')) ?></h2><p class="section-intro"><?= e(setting('experience_intro')) ?></p>
      <?php foreach ($projectsByClass as $class => $classProjects): ?><div class="exp-tier"><h3 class="exp-tier-title"><?= e($class) ?></h3><ul class="exp-list"><?php foreach ($classProjects as $project): $meta = implode(' · ', array_filter([$project['location'], $project['sector_name'], $project['client']])); ?><li class="exp-row"><span class="exp-scale"><?= e($project['scale']) ?></span><span class="exp-detail"><a class="exp-name" href="project.php?slug=<?= urlencode($project['slug']) ?>"><?= e($project['title']) ?></a><span class="exp-meta"><?= e($meta) ?></span></span></li><?php endforeach; ?></ul></div><?php endforeach; ?>
    </div></section>

    <section class="delivery-model" id="delivery-model"><div class="container"><h2 class="section-title"><?= e(setting('delivery_title')) ?></h2><p class="section-intro"><?= e(setting('delivery_intro')) ?></p><div class="model-grid">
      <div class="model-card"><h3>Local Presence</h3><p>Teams embedded in key markets across Africa with deep government and private sector relationships</p></div><div class="model-card"><h3>Global Reach</h3><p>International experience and connections across New York, London, Beijing and Cape Town</p></div><div class="model-card"><h3>Cross-Sector</h3><p>Multidisciplinary experts spanning finance, law, engineering, social and environmental disciplines</p></div><div class="model-card"><h3>Transaction Leadership</h3><p>Hands-on support from origination through close and beyond</p></div>
    </div></div></section>

    <section class="insights-section" id="insights"><div class="container"><h2 class="section-title"><?= e(setting('insights_title')) ?></h2><p class="section-intro"><?= e(setting('insights_intro')) ?></p>
      <?php foreach ($frontMessages as $message): ?><div class="public-message public-message--<?= e($message['type']) ?>"><?= e($message['message']) ?></div><?php endforeach; ?>
      <div class="insights-grid"><?php foreach ($insights as $insight): ?><article class="insight-card"><?php if ($insight['image_path']): ?><img class="insight-image" src="<?= e($insight['image_path']) ?>" alt=""><?php endif; ?><div class="insight-meta"><time datetime="<?= e(date('Y-m-d', strtotime($insight['published_at']))) ?>"><?= e(date('Y', strtotime($insight['published_at']))) ?></time><span class="category"><?= e($insight['category']) ?></span></div><h3><?= e($insight['title']) ?></h3><p><?= e($insight['excerpt']) ?></p><a href="article.php?slug=<?= urlencode($insight['slug']) ?>" class="insight-link"><?= e($insight['link_label']) ?> →</a></article><?php endforeach; ?><?php if (!$insights): ?><p class="insights-empty">No published insights are available yet.</p><?php endif; ?></div>
      <div class="insights-cta"><p>Get curated insights delivered to your inbox</p><form class="newsletter-form" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="subscribe"><input type="email" name="email" placeholder="Your email" autocomplete="email" required><button type="submit" class="btn btn-primary">Subscribe</button></form></div>
    </div></section>

    <section class="cta-section"><div class="container"><div class="cta-content"><h2><?= e(setting('cta_title')) ?></h2><p><?= e(setting('cta_body')) ?></p><a href="#contact" class="btn btn-primary btn-large">Start a Conversation</a></div></div></section>
  </main>

  <footer class="site-footer" id="contact"><div class="container"><div class="footer-grid"><div class="footer-section"><div class="footer-logo"><svg class="logo" viewBox="0 0 200 80" xmlns="http://www.w3.org/2000/svg"><image href="assets/brand/Website/Logo-Footer.svg" width="200" height="80"></image></svg></div><p>Global expertise to drive Africa's infrastructure solutions.</p></div><div class="footer-section"><h4>Services</h4><ul><?php foreach (array_slice($services, 0, 6) as $service): ?><li><a href="#services"><?= e($service['label']) ?></a></li><?php endforeach; ?></ul></div><div class="footer-section"><h4>Offices</h4><ul><li>New York</li><li>London</li><li>Beijing</li><li>Cape Town</li></ul></div><div class="footer-section"><h4>Connect</h4><ul><li><a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a></li><li><a href="tel:<?= e(preg_replace('/[^+0-9]/', '', setting('contact_phone'))) ?>"><?= e(setting('contact_phone')) ?></a></li><li><a href="#">LinkedIn</a></li><li><a href="#">Twitter</a></li></ul></div></div><div class="footer-bottom"><p>&copy; <?= date('Y') ?> ACMIRS. All rights reserved.</p><ul class="footer-legal"><li><a href="#">Privacy</a></li><li><a href="#">Terms</a></li><li><a href="#">Accessibility</a></li></ul></div></div></footer>

  <script type="application/json" id="service-data"><?= json_encode($serviceData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
  <script src="assets/js/hero-reveal.js"></script>
  <script src="assets/js/app.js"></script>
</body>
</html>
